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
if (!isset($_POST['code'])) { $allOk = false; }
if (!isset($_POST['userid'])) { $allOk = false; }
if (!isset($_POST['useremail'])) { $allOk = false; }

if ($allOk) {   
    
    $to = $_POST['useremail'];

    $senderName = "MD-Shop";
    $senderAddress = "mdshop@mikidani.probaljaki.hu";

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.nethely.hu';
        $mail->SMTPAuth = true;
        $mail->Username = $senderAddress;
        $mail->Password = EMAILPASS;
        $mail->Port = 1025;
        
        $mail->setFrom($senderAddress, $senderName);
        $mail->addAddress($to);
        
        $mail->CharSet = "UTF-8";
        $mail->isHTML(true);
        $mail->Subject = 'Rendelés visszaigazolása';
        $mail->Body = '<p>Feladója: '.$senderName.'<br>Email címe: '.$senderAddress.'<br>Üzenete:<br>
        Megrendelés visszaigazoló link:<br>
        <a href="http://web.mikidani.probaljaki.hu/mdshop/frontend/verified.php?userid='.$_POST['userid'].'&code='.$_POST['code'].'" target="_blank">http://web.mikidani.probaljaki.hu/mdshop/frontend/verified.php?userid='.$_POST['userid'].'&code='.$_POST['code'].'</a></p>';
       
        $mail->send();

        $msg = true;
    }
    catch(Exception $error) {
        $msg = false;
    }

    echo $msg;
}
?>