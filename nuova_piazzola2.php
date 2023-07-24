<?php 

session_start();
#require_once('./req.php');



if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
} else {
    require_once ('./conn.php');
}






// id via
$id_via=explode('|',$_POST['codvia'])[0];
echo $id_via."<br>";

$civ=explode('|',$_POST['id_civico'])[0];

echo $civ."<br>";

$rif=$_POST['rif'];

echo $rif."<br>";


$note=$_POST['note'];
if (!$note){
    $note=NULL;
}

echo $note."<br>";

if ($_POST['privato'] == 'privato'){
    $privato=1;
} else {
    $privato=0;
}


echo $privato."<br>";



$lat=floatval($_POST['lat']);

echo $lat."<br>";

$lon=floatval($_POST['lon']);

echo $lon."<br>";


$indi= $_POST['indi'];
$indi_st=$_POST['indi_st'];

echo $indi."<br>";
echo $indi_st."<br>";


$carta= $_POST['carta'];
$carta_st=$_POST['carta_st'];

echo $carta."<br>";
echo $carta_st."<br>";

$multi= $_POST['multi'];
$multi_st=$_POST['multi_st'];

echo $multi."<br>";
echo $multi_st."<br>";


$org= $_POST['org'];
$org_st=$_POST['org_st'];

echo $org."<br>";
echo $org_st."<br>";


# ciclo su elementi da eliminare

echo $civ ."<br>";

# cerco il numero civico da scrivere nella piazzola
$query_0="SELECT nome FROM topo.vie v WHERE id_via = $1";
$result0 = pg_prepare($conn, "my_query0", $query_0);
$result0 = pg_execute($conn, "my_query0", array($id_via,));

$status0= pg_result_status($result0);
echo "Status0=".$status0."<br>";
    
while($r0 = pg_fetch_assoc($result0)) {
    $nome_via=$r0['nome'];
}

echo 'Nome via: '.$nome_via."<br>";

# cerco il numero civico da scrivere nella piazzola
$query_2="SELECT testo FROM etl.civici_comune cc WHERE cod_civico = $1";
$result2 = pg_prepare($conn, "my_query2", $query_2);
$result2 = pg_execute($conn, "my_query2", array($civ,));

$status2= pg_result_status($result2);
echo "Status2=".$status2."<br>";
    
while($r2 = pg_fetch_assoc($result2)) {
    $testo_civ=$r2['testo'];
}

echo 'Testo civico: '.$testo_civ."<br>";


# cerco l'asta più vicina 
$query_3= "SELECT id_asta, id_transitabilita
from geo.v_grafostradale vg 
where id_via = $1 
order by st_distance(ST_SetSRID(ST_MakePoint($2, $3),4326)::geography, st_transform(geoloc,4326)::geography)
limit 1;";

$result3 = pg_prepare($conn, "my_query3", $query_3);
$result3 = pg_execute($conn, "my_query3", array($id_via, $lon, $lat));

$status3= pg_result_status($result3);
echo "Status3=".$status3."<br>";
    
while($r3 = pg_fetch_assoc($result3)) {
    $id_asta=intval($r3['id_asta']);
    $id_transitabilita=intval($r3['id_transitabilita']);
}

echo 'ID asta: '.$id_asta."<br>";


# cerco ID nuova piazzola
$query_1=" select max(id_piazzola)+1 as new_id from elem.piazzole p";
$result1 = pg_prepare($conn, "my_query1", $query_1);
$result1 = pg_execute($conn, "my_query1", array());

$status1= pg_result_status($result1);
echo "Status1=".$status1."<br>";
    
while($r1 = pg_fetch_assoc($result1)) {
    $new_id=intval($r1['new_id']);
}


echo 'New ID: '.$new_id."<br>";
//exit;


$query_4="INSERT INTO elem.piazzole
(riferimento, numero_civico, id_asta,
 note, suolo_privato,  foto, ecopunto, lato, riportata_prod, id_transitabilita)
VALUES ($1, $2, $3, $4, $5,  0, 0, 1, 0, $6)";


$result4 = pg_prepare($conn, "my_query4", $query_4);
//$result4 = pg_execute($conn, "my_query4", array($rif, $testo_civ, $id_asta, $note, $privato, $id_transitabilita, $new_id, $lon, $lat));
$result4 = pg_execute($conn, "my_query4", array($rif, $testo_civ, $id_asta, $note, $privato, $id_transitabilita));
$status4= pg_result_status($result4);
echo "Status4=".$status4."<br>";


$query_5="INSERT INTO geo.piazzola
(id, geoloc, coord_lat, coord_long)
VALUES ($1, st_transform(ST_SetSRID(ST_MakePoint($2, $3),4326),3003), $3, $2);";
$result4 = pg_prepare($conn, "my_query5", $query_5);
$result5 = pg_execute($conn, "my_query5", array( $new_id, $lon, $lat));
$status5= pg_result_status($result5);
echo "Status5=".$status5."<br>";


# questo sarebbe inutile ma è per lasciare intatto il codice
$id_piazzola= $new_id;


# creo elementi bilaterali
#exit;

$query_insert="INSERT INTO elem.elementi
(tipo_elemento, 
id_piazzola, 
id_asta, 
x_id_cliente, privato, peso_reale, peso_stimato, 
id_utenza,  
percent_riempimento, freq_stimata, 
data_inserimento, 
id_materiale)
VALUES($1, 
$2, (select id_asta from elem.piazzole p where id_piazzola =$3),
'-1'::integer, 0, 0, 0,
'-1'::integer,
90, 3, now(), 0);";



# inserisco indifferenziati

if ($indi_st==1){
    $te=183;
} else if ($indi_st==0){
    $te=184;
} else{
    die('ATTENZIONE - Problema con inserimento indifferenziati');
}

$i=0;
while ($i< $indi){
    $result_insert = pg_prepare($conn, "query_insert_i", $query_insert);
    $result_insert = pg_execute($conn, "query_insert_i", array($te, $id_piazzola, $id_piazzola));
    $i=$i+1;
}


# inserisco carta
if ($carta_st==1){
    $te=187;
} else if ($carta_st==0){
    $te=188;
} else{
    die('ATTENZIONE - Problema con inserimento carta');
}

$i=0;
while ($i< $carta){
    $result_insert = pg_prepare($conn, "query_insert_c", $query_insert);
    $result_insert = pg_execute($conn, "query_insert_c", array($te, $id_piazzola, $id_piazzola));
    $i=$i+1;
}



# inserisco multi
if ($multi_st==1){
    $te=189;
} else if ($multi_st==0){
    $te=190;
}

$i=0;
while ($i< $multi){
    $result_insert = pg_prepare($conn, "query_insert_m", $query_insert);
    $result_insert = pg_execute($conn, "query_insert_m", array($te, $id_piazzola, $id_piazzola));
    $i=$i+1;
}



# inserisco org
if ($org_st==1){
    $te=185;
} else if ($org_st==0){
    $te=186;
}

$i=0;
while ($i< $org){
    $result_insert = pg_prepare($conn, "query_insert_o", $query_insert);
    $result_insert = pg_execute($conn, "query_insert_o", array($te, $id_piazzola, $id_piazzola));
    $i=$i+1;
}

// Aggiungo il riordino piazzola
$result_insert = pg_prepare($conn, "query_insert_o", $query_insert);
$result_insert = pg_execute($conn, "query_insert_o", array(180, $id_piazzola, $id_piazzola));

// inserisco di default anche un riordino piazzola


# questa parte è da rivedere, bisogna usare jquery
#header("location:javascript://history.go(-1)");



//****************************************************************************
//			Invio mail
//****************************************************************************

require_once('invio_mail_general.php');



$mails=array('roberto.marzocchi@amiu.genova.it', 'assterritorio@amiu.genova.it', 'marco.zamboni@ideabs.com');


echo "fino a qua 1 ";

while (list ($key, $val) = each ($mails)) {
  $mail->AddAddress($val);
}

echo "fino a qua 2";

//Set the subject line
$mail->Subject = 'Nuova piazzola creata';
//$mail->Subject = 'PHPMailer SMTP without auth test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$body =  'Nuova piazzola bilaterale creata:<br>
<b>'.$id_piazzola.'</b> - '.$nome_via.' '.$testo_civ.' - ' .$rif.'  <br><br>'.$testo_mail.'

 <br> <br> '.$titolo_app.'';
  
require('./informativa_privacy_mail.php');

$mail-> Body=$body ;

echo "fino a qua";

//$mail->Body =  'Corpo del messaggio';
//$mail->msgHTML(file_get_contents('E\' arrivato un nuovo incarico da parte del Comune di Genova. Visualizza lo stato dell\'incarico al seguente link e aggiornalo quanto prima. <br> Ti chiediamo di non rispondere a questa mail'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
//echo "<br>OK 2<br>";
if (!$mail->send()) {
    echo "<h3>Problema nell'invio della mail: " . $mail->ErrorInfo;
	?>
	<script> //alert(<?php echo "Problema nell'invio della mail: " . $mail->ErrorInfo;?>) </script>
	<?php
	//echo '<br>La comunicazione è stata correttamente inserita a sistema, ma si è riscontrato un problema nell\'invio della mail.';
	echo '<div style="text-align: center;"><img src="../../img/no_mail_com.png" width="75%" alt=""></div>';
	echo '<br>Entro 10" verrai re-indirizzato alla pagina precedente, clicca al seguente ';
	echo '<a href="./piazzola.php?piazzola='.$id_piazzola.'">link</a> per saltare l\'attesa.</h3>' ;
	//sleep(30);
    //header("refresh:10;url=./piazzola.php?piazzola=".$id_piazzola."");
} else {
    echo "Message sent!";
	//header("location: ./piazzola.php?piazzola=".$id_piazzola);
}
//exit;
//header("location: ../dettagli_incarico.php?id=".$id);


# questa parte è da rivedere, bisogna usare jquery
#header("location:javascript://history.go(-1)");



header('Location: piazzola.php?piazzola='.$id_piazzola.'');
?>