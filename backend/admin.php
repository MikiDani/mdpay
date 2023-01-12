<?php
if (isset($_SESSION["login"])) {

    $data = ["token" => $_SESSION["login"]];
    $userData = sendApi('GET', '?user=userdata', $data);
    
    if ($userData->status_code == 200) {

        $login = [
            "usertoken" => $_SESSION["login"],
            "username" => $userData->response_data->username,
            "useremail" => $userData->response_data->useremail,
            "userrank" => $userData->response_data->userrank
        ];

        ?>
        <div class="container m-0 p-0 mx-auto mt-3">
            <div class="text-center">
                <h3 class="text-warning mb-3">MD Shop - Admin</h3>
                <h6 class="text-warning mb-3">Bejelentkezve: <?php echo $login["username"]; ?></h6>
            </div>
            <nav class="navbar navbar-expand-sm navbar-light bg-primary rounded-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#"></a>
                    <button class="navbar-toggler hambColor mx-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarToggler">
                        <ul class="navbar-nav me-auto mb-lg-0 ms-3">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="index.php?menu=products">Termékek</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?menu=users">Felhasználók</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?menu=orders">Megrendelések</a>
                            </li>
                        </ul>
                        <div class="text-center ms-3">
                            <form action="index.php" method="post"><button type="submit" name="logout" class="btn btn-danger btn-sm">Kilépés</button></form>
                        </div>
                    </div>
                </div>
            </nav>
            <div class="row bg-secondary m-0 p-3">
                <?php
                $_SESSION['menu'] == "products" ? require_once "_products.php" : false;
                $_SESSION['menu'] == "users" ? require_once "_users.php" : false;
                $_SESSION['menu'] == "orders" ? require_once "_orders.php" : false;
                ?>
            </div>
            <div class="row bg-primary m-0 p-3 text-center rounded-bottom">
                <a href="#" class="text-warning">Ugrás az oldal tetejére.</a>
            </div>
        </div>
        <?php
    } else { unauthorized(); }
} else { unauthorized(); }

// list functions

function ifShow($num) {
    if (isset($_POST['accordion-num'])) {
        $insert = $_POST['accordion-num'] == $num ? "show" : false;
        return $insert;
    }
}

function ifMessage($num, $apitext) {
    $insert = "";
    if (isset($_POST['accordion-num'])) {
        if ($apitext) {
            $insert = $_POST['accordion-num'] == $num ? "<div class='text-center mb-2'><span class='text-danger'>$apitext</span></div>" : false;
            return $insert;
        }
        $insert = ($_POST['accordion-num'] == $num) ? "<div class='text-center mb-2'><span class='text-danger'>Nincsen kijelőlve a checkbutton!</span></div>" : false;
    }
    return $insert;
}
?>