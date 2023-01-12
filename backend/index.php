<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$message = "";
$apitext = "";

require_once "includes.php";

if (!isset($_SESSION['menu'])) { $_SESSION['menu'] = "users"; }
if (isset($_GET["menu"])) { $_SESSION['menu'] = $_GET["menu"]; }

// LOGIN
if (isset($_POST["submit"])) {
    unset($_POST["submit"]);
    unset($_SESSION['login']);
    
    $message = adminLogin($_POST["usernameoremail"], $_POST["password"]);
}
// LOG OUT
if (isset($_POST["logout"])) {
   logOut();
}
// UPLOAD PICTURE FILE
if (isset($_POST["filesubmit"])) {
    unset($_POST["filesubmit"]);

    if (isset($_FILES["picturefile"]) && ($_FILES['picturefile']['size'] !== 0)) {

        $uploadResponse = uploadFile("picturefile", "product-pictures/", 500000, ["jpg", "jpeg", "png", "gif"], $_POST['productid'], "true");

    } else {
        $uploadResponse = ["success" => false, "upload_message" => "Nincsen file kiválasztva!"];
    }

    if ($uploadResponse["success"] == "yes") {
        
        $data = ["token" => $_SESSION['login'], "productid" => $_POST['productid'], "serverfilename" => $uploadResponse["serverfilename"]];
        $apiResult = sendApi("POST", "?product=pictureinsert", $data);

        if ($apiResult->status_code !== 401) {
            $apitext = $apiResult->response_data;
        } else {
            unauthorized();
        }

    } else {
        $apitext = $uploadResponse["upload_message"];
    }
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../frontend/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/mdshop-admin.css">
    <title>MD-Pay Admin</title>
</head>
<body>
<?php
if (isset($_SESSION["login"])) {
    include "admin.php";
} else {
?>
<div class="container-fluid page d-flex justify-content-center align-items-center">
    <div class="row col-xs-10 col-sm-10 col-md-10 col-lg-8 col-xl-6 m-3 p-3 mx-auto bg-secondary text-center rounded">
        <div class="row col-xs-10 col-sm-10 col-md-8 mx-auto">
            <h3><strong>MD-Pay Admin</strong></h3>
            <form form="index.php" method="post">
                <label class="mt-3">Felhasználónév:</label>
                <input type="text" name="usernameoremail" class="form-control form-control-sm" placeholder="felhaszálónév" autocomplete="off">
                <label class="mt-3">Jelszó:</label>
                <input type="password" name="password" class="form-control form-control-sm" placeholder="jelszó" autocomplete="off">
                <div id="message" class="p-3"><?= $message ?></div>
                <button type="submit" name="submit" class="btn btn-warning btn-sm">Belépés</button>
            </form>
        </div>
    </div>
</div>
<?php
}
?>
<script src="../bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>