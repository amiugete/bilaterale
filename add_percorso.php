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
    if ($rp['tipo'] !='SERVIZIO'){
        // update tipo asta_percorso
        $query_update_tipo="UPDATE elem.aste_percorso SET tipo='servizio'
        WHERE id_asta_percorso=$1";
        $result_p = pg_prepare($conn, "query_update_tipo", $query_update_tipo);
        $result_p = pg_execute($conn, "query_update_tipo", array($rp['id_asta_percorso']));
    }
    // per successivo insert elemento asta percorso mi salvo id_asta_percorso
    $nap=$rp['id_asta_percorso'];
    echo "L'asta percorso già c'è ed è quella con id = ".$nap."<br>";

}
if ($check_asta_esistente==0){
    //$id_asta_percorso='select max(id_asta_percorso)+1 as nap from elem.aste_percorso ap where id_percorso=$1';
    //$result_p = pg_prepare($conn, "id_asta_percorso", $id_asta_percorso);
    //$result_p = pg_execute($conn, "id_asta_percorso", array($id_percorso));
    $id_asta_percorso='select max(id_asta_percorso)+1 as nap from elem.aste_percorso ap';
    $result_p = pg_prepare($conn, "id_asta_percorso", $id_asta_percorso);
    $result_p = pg_execute($conn, "id_asta_percorso", array());
    while($rp = pg_fetch_assoc($result_p)) {
        // new asta percorso
        $nap=$rp['nap'];
        echo "La nuova asta percorso avrà  id = ".$nap."<br>" ;
    }


    $query_metri_trasf="select distinct
        (select max(metri_trasf) from elem.aste_percorso where id_percorso=$1) +
        round(st_distance(
        (
        select g.geoloc from geo.grafostradale g where id =( 
            select id_asta from elem.aste_percorso ap2 where 
            id_percorso=$1
            and num_seq = (select max(num_seq) from elem.aste_percorso ap3 where id_percorso=$1)
        )
        ), 
        (select g.geoloc from geo.grafostradale g where id =
            (select id_asta from elem.piazzole where id_piazzola=$2)
        )
        )::numeric,2) as metri_trasform from topo.comuni";
    $result_mt = pg_prepare($conn, "query_metri_trasf", $query_metri_trasf);
    
    $result_mt = pg_execute($conn, "query_metri_trasf", array($id_percorso, $id_piazzola));
    $status2= pg_result_status($result_mt);
    echo "Stato query 2 = ".$status2."<br>";
    while($rmt = pg_fetch_assoc($result_mt)) {
        $r_m_t=$rmt['metri_trasform'];
        echo count($rmt).'<br>';
        echo $rmt['metri_trasform'].'<br>';
        
    }
    /*while($rmt = pg_fetch_row($result_mt)) {
        echo $rmt.'<br>';
    }*/
    echo 'Metri_trasf='. $r_m_t .'<br>';

    echo 'Tempo_trasf='.($r_m_t/6.944).'<br>';

    //exit;


    echo "Nap = ".$nap."<br>";
    echo "id_percorso = ".$id_percorso."<br>";
    echo "id_piazzola = ".$id_piazzola."<br>";
    echo "r_m_t = ".$r_m_t."<br>";
    //exit;
    $insert_asta_percorso="INSERT INTO elem.aste_percorso
    (id_asta_percorso, num_seq,
     id_asta, lato_servizio, tipo,
     frequenza, carico_scarico, id_percorso,
     metri_trasf, tempo_trasf, senso_perc, lung_trattamento)
    VALUES($1, (select max(num_seq)+1 from elem.aste_percorso where id_percorso=$2), 
    (select id_asta from elem.piazzole where id_piazzola=$3), 'entrambi', 'servizio',
    (select frequenza from elem.percorsi where id_percorso=$2),0, $2,
     $4, ($4/6.944), -1, 0)";
    $result_i = pg_prepare($conn, "insert_asta_percorso", $insert_asta_percorso);
    $result_i = pg_execute($conn, "insert_asta_percorso", array($nap, $id_percorso, $id_piazzola, $r_m_t));
    $status2= pg_result_status($result_i);
    echo "Stato query 3 = ".$status2."<br>";

    $descrizione_operazione='Aggiunta asta '. $nap.' al percorso '. $id_percorso.'';
    
    $query_history1= "INSERT INTO util.sys_history
    (type, action, description,
     datetime, id_user, id_percorso, id_elemento)
    VALUES('ASTA PERCORSO', 'INSERT', $1,
    CURRENT_TIMESTAMP, $2, $3, $4)";
    $result_i2 = pg_prepare($conn, "query_history1", $query_history1);
    $result_i2 = pg_execute($conn, "query_history1", array($descrizione_operazione, $_SESSION['id_user'], $id_percorso, $nap));
    $status2= pg_result_status($result_i2);
    
}

// ora inserisco nel percorso tutti gli elementi dello stesso tipo

$query_select_elementi="SELECT id_elemento from elem.elementi 
where id_piazzola = $1 and 
tipo_elemento = (select tipo_elemento from elem.elementi e where id_elemento= $2)";
$result_se = pg_prepare($conn, "query_select_elementi", $query_select_elementi);
$result_se = pg_execute($conn, "query_select_elementi", array( $id_piazzola, $id_elemento));
while($rse = pg_fetch_assoc($result_se)) {
    // faccio insert nella tabella elemeti_aste_percorsi
    $query_insert_elementi="INSERT INTO elem.elementi_aste_percorso
    (id_asta_percorso, id_elemento,
    frequenza,
    ripasso)
    VALUES($1, $2, 
    (select frequenza from elem.percorsi where id_percorso=$3)::text,
     0)";
    $result_ie = pg_prepare($conn, "query_insert_elementi", $query_insert_elementi);
    $result_ie = pg_execute($conn, "query_insert_elementi", array( $nap, $rse['id_elemento'],$id_percorso));
    $status2= pg_result_status($result_ie);
    echo "Stato query 4 = ".$status2."<br>";

    $descrizione_operazione2='Aggiunta elemento '. $rse['id_elemento'].' da piazzola '.$id_piazzola.' al percorso '. $id_percorso.'';
    $query_history2= "INSERT INTO util.sys_history
    (type, action, description,
     datetime, id_user, id_percorso, id_elemento, id_piazzola)
    VALUES('PERCORSO', 'UPDATE_ELEM', $1,
    CURRENT_TIMESTAMP, $2, $3, $4, $5)";
    $result_ie2 = pg_prepare($conn, "query_history2", $query_history2);
    $result_ie2 = pg_execute($conn, "query_history2", array($descrizione_operazione2, $_SESSION['id_user'], $id_percorso, $id_elemento, $id_piazzola));
    $status2= pg_result_status($result_ie2);

}

// SISTEMO LE SEQUENZE
$query_sequenze="SELECT pg_catalog.setval('elem.sq_aste_perc'::text, COALESCE((SELECT MAX(id_asta_percorso)+1 FROM elem.aste_percorso), 1)::bigint, false);";
$result_p = pg_prepare($conn, "query_sequenze", $query_sequenze);
$result_p = pg_execute($conn, "query_sequenze", array());



//exit;
header("location: ./piazzola.php?piazzola=".$id_piazzola);


?>