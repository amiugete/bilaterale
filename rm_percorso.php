<?php
session_set_cookie_params($lifetime);
session_start();

//require_once('./funzioni_jwt.php');
echo $lifetime."<br>";
echo $_SESSION['test']."<br>";
//exit;

if ($_SESSION['test']==1) {
    require_once('./conn_test.php');
} else {
    require_once('./conn.php');
}

$id_piazzola=$_POST['piazzola'];

$id_elemento=$_POST['elemento'];
echo $id_elemento."<br>";

$id_percorso=$_POST['percorso'];
echo $id_percorso."<br>";





$cerco_asta_percorso='select id_asta_percorso, num_seq, upper(tipo) as tipo 
from elem.aste_percorso ap where id_asta = (
	select id_asta from elem.piazzole where id_piazzola = (
		select id_piazzola from elem.elementi e where id_elemento = $1
	)
) and id_percorso = $2';

$result_p = pg_prepare($conn, "cerco_asta_percorso", $cerco_asta_percorso);
$result_p = pg_execute($conn, "cerco_asta_percorso", array($id_elemento, $id_percorso));
$status1= pg_result_status($result_p);
echo "Stato query = ".$status1."<br>";
$check_asta_esistente=0;
while($rp = pg_fetch_assoc($result_p)) {
    $check_asta_esistente=1;
    if ($rp['tipo'] =='SERVIZIO'){
        // update tipo asta_percorso
        $query_update_tipo="UPDATE elem.aste_percorso SET tipo='trasferimento'
        WHERE id_asta_percorso=$1";
        $result_p = pg_prepare($conn, "query_update_tipo", $query_update_tipo);
        $result_p = pg_execute($conn, "query_update_tipo", array($rp['id_asta_percorso']));
    }
    // per successivo insert elemento asta percorso mi salvo id_asta_percorso
    $nap=$rp['id_asta_percorso'];
    echo "L'asta percorso già c'è ed è quella con id = ".$nap."<br>";

    $descrizione_operazione='Modificata asta '. $nap.' del percorso '. $id_percorso.' da servizio a trasferimento';
    
    $query_history1= "INSERT INTO util.sys_history
    (type, action, description,
     datetime, id_user, id_percorso, id_elemento)
    VALUES('ASTA PERCORSO', 'UPDATE', $1,
    CURRENT_TIMESTAMP, $2, $3, $4)";
    $result_i2 = pg_prepare($conn, "query_history1", $query_history1);
    $result_i2 = pg_execute($conn, "query_history1", array($descrizione_operazione, $_SESSION['id_user'], $id_percorso, $nap));
    $status2= pg_result_status($result_i2);


}
if ($check_asta_esistente==0){
    echo "NON HO TROVATO L'ASTA e QUESTO E' PROBLEMA!";
    
    exit;

}

    


// ora rimuovo dal percorso tutti gli elementi dello stesso tipo

$query_select_elementi="SELECT id_elemento from elem.elementi 
where id_piazzola = $1 and 
tipo_elemento = (select tipo_elemento from elem.elementi e where id_elemento= $2)";
$result_se = pg_prepare($conn, "query_select_elementi", $query_select_elementi);
$result_se = pg_execute($conn, "query_select_elementi", array( $id_piazzola, $id_elemento));
while($rse = pg_fetch_assoc($result_se)) {
    // faccio insert nella tabella elemeti_aste_percorsi
    $query_delete_elementi="DELETE FROM elem.elementi_aste_percorso
    WHERE id_asta_percorso=$1 and id_elemento=$2";
    $result_ie = pg_prepare($conn, "query_delete_elementi", $query_delete_elementi);
    $result_ie = pg_execute($conn, "query_delete_elementi", array($nap, $rse['id_elemento']));
    $status2= pg_result_status($result_ie);
    echo "Stato query 4 = ".$status2."<br>";

    $descrizione_operazione2='Rimosso elemento '. $rse['id_elemento'].' da piazzola '.$id_piazzola.' dal percorso '. $id_percorso.'';
    $query_history2= "INSERT INTO util.sys_history
    (type, action, description,
     datetime, id_user, id_percorso, id_elemento, id_piazzola)
    VALUES('PERCORSO', 'UPDATE_ELEM', $1,
    CURRENT_TIMESTAMP, $2, $3, $4, $5)";
    $result_ie2 = pg_prepare($conn, "query_history2", $query_history2);
    $result_ie2 = pg_execute($conn, "query_history2", array($descrizione_operazione2, $_SESSION['id_user'], $id_percorso, $id_elemento, $id_piazzola));
    $status2= pg_result_status($result_ie2);


}
$query_pulizia='select  etl.pulizia_trasferimenti()';
$result = pg_prepare($conn, "query_pulizia", $query_pulizia);
$result = pg_execute($conn, "query_pulizia", array());

//exit;
header("location: ./piazzola.php?piazzola=".$id_piazzola);


?>