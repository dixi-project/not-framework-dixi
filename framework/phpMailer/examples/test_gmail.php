<?php

//error_reporting(E_ALL);
error_reporting(E_STRICT);

//date_default_timezone_set('America/Toronto');

include_once('../class.phpmailer.php');
include("../class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
//$fs = fsockopen("ssl://smtp.gmail.com", 465);
//echo 1; 
$mail             = new PHPMailer();
//$body             = $mail->getFile('contents.html');

$body             = eregi_replace("[\]",'',$body);

$mail->IsSMTP();
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                  // sets the prefix to the servier
$mail->Host       = "mail.solteci.com";//"ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
//$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

$mail->Username   = "alfredo@solteci.com";  // GMAIL username
$mail->Password   = "krisnadixisharon";            // GMAIL password

$mail->AddReplyTo("alfredo@solteci.com","First Last");

$mail->From       = "alfredo@solteci.com";
$mail->FromName   = "First Last";

$mail->Subject    = "PHPMailer Test Subject via gmail";

//$mail->Body       = "Hi,<br>This is the HTML BODY<br>";                      //HTML Body
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->WordWrap   = 50; // set word wrap

$mail->MsgHTML($body);

$mail->AddAddress("alfdixi@gmail.com", "PP");

$mail->AddAttachment("images/phpmailer.gif");             // attachment

$mail->IsHTML(true); // send as HTML

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>
