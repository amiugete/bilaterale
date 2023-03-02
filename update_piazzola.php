<?php 

session_start();
#require_once('./req.php');



if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
} else {
    require_once ('./conn.php');
}




$id_piazzola=$_POST['id_piazzola'];

echo $id_piazzola."<br>";


$civ=$_POST['civ'];

if (!$civ){
    $civ=NULL;
}
echo $civ."<br>";

$rif=$_POST['rif'];
echo $rif."<br>";


$note=$_POST['note'];
if (!$note){
    $note=NULL;
}

echo $note."<br>";

/*if ($_POST['privato'] == 'privato'){
    $privato=1;
} else {
    $privato=0;
}*/
$privato=$_POST['privato'];

echo $privato."<br>";




$query_1="UPDATE elem.piazzole
SET riferimento = $1, numero_civico = $2, 
note =$3, suolo_privato = $4 where id_piazzola = $5";


$result4 = pg_prepare($conn, "my_query1", $query_1);
//$result4 = pg_execute($conn, "my_query4", array($rif, $testo_civ, $id_asta, $note, $privato, $id_transitabilita, $new_id, $lon, $lat));
$result1 = pg_execute($conn, "my_query1", array($rif, $testo_civ, $note, $privato, $id_piazzola));
$status1= pg_result_status($result1);
echo "Status1=".$status1."<br>";



//header('Location: piazzola.php?piazzola='.$id_piazzola.'');

?>