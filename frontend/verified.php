<?php

require_once "../backend/includes.php";

if ((isset($_GET["userid"])) && (isset($_GET["code"]))) {

    $data = ["userid" => $_GET["userid"], "code" => $_GET["code"]];

    $apiResult = sendApi("GET", "?order=verified", $data);
    
    if ($apiResult->status_code == 201) {
        $msg = '<h5 class="text-success">'.$apiResult->response_data.'</h5>';
    } else if ($apiResult->status_code == 200)  {
        $msg = '<h5 class="text-warning">'.$apiResult->response_data.'</h5>';
    } else {
        $msg = '<h5 class="text-danger">'.$apiResult->response_data.'</h5>';
    }

} else {
    $msg = '<h5 class="text-success">Nincsen üzenet.</h5>';
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../bootstrap/bootstrap.min.css">
    <title>MD-Shop - rendellés visszaigazolása</title>
</head>
<body class="bg-primary">
    <div class="container mx-auto">
        <div class="container bg-light mt-5 p-3 rounded text-center" style="max-width: 600px">
            <div class="rounded mx-auto" style="max-width: 60%; background-color: #334455">
                <a href="index.html"><img src="./img/head/logo-head.svg" alt="MD-Shop"></a>
            </div>
            <h4 class="mt-3 mb-3">Megrendelés visszaigazolása</h4>
            <h6><?= $msg ?></h6>
        </div>
    </div>
</body>
</html>