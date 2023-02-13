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



include "crea_jwt.php";


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