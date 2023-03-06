<?php


session_start();




if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
} else {
    require_once ('./conn.php');
}

require_once('./credenziali_mail.php');

//echo $_SESSION['user'];



$id_piazzola=$_POST['id_piazzola'];

echo $id_piazzola."<br>";


$testo_mail=$_POST['testo_mail'];

echo $testo_mail."<br>";


//****************************************************************************
//			Invio mail
//****************************************************************************



// $query="SELECT mail FROM users.t_mail_incarichi WHERE cod='".$uo."';";
// $result=pg_query($conn, $query);
// $mails=array();
// while($r = pg_fetch_assoc($result)) {
//   array_push($mails,$r['mail']);
// }



//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

require './vendor/phpmailer/phpmailer/src/Exception.php';
require './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './vendor/phpmailer/phpmailer/src/SMTP.php';


//echo "<br>OK 1<br>";
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');
//require '../../vendor/autoload.php';
//Create a new PHPMailer instance
$mail = new PHPMailer;

//echo "<br>OK 1<br>";
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;
//Set the hostname of the mail server

// host and port on the file credenziali_mail.php
require './credenziali_mail.php';


//Set who the message is to be sent from
$mail->setFrom('applicativi@amiu.genova.it', 'Applicativi');
//Set an alternative reply-to address
$mail->addReplyTo('no-reply@amiu.genova.it', 'No Reply');
//Set who the message is to be sent to



/*$query="SELECT mail, id_telegram FROM users.t_mail_incarichi WHERE cod=$1;";
$result = pg_prepare($conn, "myquery0", $query);
$result = pg_execute($conn, "myquery0", array($uo));
$mails=array();
$telegram=array();
$messaggio="\xE2\x9C\x89 Messaggio inviato da Protezione Civile Genova circa l'incarico assegnato: ".$note."";
while($r = pg_fetch_assoc($result)) {
  array_push($mails,$r['mail']);
  array_push($telegram,$r['id_telegram']);
  //sendMessage($r['id_telegram'], $messaggio , $token);
}
*/

// In questo momento il pezzo sopra non serve.. più semplice indirizzo fisso
$mails=array('vobbo@libero.it','roberto.marzocchi@amiu.genova.it');



while (list ($key, $val) = each ($mails)) {
  $mail->AddAddress($val);
}
//Set the subject line
$mail->Subject = 'Messaggio inviato dal territorio attraverso l\'applicativo per il passaggio al bilaterale';
//$mail->Subject = 'PHPMailer SMTP without auth test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$body =  'Piazzola: '.$id_piazzola.'<br><br>'.$testo_mail.'

 <br> <br> '.$titolo_app.'';
  
require('./informativa_privacy_mail.php');

$mail-> Body=$body ;

//$mail->Body =  'Corpo del messaggio';
//$mail->msgHTML(file_get_contents('E\' arrivato un nuovo incarico da parte del Comune di Genova. Visualizza lo stato dell\'incarico al seguente link e aggiornalo quanto prima. <br> Ti chiediamo di non rispondere a questa mail'), __DIR__);
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');
//send the message, check for errors
//echo "<br>OK 2<br>";
if (!$mail->send()) {
    echo "<h3>Problema nell'invio della mail: " . $mail->ErrorInfo;
	?>
	<script> //alert(<?php echo "Problema nell'invio della mail: " . $mail->ErrorInfo;?>) </script>
	<?php
	//echo '<br>La comunicazione è stata correttamente inserita a sistema, ma si è riscontrato un problema nell\'invio della mail.';
	echo '<div style="text-align: center;"><img src="../../img/no_mail_com.png" width="75%" alt=""></div>';
	echo '<br>Entro 10" verrai re-indirizzato alla pagina precedente, clicca al seguente ';
	echo '<a href="./piazzola.php?piazzola='.$id_piazzola.'">link</a> per saltare l\'attesa.</h3>' ;
	//sleep(30);
    header("refresh:10;url=./piazzola.php?piazzola=".$id_piazzola."");
} else {
    echo "Message sent!";
	header("location: ./piazzola.php?piazzola=".$id_piazzola);
}
//exit;
//header("location: ../dettagli_incarico.php?id=".$id);


?>


?>