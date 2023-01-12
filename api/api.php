<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "class/authorization-class.php";
require "class/users-class.php";
require "class/product-class.php";
require "class/order-class.php";

$data = "";

//$_SERVER['REQUEST_METHOD'];
//$_SERVER['QUERY_STRING'];
//file_get_contents('php://input');

/*
[0] => method		method
[1] => adatcsoport	dataGroup
[2] => műveletnév	actionName
[3] => Body JSON	inData
*/

if ($_SERVER['QUERY_STRING'] == "") {
    $queryInstructions[0] = "none";
    $queryInstructions[1] = "none";
} else {
    $queryInstructions = explode("=",$_SERVER['QUERY_STRING']);
    if (!isset($queryInstructions[1])) {
        $queryInstructions[1] = "none";
    }
}

array_unshift($queryInstructions, $_SERVER['REQUEST_METHOD']);
$inData = json_decode(file_get_contents('php://input'));

$authorization = new Authorization($queryInstructions, $inData);

if ($authorization->authorizationResult) {

    if ($authorization->dataGroup == "user") {
        $user = new Users($queryInstructions, $inData);
        $data = $user->data;
    }

    if ($authorization->dataGroup == "product") {
        $product = new Product($queryInstructions, $inData);
        $data = $product->data;
    }

    if ($authorization->dataGroup == "order") {
        $order = new Order($queryInstructions, $inData);
        $data = $order->data;
    }

} else {
    $data = ["response_data" => "Unauthorized", "status_code" => 401];
}

// CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type:application/json");

if ($data) { echo json_encode($data); }
?>