<?php
session_start();
#require('../validate_input.php');

if ($_SESSION['test']==1) {
    require_once('../conn_test.php');
} else {
    require_once('../conn.php');
}

//echo "OK";


$id_servizio=$_GET["s"];


$query='select distinct v.nome, v.id_via, p.descrizione, p.id_percorso, 
count(eap.id_elemento) as n_elem
from elem.aste_percorso ap
join elem.elementi_aste_percorso eap on ap.id_asta_percorso = eap.id_asta_percorso 
join elem.aste a on a.id_asta = ap.id_asta 
join topo.vie v on a.id_via = v.id_via 
join elem.percorsi p on p.id_percorso = ap.id_percorso 
where p.id_categoria_uso in (3,6) and p.id_servizio = $1
group by v.nome, v.id_via, p.descrizione, p.id_percorso
order by 1';


$result = pg_prepare($conn, "query", $query);
$result = pg_execute($conn, "query", array($id_servizio));
$status= pg_result_status($result);

//echo $id_servizio."<br>";
//echo $status."<br>";


$rows = array();
while($r = pg_fetch_assoc($result)) {
    $rows[] = $r;
}

#echo "<br>OK";


#echo $rows ;
if (empty($rows)==FALSE){
    //print $rows;
    $locations =(json_encode($rows));
    echo $locations;
} else {
    echo "[{\"NOTE\":'No data'}]";
}


?>