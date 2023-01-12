<?php
require "../backend/connect.php";

require "PHPMailer/Exception.php";
require "PHPMailer/PHPMailer.php";
require "PHPMailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$allOk = true;
if (!isset($_POST['senderAddress'])) { $allOk = false; }
if (!isset($_POST['senderName'])) { $allOk = false; }
if (!isset($_POST['emailsubject'])) { $allOk = false; }
if (!isset($_POST['msgBody'])) { $allOk = false; }

if ($allOk) {   
    
    $toMe = "mdshop@mikidani.probaljaki.hu";
    $senderName = $_POST['senderName'];
    $senderAddress = $_POST['senderAddress'];

    $msg = "";
    
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.nethely.hu';
        $mail->SMTPAuth = true;
        $mail->Username = $toMe;
        $mail->Password = EMAILPASS;
        $mail->Port = 1025;
        
        $mail->setFrom($toMe, 'Üzenet a weboldalról:');
        $mail->addAddress($toMe);
        
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->Subject = $_POST['emailsubject'];
        $mail->Body = '<p>Feladója: '.$senderName.'<br>Email címe: '.$senderAddress.'<br>Üzenete:<br>'.nl2br($_POST['msgBody']).'</p>';
       
        $mail->send();

        $msg = '<p class="text-success">Sikeres levélküldés!</p>';
    }
    catch(Exception $error) {
        $msg = '<p class="text-danger">Sikertelen levélküldés!</p>';
    }

    echo $msg;
}
?>