<?php
session_set_cookie_params($lifetime);
session_start();

require_once('./funzioni_jwt.php');
//echo $_SESSION['test']."<br>";
//exit;

if ($_SESSION['test']==1) {
    require_once('./conn_test.php');
} else {
    require_once('./conn.php');
}

$id=$_POST['piazzola'];



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

//echo $jwt."<br><hr>";

$url_ok=$url_api_chiusura."".$id;
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

/*$postRequest = array(
    'method' => 'DELETE'
);
curl_setopt($curl, CURLOPT_POSTFIELDS, $postRequest);
*/
$json = '';
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$resp = curl_exec($curl);
//echo $resp;
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//echo "<br>". $httpCode;
curl_close($curl);
//exit;


//header("location:javascript://history.go(-1)");
//header("location:javascript://history.back(); Location.reload ()");

require_once('./req.php');

require_once('./navbar_up.php');

if ($httpCode==200){
    ?>
    <div class="container">
    <div class="row">
<div id="ok">
  <h3> Piazzola eliminata</h3>
  <form autocomplete="off" id="prospects_form3" action="piazzola.php" method="post">
  <input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id?>">
  <button class="btn btn-success"> <i class="fa-solid fa-arrow-rotate-right"></i> Ricarica piazzola</button>
  </form>
</div>
<?php
} else {
?>
<div id="problema">
<h3> Problema eliminazione piazzola</h3>
<form autocomplete="off" id="prospects_form3" action="piazzola.php" method="post">
<input type="hidden" id="piazzola" name="piazzola" value="<?php echo $id?>">
<button class="btn btn-danger"> <i class="fa-solid fa-arrow-rotate-right"></i> Ricarica piazzola</button>
</form>
</div>
<?php
}
?>
</div></div>


<?php
require_once('req_bottom.php');
require('./footer.php');
?>




</body>

</html>