<?php

session_start();
#require('../validate_input.php');

//require_once('./funzioni_jwt.php');
//echo $_SESSION['test']."<br>";
//exit;

if ($_SESSION['test']==1) {
    require_once('./conn_test.php');
} else {
    require_once('./conn.php');
}

//echo "OK";


$id_via=$_POST["id_via"];
$id_percorso=$_POST["id_percorso"];

echo $id_via."<br>";
echo $id_percorso."<br>";


$query='select id_asta_percorso from elem.aste_percorso ap 
where id_percorso = $1 and id_asta in (select id_asta  from elem.aste where id_via=$2)';


$result = pg_prepare($conn, "query", $query);
$result = pg_execute($conn, "query", array($id_percorso, $id_via));
$status= pg_result_status($result);


// creo il JWT
include "crea_jwt.php";
echo $jwt."<br>";


//$rows = array();
while($r = pg_fetch_assoc($result)) {
    echo $r['id_asta_percorso']."<br>";

    

    // CHIAMO L'API DEL SIT !!! (volendo si potrebbero anche fare le query qua dentro, ma così sono sicuro di simulare il SIT)
    

    //echo $jwt."<br><hr>";

    $url_ok=$url_eliminazione_percorso."".$r['id_asta_percorso'];
    echo $url_ok ."<br>";
    
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url_ok);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");




    $headers = array(
    "Accept: application/json",
    "Authorization: Bearer {$jwt}",
    );

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $json = '';
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($curl);
    //echo $resp;
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    //echo "<br>". $httpCode;
    curl_close($curl);
    
}

?>