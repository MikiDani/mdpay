<?php

if (isset($_POST["filterresetsubmit"])) {
    unset($_POST);
    unset($_SESSION["filterProducts"]); unset($_SESSION["filtertype"]); unset($_SESSION["filtername"]);
    unset($_SESSION["filterminprice"]); unset($_SESSION["filtermaxprice"]); unset($_SESSION["markdownswitch"]);
}

if (!isset($_SESSION["filtertype"])) { $_SESSION["filtertype"] = 0; }
if (!isset($_SESSION["markdownswitch"])) { $_SESSION["markdownswitch"] = 0; }

ifNotIssetSession(["filtername", "filterminprice", "filtermaxprice"]);
ifGetPost(["filtertype", "markdownswitch", "filtername", "filterminprice", "filtermaxprice"]);

$filterTypeName = productTypenameLoad($_SESSION["filtertype"]);
$sendFiltertype = ($filterTypeName == "összes") ? "*" : $filterTypeName;
$data = [
    "filtertype" => $_SESSION["filtertype"],
    "filtername" => $_SESSION["filtername"],
    "filterminprice" => $_SESSION["filterminprice"],
    "filtermaxprice" => $_SESSION["filtermaxprice"],
    "filtermarkdown" => $_SESSION["markdownswitch"]
];

$result = sendApi("POST", "?product=productfilter", $data);
$_SESSION["filterProducts"] = apiResultProcessing($result->status_code, $result->response_data);

?>
<div class="col-12 mx-auto text-center bg-light p-2 mt-2 rounded position-relative">
    <form action="index.php" method="post" class="mx-auto">
    <button name="filterresetsubmit" class="btn btn-warning btn-sm rounded m-1 reset-position"><img src="icons/icon-reset.svg" alt="szűrés törlése" title="szűrés törlése" class="icon-controll"></button>
        <div class="d-flex justify-content-between align-items-center p-1 text-end">
            <div class="ms-2 me-4 w-50">Típus</div>
            <select name="filtertype" class="form-select form-select-sm d-inline m-0 p-1">
                <?php echo productTypeList($filterTypeName, true); ?>
            </select>
        </div>
        <div class="d-flex justify-content-between align-items-center col-12 p-1 text-end">
            <div class="ms-2 me-4 w-50">Terméknév</div>
            <input autocomplete="off" type="text" name="filtername" value="<?= valueReturn("filtername"); ?>" class="form-control form-control-sm d-inline">
        </div>
        <div class="d-flex justify-content-between align-items-center col-12 p-1 text-end">
            <div class="ms-2 me-4 w-50">Min ár</div>
            <input autocomplete="off" type="number" name="filterminprice" value="<?= valueReturn("filterminprice"); ?>" class="form-control form-control-sm d-inline">
        </div>
        <div class="d-flex justify-content-between align-items-center col-12 p-1 text-end">
            <div class="ms-2 me-4 w-50">Max ár</div>
            <input autocomplete="off" type="number" name="filtermaxprice" value="<?= valueReturn("filtermaxprice"); ?>" class="form-control form-control-sm d-inline">
        </div>
        <div class="d-flex justify-content-center align-items-center col-12 p-1 text-end">
            <div class="ms-2 me-4 d-inline">Akciós-e?</div>
            <div class="btn-group" role="group" aria-label="Basic example">
                <div class="form-check">
                    <input class="form-check-input me-1" type="radio" name="markdownswitch" value="1" <?= ifChecked("1", $_SESSION["markdownswitch"]); ?> >
                    <label class="form-check-label me-3">Igen</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="markdownswitch" value="0" <?= ifChecked("0", $_SESSION["markdownswitch"]); ?> >
                    <label class="form-check-label">Nem</label>
                </div>
            </div>
        </div>
        <button type="submit" name="filtersubmit" class="btn btn-success form-control btn-sm mt-2">Szűrés</button>
        <!--
            <button type="submit" name="filterresetsubmit" class="btn btn-warning form-control btn-sm mt-2">Töröl</button>
        -->
    </form>
</div>

<?php

function valueReturn($inVariable) {
    if (isset($_SESSION[$inVariable])) { return $_SESSION[$inVariable]; }
}

function ifChecked($value, $markdownvalue) {
    echo (($value == $markdownvalue)) ? "checked" : false;
}

function ifGetPost($variableArray) {
    foreach ($variableArray as $listElement) {
        if (isset($_POST[$listElement])) { $_SESSION[$listElement] = $_POST[$listElement]; }
    }
}

function ifNotIssetSession($variableArray) {
    foreach ($variableArray as $listElement) {
        if (!isset($_SESSION[$listElement])) { $_SESSION[$listElement] = ""; }
    }
}