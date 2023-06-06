<?php 

require_once('./jwt.php');
//require_once("./vendor/autoload.php");
use firebase\jwt\JWT;
use firebase\jwt\Key;

$token=$_GET['jwt'];

//set the duration to 0, so that cookie duration will end only when users browser is close
setcookie("tokenCookie", $token, 0);

echo $token . "<br><br>";

echo $secret_pwd ."ok 0<br><br>";

$decoded1=json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1]))));

//print_r($decoded1);
//echo $secret_pwd ."<br><br>";

//echo $decoded1['userId '] ."<br><br>";

 
foreach($decoded1 as $key => $value)
{
   echo $key." is ". $value . "<br>";
   if ($key=='userId') {
        $userId = (int)$value;
   }
   if ($key=='exp') {
        $exp = (int)$value;
        if (time()>$exp){
            die ("Token di autorizzazione scaduto");
        }
   }
}

echo 'Now: '. time()."<br><br>";
echo 'Exp: '.$exp ."<br><br>";
echo 'userId: '.$userId ."<br><br>";

//$decoded = JWT::decode($token, $secret_pwd, array('HS256'));
//$decoded = JWT::decode($jwt, new Key($secret_pwd, 'HS256'));
//echo "ok 1<br><br>";
//print_r($decoded);
?>