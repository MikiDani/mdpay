<h4>Rendelések</h4>
<?php
$apitext = "";

if (isset($_POST['orderdelid'])) {
    if (isset($_POST['delcheckbox']) && ($_POST['delcheckbox'] == 'on')) {
        
        $data = ["token" => $_SESSION["login"], "orderid" => $_POST['orderdelid']];
        $deleteOrder = sendApi('DELETE', '?order=delete', $data);
        
        if ($deleteOrder->status_code == 200 || $deleteOrder->status_code == 201) {
            unset($_POST['accordion-num']);
            unset($_POST['delcheckbox']);
        } else {
            $apitext = $deleteOrder->response_data;
        }
    }
}

$data = ["token" => $_SESSION["login"]];
$query = sendApi('GET','?order=list', $data);
$result = apiResultProcessing($query->status_code, $query->response_data);

if ($result) {
    echo "<div class='accordion accordion-flush m-0 p-0' id='accordionFlushExample'>";
    $num = 0;
    foreach ($result as $order) {

        $num ++;
        echo '<div class="accordion-item"><h2 class="accordion-header bg-primary" id="flush-heading'.$num.'"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse'.$num.'" aria-expanded="false" aria-controls="flush-collapse'.$num.'">'.$num.'. <strong>'.epochDateDraw($order->orderepoch).' | Rendelési azonosító: '.$order->code.'</strong></button></h2><div id="flush-collapse'.$num.'" class="accordion-collapse collapse '.ifShow($num).'" aria-labelledby="flush-heading'.$num.'" data-bs-parent="#accordionFlushExample"><div class="accordion-body">';
        
        echo '
        <table class="table">
        <tr>
            <td>Rendelés Dátuma:</td>
            <td>'.epochDateDraw($order->orderepoch).'</td>
            </tr>
            <tr>
            <td>Rendelés megerősítése:</td>
            <td><span class="'.verifiedBgColor($order->verified).'">'.verifiedDraw($order->verified).'</span></td>
        </tr>
        <tr>
            <td>Felhasználó ID:</td>
            <td>'.$order->userid.'</td>
        </tr>
        <tr>
            <td>Rang:</td>
            <td>'.$order->rank.'</td>
        </tr>
        <tr>
            <td>Felhasználónév:</td>
            <td>'.$order->username.'</td>
        </tr>
        <tr>
            <td>Email:</td>
            <td>'.$order->email.'</td>
        </tr>
        <tr>
            <td>Megrendelési cím:</td>
            <td>'.$order->postalcode.' '.$order->city.' '.$order->designation.' '.$order->designationtype.' '.$order->designationnumber.'.</td>
        </tr>
        <tr>
            <td colspan = "2" class="text-center bg-light">Megrendelt termékek:</td>
        </tr>
        <tr>
            <td colspan = "2" class="text-center">'.productList($order->productlist).'</td>
        </tr>
        <tr>
            <td>Végösszeg:</td>
            <td><strong>'.$order->totalprice.' Ft</strong></td>
        </tr>
        </table>';

        echo ifMessage($num, $apitext);

        echo '<form class="d-flex justify-content-center align-items-center" method="post" action="index.php"><input class="form-check-input me-2" type="checkbox" name="delcheckbox"><input type="hidden" name="accordion-num" value="'.$num.'"><button name="orderdelid" value="'.$order->id.'" class="btn btn-danger btn-sm">Megrendelés törlése</button></form>';

        echo '</div></div></div>'; // Accordion close
    }
    echo "</div>";
} else { echo "<div class='text-center text-warning'>Jelenleg nincsen megrendelés.</div>"; }

function verifiedDraw ($value) {
    if ($value) { return 'Igen'; } else { return 'Nem'; }
}

function verifiedBgColor ($value) {
    if ($value) { return 'green-bg'; } else { return 'red-bg'; }
}

function epochDateDraw ($epoch) {
    return date("Y-m-d H:i:s", substr($epoch, 0, 10));
}

function productList ($data) {
    
    $list = json_decode(json_decode($data));
    
    $returntext = '<table class="table">';
    $upper = 1;

    foreach ($list as $product) {
        
        if ($product->product->markdown == 0) {
            $realPrice = $product->product->price;
        } else {
            $realPrice = ($product->product->price/100) * $product->product->markdown;
        }

        if (isset($product->pictures[0]->serverfilename)) {
            $picLink = $product->pictures[0]->serverfilename;
            $picLinkText = $product->pictures[0]->text;
        } else {
            $picLink = "none.png";
            $picLinkText = "none";
        }
        
        $returntext .= '
        <tr><td colspan="2"><strong>'.$upper.'.</strong><br></td></tr>
        <tr><td>teremék ID:</td><td>'.$product->product->id.'</td></tr>
        <tr><td>típus: </td><td>'.$product->product->typename.'</td></tr>
        <tr><td>megnevezés: </td><td>'.$product->product->name.'</td></tr>
        <tr><td>Ára: </td><td>'.$realPrice.' Ft</td></tr>
        <tr><td>Kevezmény: </td><td>'.$product->product->markdown.' %</td></tr>
        <tr><td>Megrendelt darabszám: </td><td>'.$product->quantity.' darab</td></tr>
        
        <tr><td colspan = "2">Info: '.$product->product->text.'</td></tr>
        <tr><td colspan = "2"><img src="product-pictures/small_'.$picLink.'" alt="'.$picLinkText.'" title="'.$picLinkText.'" class="rounded">
        </td></tr>
        ';

        $upper++;
    }
    
    $returntext .= '</table>';

    return $returntext;
}
?>