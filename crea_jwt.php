<?php
//echo "Ci sono <br>";

$query_role='SELECT  su.id_user, su.email, sr.id_role, sr."name" as "role" FROM util.sys_users su
        join util.sys_roles sr on sr.id_role = su.id_role  
        where su."name" ilike $1;';
$result_n = pg_prepare($conn, "my_query_navbar1", $query_role);
$result_n = pg_execute($conn, "my_query_navbar1", array($_SESSION['username']));
$status1= pg_result_status($result_n);
//echo $status1."<br>";
// recupero i dati dal DB di SIT
while($r = pg_fetch_assoc($result_n)) {
    $role_SIT=$r['role'];
    $id_role_SIT=$r['id_role'];
    $id_user_SIT=$r['id_user'];
    $mail_SIT=$r['email'];
}

// creo il JWT
$issuedAt   = new DateTimeImmutable();
$expire     = $issuedAt->modify('+420 minutes')->getTimestamp();

$headers = array('alg'=>'HS256','typ'=>'JWT');
$payload = array('role'=>$role_SIT,
        'name'=> $_SESSION['username'],
        "userId"=> $id_user_SIT,
        "roleId"=>$id_role_SIT,
        "userMail"=>$mail_SIT,
        'iss' => $iss,
        'grants' => 'MOD_ASTE;MOD_PIAZZOLE;MOD_ELEMENTI;FILTER_EL_ID;FILTER_PIAZ_ID;V_LOG;V_LAYER_GRAFICI;DEL_PIAZZOLA;DEL_ELEMENTO;ADD_PIAZZOLE;ADD_PERCORSI;ADD_ELEMENTO;ADD_ASTE;MOD_SEASON;MOD_VEHICLES;ASSOC_PERCORSI;V_LAYER_TOPO;V_PERCORSI;MOD_PERCORSI;V_UTENTI;MOD_UTENTI;V_SERVIZI;MOD_SERVIZI;IMPORT_PIAZZOLE;MOD_UTENZE;V_UTENZE;IMPORT_PERCORSI',
        'iat'  => $issuedAt->getTimestamp(),         // Issued at: time when the token was generated
        'nbf'  => $issuedAt->getTimestamp(),
        //'exp'	=>(time() + 60)
        'exp'  => $expire,                           // Expire
    );

$jwt = generate_jwt($headers, $payload, $secret_pwd);


?>