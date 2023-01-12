<?php

// product name modification
if (isset($_POST["productnamesubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "productname" => $_POST["productname"], "function" => "namemod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// product price modification
if (isset($_POST["productpricesubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "productprice" => $_POST["productprice"], "function" => "pricemod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// product textsubmit modification
if (isset($_POST["producttextsubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "producttext" => $_POST["producttext"], "function" => "textmod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// product type modification
if (isset($_POST["producttypesubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "typeid" => $_POST["typeid"], "function" => "typemod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// product markdown modification
if (isset($_POST["productmarkdownsubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "markdown" => $_POST["productmarkdown"], "function" => "markdownmod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}
// product instock modification
if (isset($_POST["instocksubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "instock" => $_POST["instock"], "function" => "instockmod"];
    $result = sendApi('POST','?product=productmod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// Pictures
// Product Primary Picture modification
if (isset($_POST["pictureprimarysubmit"])) {
    $data = ["token" => $_SESSION["login"], "productid" => $_POST["productid"], "pictureid" => $_POST["pictureid"], "function" => "primarymod"];
    $result = sendApi('POST','?product=picturemod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// Product Picture Text modification
if (isset($_POST["picturetextsubmit"])) {
    $data = ["token" => $_SESSION["login"], "pictureid" => $_POST["pictureid"], "function" => "textmod", "picturetext" => $_POST["picturetext"]];

    $result = sendApi('POST','?product=picturemod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// Product Picture Delete
if (isset($_POST["picturedelsubmit"])) {
    $data = ["token" => $_SESSION["login"], "pictureid" => $_POST["pictureid"], "productid" => $_POST["productid"], "function" => "delete"];
    $result = sendApi('POST','?product=picturemod', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// Insert New Product
if (isset($_POST["newproductsubmit"])) {
    $data = ["token" => $_SESSION["login"], "newtype" => $_POST["newtype"], "newproductname" => $_POST["newproductname"]];
    $result = sendApi('POST','?product=productinsert', $data);
    $apitext = apiResultProcessing($result->status_code, $result->response_data);
}

// delete product. Delete server picture, and product_pic table and product table
if (isset($_POST['productdelidsubmit'])) {
    if (isset($_POST['delcheckbox']) && ($_POST['delcheckbox'] == 'on')) {

        $data = ["token" => $_SESSION["login"], "productid" => $_POST['productdelidsubmit']];
        $deleteProduct = sendApi('DELETE', '?product=productdelete', $data);

        if ($deleteProduct->status_code == 202) {
            unset($_POST['accordion-num']);
            unset($_POST['delcheckbox']);
        } else {
            $apitext = $deleteProduct->response_data;
        }
    }
}

echo '
<div class="col-12 mx-auto text-center bg-light p-2 rounded bg-warning">
<form action="index.php" method="post">
<h4>Új termék hozzáadása:</h4>
    <div>
        <div class="d-inline newtipo">Típus:</div>
        <select name="newtype" class="form-select form-select-sm d-inline w-25 ms-2 me-2">';
            echo productTypeList("elektronika", false); 
        echo '</select>
        <div class="d-inline newtipo">Terméknév:</div>
        <input autocomplete="off" type="text" name="newproductname" value="új termék" class="form-control form-control-sm d-inline w-25 ms-2 me-2">
    </div>
    <button type="submit" name="newproductsubmit" class="btn btn-success form-control btn-sm d-inline mt-2">Rögzítés</button>
</form>
</div>';

include_once "_productfilter.php";

// Get Product List
echo '<div class="text-center m-2 mb-0"><h4>Termékek listája:</h4></div>';

if (isset($_SESSION["filterProducts"])) {
    $products = $_SESSION["filterProducts"];
} else {
    $result = sendApi('GET','?product=allproduct', "");
    $products = apiResultProcessing($result->status_code, $result->response_data);
}

if (($products != null) && ($products != "none")) {

    echo "<div class='accordion accordion-flush m-0 p-0' id='accordionFlushExample'>";
    $num = 0;
    foreach ($products as $product) {
        $num ++;
        echo '<div id ="'.$num.'" class="accordion-item"><h2 class="accordion-header bg-primary" id="flush-heading'.$num.'"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse'.$num.'" aria-expanded="false" aria-controls="flush-collapse'.$num.'">'.$num.'. '.$product->typename.' | '.$product->name.'</button></h2><div id="flush-collapse'.$num.'" class="accordion-collapse collapse '.ifShow($num).'" aria-labelledby="flush-heading'.$num.'" data-bs-parent="#accordionFlushExample"><div class="accordion-body">';
        // Accordion 3 div open
        echo '<div class="text-center">'.ifMessage($num, $apitext).'</div>';
        echo '<form method="post" action="index.php"><input type="hidden" name="productid" value="'.$product->id.'"><input type="hidden" name="accordion-num" value="'.$num.'"><table class="table verticalalign"><tr><td class="text-id">id:'.$product->id.'.</td><td>típus:</td><td><div class="d-flex align-items-center"><select name="typeid" class="form-select w-75 me-2">';
        // product type list
        echo productTypeList($product->typename, false);
        echo '</select><button name="producttypesubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="típus módosítás" title="típus módosítás" class="icon-controll"></button></div></td></tr><tr><td></td><td>terméknév:</td><td><div class="d-flex align-items-center"><input autocomplete="off" type="text" name="productname" value="'.$product->name.'" class="form-control w-75 me-2"><button name="productnamesubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="terméknév módosítás" title="terméknév módosítás" class="icon-controll"></button></div></td></tr><tr><td></td><td>raktárkészlet:</td><td><div class="d-flex align-items-center"><input autocomplete="off" type="number" name="instock" value="'.$product->instock.'" class="form-control w-75 me-2"><button name="instocksubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="raktárkészlet módosítás" title="raktárkészlet módosítás" class="icon-controll"></button></div></td></tr><tr><td></td><td>eredeti ár:</td><td><div class="d-flex align-items-center"><input autocomplete="off" type="number" name="productprice" value="'.$product->price.'" class="form-control w-75 me-2"><button name="productpricesubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="erededi ár módosítás" title="eredeti ár módosítás" class="icon-controll"></button></div></tr><tr><td></td><td>Engedmény százalékban:</td><td><div class="d-flex align-items-center"><input autocomplete="off" type="number" name="productmarkdown" value="'.$product->markdown.'" class="form-control w-75 me-2"><button name="productmarkdownsubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="engedmény módosítás" title="engedmény módosítás" class="icon-controll"></button></div></tr>';
        if ($product->markdown !== 0) {
            $lowPrice =  $product->price - (($product->price / 100) * $product->markdown);
            echo '<tr><td></td><td>Akciós ár:</td><td><div class="d-flex align-items-center"><div class="w-75 ms-2 text-danger"><strong>'.$lowPrice.' Ft</strong></div></div></tr>';
        }
        echo '<tr><td colspan="3" class="text-center m-0 p-1 bg-light">Termék leírás:</td></tr><tr><td colspan="3"><textarea class="form-control mb-2" name="producttext" rows="3">'.$product->text.'</textarea><div class="text-center"><button name="producttextsubmit" class="btn btn-success btn-sm rounded m-1 d-inline"><img src="icons/icon-accept.svg" alt="leírás módosítás" title="leírás módosítás" class="icon-controll"></button></div></td></tr></table></form>';
        // picture list
        $data2 = ["productid" => $product->id];
        $pictureResult = sendApi('POST','?product=picturelist', $data2);
        echo '<div class="text-center d-flex flex-wrap justify-content-center align-items-stretch">';
        if (($pictureResult->status_code == 200) && ($pictureResult->response_data !== null)) {
            foreach($pictureResult->response_data as $picture) {
            echo '<form method="post" action="index.php" class="border bg-light m-1 rounded"><input type="hidden" name="productid" value="'.$product->id.'"><input type="hidden" name="pictureid" value="'.$picture->id.'"><input type="hidden" name="accordion-num" value="'.$num.'"><div class="m-1"><div class="btn-group align-items-center mb-2"><input autocomplete="off" type="text" name="picturetext" value="'.$picture->text.'" class="form-control form-control-sm text-center me-1"><button name="picturetextsubmit" class="btn btn-success btn-sm rounded m-1"><img src="icons/icon-accept.svg" alt="képcím módosítás" title="képcím módosítás" class="icon-controll"></button></div><div><button type="submit" name="pictureprimarysubmit" class="m-0 p-0 border-0 rounded"><img src="product-pictures/small_'.$picture->serverfilename.'" height="100" class="rounded '.primaryPic($picture->primarypic).'" alt="'.$picture->text.'" title="'.$picture->text.'"></button></div><div><h6 class="text-secondary mt-2">'.$picture->serverfilename.'</h6></div><div class="d-flex justify-content-center align-items-center"><button name="picturedelsubmit" class="btn btn-danger btn-sm rounded m-1"><img src="icons/icon-trash.svg" alt="törlés" title="törlés" class="icon-controll"></button></div></div></form>';
            }
        }
        echo '</div>';
        // Picture upload
        echo '<form method="post" action="index.php" enctype="multipart/form-data">
        <div class="mt-3 mb-2 text-center bg-light rounded p-1"><div class="btn-group" role="group" aria-label="Basic example"><div class="border-0 rounded-start"><input type="file" name="picturefile" class="form-control btn form-control-sm"></div><input type="hidden" name="accordion-num" value="'.$num.'"><input type="hidden" name="productid" value="'.$product->id.'"><button type="submit" name="filesubmit" class="btn btn-sm btn-primary rounded m-1 ms-2">Feltöltés</button></div></div>';
        // törlés
        echo '<div class="d-flex justify-content-center align-items-center"><input class="form-check-input me-2" type="checkbox" name="delcheckbox"><input type="hidden" name="accordion-num" value="'.$num.'"><button type="submit" name="productdelidsubmit" value="'.$product->id.'" class="btn btn-danger btn-sm ms-2">Termék törlése</button></div></form>';
        echo '</div></div></div>'; // Accordion 3 div close
    }
    echo "</div>";

    if (isset($_POST['accordion-num'])) {
    ?>
    <script>document.getElementById("<?= $_POST['accordion-num']; ?>").scrollIntoView();</script>
    <?php
    }
    
    //unset($_POST);

} else { echo "<div class='text-center text-warning'>Nincsen találat.</div>"; }

function primaryPic ($value) {
    if ($value == 1) {
        return "primaryborder";
    }
}
?>