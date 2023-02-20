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
$id_percorso_originale=$_GET["p"];

$query="select rsu.num_seq, 
rsu.id_piazzola,
concat(rsu.via, ' ', rsu.civ, ' - ', rsu.riferimento) as indirizzo,
fo.descrizione_long as freq,
string_agg(distinct
concat(rsu.nome, ' x ', rsu.num),
' - ') as elem_originale,
te2.descrizione as elem_new,
count(distinct e2.id_elemento) as num_new
from 
(
select
ap2.num_seq,
 e.id_piazzola, eap.frequenza, v.nome as via,
p2.numero_civico as civ, p2.riferimento, 
te.nome, count(distinct e.id_elemento) as num
from elem.elementi_aste_percorso eap 
join elem.elementi e on e.id_elemento = eap.id_elemento 
join elem.piazzole p2 on p2.id_piazzola = e.id_piazzola 
join elem.aste a on a.id_asta = e.id_asta 
join topo.vie v on v.id_via = a.id_via 
join elem.tipi_elemento te on te.tipo_elemento = e.tipo_elemento 
join elem.aste_percorso ap2  on ap2.id_asta_percorso = eap.id_asta_percorso 
where eap.id_asta_percorso in ( 
select id_asta_percorso 
from elem.aste_percorso ap
where id_percorso = $1
)
group by ap2.num_seq,
e.id_piazzola, v.nome, eap.frequenza, p2.numero_civico, p2.riferimento, te.nome 
) rsu 
left join elem.elementi e2 on e2.id_piazzola = rsu.id_piazzola
left join elem.tipi_elemento te2 on (te2.tipo_elemento = e2.tipo_elemento)
join etl.frequenze_ok fo on fo.cod_frequenza = rsu.frequenza::int
where te2.tipo_elemento in (select es.tipo_elemento 
from elem.elementi_servizio es where es.id_servizio = $2
)
group by rsu.num_seq, 
rsu.id_piazzola,
rsu.via,
rsu.civ,
rsu.riferimento,
fo.descrizione_long,
te2.descrizione;";


$result = pg_prepare($conn, "query", $query);
$result = pg_execute($conn, "query", array($id_percorso_originale,$id_servizio));
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