<?php 

session_start();
#require_once('./req.php');



if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
    $titolo_test=' (ambiente di TEST)';
} else {
    require_once ('./conn.php');
    $titolo_test='';
}

$id_piazzola=$_POST['piazzola'];

//echo $id_piazzola."<br>";




$indi= $_POST['indi'];
$indi_st=$_POST['indi_st'];

//echo $indi."<br>";
//echo $indi_st."<br>";


$carta= $_POST['carta'];
$carta_st=$_POST['carta_st'];

//echo $carta."<br>";
//echo $carta_st."<br>";

$multi= $_POST['multi'];
$multi_st=$_POST['multi_st'];

//echo $multi."<br>";
//echo $multi_st."<br>";


$org= $_POST['org'];
$org_st=$_POST['org_st'];

//echo $org."<br>";
//echo $org_st."<br>";

#exit;
# ciclo su elementi da eliminare

$query_1="select id_elemento 
	from elem.elementi e 
	where id_piazzola= $1 
	and 
	tipo_elemento in (select tipo_elemento from elem.tipi_elemento te 
	where te.tipologia_elemento in ('L', 'P', 'C') and te.tipo_rifiuto in (1,3,4,5,7))
	and 
	id_elemento not in (
	select elemento_id  from gestione_oggetti.v_intervento vi where stato = 5
	)";
$result1 = pg_prepare($conn, "my_query_e", $query_1);
$result1 = pg_execute($conn, "my_query_e", array($id_piazzola));

$status1= pg_result_status($result1);
//echo "Status1=".$status1."<br>";
    
while($r1 = pg_fetch_assoc($result1)) {
    echo "Elemento da eliminare:".$r1['id_elemento']."<br>";
    # cerco interventi da eliminare

    $query_i1="select id from gestione_oggetti.v_intervento vi 
	where stato = 1 and elemento_id = $1";

    $result_i = pg_prepare($conn, "my_query_i1", $query_i1);
    $result_i = pg_execute($conn, "my_query_i1", array($r1['id_elemento']));

        
    while($r_i = pg_fetch_assoc($result_i)) {

        # scrivo nella tabella intervento_tipo_stato_intervento
        $query_i2="INSERT INTO gestione_oggetti.intervento_tipo_stato_intervento
        (tipo_stato_intervento_id, intervento_id, data_ora)
        VALUES(2, $1, now());";
        $result_i2 = pg_prepare($conn, "my_query_i2", $query_i2);
        $result_i2 = pg_execute($conn, "my_query_i2", array($r_i['id']));

      
        # inserisco note di chiusura
        $query_i3="UPDATE gestione_oggetti.intervento
        SET note_chiusura='Intervento non più necessario per trasformazione piazzola a bilaterale'
        WHERE id=$1;";
        $result_i3 = pg_prepare($conn, "my_query_i3", $query_i3);
        $result_i3 = pg_execute($conn, "my_query_i3", array($r_i['id']));


        # scrivo nella tabella email
        $query_i4="INSERT INTO gestione_oggetti.email
        (tipo_mail, intervento_id, data_creazione)
        VALUES('Abortito', $1, now()::date);";
        $result_i4 = pg_prepare($conn, "my_query_i4", $query_i4);
        $result_i4 = pg_execute($conn, "my_query_i4", array($r_i['id']));

    } # fine ciclo su interventi


    # Cerco i percorsi con gli elementi da eliminare ed elimino
    # questa parte si potrebbe spostare su trigger


    $query_p1="select eap.id_asta_percorso, eap.id_elemento, ap.id_percorso, te.descrizione 
	from elem.elementi_aste_percorso eap 
	join elem.aste_percorso ap on ap.id_asta_percorso = eap.id_asta_percorso 
	join elem.elementi e on e.id_elemento = eap.id_elemento 
	join elem.tipi_elemento te on te.tipo_elemento = e.tipo_elemento 
	where eap.id_elemento = $1";

    $result_p = pg_prepare($conn, "my_query_p1", $query_p1);
    $result_p = pg_execute($conn, "my_query_p1", array($r1['id_elemento']));




    while($r_p = pg_fetch_assoc($result_p)) {
        //echo "Asta elemento:" . $r_p['id_asta_percorso']."<br>";

        $query_p2 = "DELETE FROM elem.elementi_aste_percorso
WHERE id_asta_percorso=$1 AND id_elemento=$2;";

        $result_p2 = pg_prepare($conn, "my_query_p2", $query_p2);
        $result_p2 = pg_execute($conn, "my_query_p2", array($r_p['id_asta_percorso'], $r1['id_elemento']));
        $status_p2= pg_result_status($result_p2);

            
        echo "status_p2=".$status_p2."<br>";

        $des='Eliminati 1 '.$r_p['descrizione'].' da piazzola '.$id_piazzola .'';

        # scrivo history per variazioni
        # questa parte si potrebbe spostare su trigger
        $query_p3 = "INSERT INTO util.sys_history
        (type, action, description, datetime,
         id_user, id_piazzola, id_percorso, id_elemento)
        VALUES ('PERCORSO', 'UPDATE_ELEM', $1 ,
        now(), $2, $3, $4, $5);";

        echo $des."<br>";
        echo "id user:".$_SESSION['id_user']."<br>";
        echo "id piazzola:".$id_piazzola."<br>";
        echo "id percorso:".$r_p['id_percorso']."<br>";
        echo "id elemento:".$r1['id_elemento']."<br>";
        echo $query_p3."<br>";



        $result_p3 = pg_prepare($conn, "my_query_p3", $query_p3);
        $result_p3 = pg_execute($conn, "my_query_p3", array($des, $_SESSION['id_user'], $id_piazzola, $r_p['id_percorso'], $r1['id_elemento']));
        $status_p3= pg_result_status($result_p3);

        echo "status_p3=".$status_p3."<br>";

    }



    


    # elimino elemento
    $query_elimina='DELETE FROM elem.elementi
    WHERE id_elemento=$1;';
    $result_elimina = pg_prepare($conn, "query_elimina", $query_elimina);
    $result_elimina = pg_execute($conn, "query_elimina", array($r1['id_elemento']));

    $status_elimina= pg_result_status($result_elimina);

    echo "status_elimina=".$status_elimina."<br>";
    





} # fine ciclo su elementi da eliminare


echo "Ho eliminato tutto<br>"; 
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



//****************************************************************************
//			Invio mail
//****************************************************************************

require_once('invio_mail_general.php');

if ($_SESSION['username']!='Marzocchi' && $_SESSION['username'] != 'Magioncalda'){
// In questo momento il pezzo sopra non serve.. più semplice indirizzo fisso
    $mails=array('roberto.marzocchi@amiu.genova.it');
} else {
    $mails=array('vobbo@libero.it','roberto.marzocchi@amiu.genova.it');
}


while (list ($key, $val) = each ($mails)) {
  $mail->AddAddress($val);
}
//Set the subject line
$mail->Subject = 'Piazzola bilateralizzata attraverso l\'applicativo per il passaggio al bilaterale.';
//$mail->Subject = 'PHPMailer SMTP without auth test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$body =  'Piazzola: '.$id_piazzola.' bilateralizzata <br><br>'.$testo_mail.'

 <br> <br> '.$titolo_app.'';
  
require('./informativa_privacy_mail.php');

$mail-> Body=$body ;

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
    header("refresh:10;url=./piazzola.php?piazzola=".$id_piazzola."");
} else {
    echo "Message sent!";
	//header("location: ./piazzola.php?piazzola=".$id_piazzola);
}
//exit;
//header("location: ../dettagli_incarico.php?id=".$id);


# questa parte è da rivedere, bisogna usare jquery
#header("location:javascript://history.go(-1)");

?>