<?php
define('URL', 'http://web.mikidani.probaljaki.hu/mdpay/api/api.php');

function sendApi($method, $request, $data) {

    $headers = array(
        "Content-Type: application/json",
        "Accept: application/json",
        "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201"
    );
    $options = array(
        "http" => array(
            "method" => $method,
            "header" => $headers,
            "content" => json_encode($data)
        )
    );

    $context = stream_context_create($options);

    $result = file_get_contents(URL.$request, false, $context);
    $response = json_decode($result);

    return $response;
}

function apiResultProcessing($status_code, $response_data) {
    if ($status_code !== 401) { return $response_data; }
    unauthorized();
}

function adminLogin ($usernameoremail, $password) {
    $message = "";
    
    $data = ["usernameoremail" => $usernameoremail, "password" => $password];
    $response = sendApi("POST", "?user=login", $data);
        
    if (isset($response) && ($response->status_code == 200)) {
                
        $token = $response->response_data;
        $data = ["token" => $token];
        $userData = sendApi("POST", "?user=userdata", $data);
        
        if ($response->status_code == 200) {        
            if ($userData->response_data->userrank == 1) {
                        
                unset($_POST["usernameoremail"]);
                unset($_POST["password"]);

                $_SESSION["login"] = $token;
                $message = "Sikeres azonosítás!";
                
            } else {
                unset($_SESSION["login"]);
                $message = "Nincsen megfelelő rangod a belépéshez!";
            }
        } else {
            unset($_SESSION["login"]);
            $message = "Sikertelen belépés!";
        }
    } else {
        unset($_SESSION["login"]);
        $message = "Sikertelen belépés!";
    }
    
    return $message;
}

function logOut () {
    session_unset();
    $_POST = [];
    header("Refresh:0; url=index.php");
}

function unauthorized () {
    logOut();
}

function uploadFile ($filename, $directory, $filesize, $extensions, $upname, $needSmallFile) {
    $upload = true;
    
    $target_dir = $directory;
    $target_file = $target_dir.basename($_FILES[$filename]["name"]);
    $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    if ($_FILES[$filename]["size"] > $filesize) {
        $upload = false;
        $upload_message = "Túl nagy a file mérete! (Max 500 kilobyte)";
    }

    $correctExtension = false;
    $extensionTextList = "";
    foreach ($extensions as $extension) {
        if ($file_type == $extension) { $correctExtension = true; }
        $extensionTextList .= $extension.", ";
    }

    if ($correctExtension == false) {
        $upload = false;
        $upload_message = "Nem megfelelő file formátum! Csak $extensionTextList kiterjesztések megengedettek.";
    }

    if ($upload == true) {

        $ext = explode(".", $_FILES[$filename]["name"]);
        $epoch = time();
        $serverFilename =  $upname."_".$epoch.".".end($ext);
        $bigFilename =  "big_".$serverFilename;
        $smallFilename =  "small_".$serverFilename;
        $fileWay = $directory.$bigFilename;

        if (move_uploaded_file($_FILES[$filename]["tmp_name"], $fileWay)) {
            $upload = true;
            $upload_message = "Sikeres file feltöltés.";

            if ($needSmallFile) {
                
                $maxDimW = 200;
                $maxDimH = 200;
                
                list($width, $height, $type, $attr) = getimagesize($fileWay);
                if ($width > $maxDimW || $height > $maxDimH) {
                    
                    $size = getimagesize($fileWay);
                    $ratio = $size[0]/$size[1];
                    
                    if ($ratio > 1) {
                        $width = $maxDimW;
                        $height = $maxDimH/$ratio;
                    } else {
                        $width = $maxDimW*$ratio;
                        $height = $maxDimH;
                    }
                    
                    $src = imagecreatefromstring(file_get_contents($fileWay));
                    $dst = imagecreatetruecolor($width, $height);
                    
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

                    if((end($ext) == "jpg") || (end($ext) == "jpeg")) {
                        $smallFile = imagejpeg($dst, $directory.$smallFilename);
                    }
                    if(end($ext) == "png") {
                        $smallFile = imagepng($dst, $directory.$smallFilename);
                    }
                    if(end($ext) == "gif") {
                        $smallFile = imagegif($dst, $directory.$smallFilename);
                    }

                    move_uploaded_file($smallFile, $directory.$smallFilename);
                }
            }
        } else {
            $upload = false;
            $upload_message = "Hiba a file feltöltésekor!";
        }

    }
    $success = ($upload) ? "yes" : "no";

    $uploadResponse = ["success" => $success, "upload_message" => $upload_message];
    if (isset($serverFilename)) { $uploadResponse["serverfilename"] = $serverFilename; }

    return $uploadResponse;
}

function productTypeList ($typevalue, $allRow) {

    $returnText = "";
    $result = sendApi("GET", "?product=producttypelist", "");
    $typeList = apiResultProcessing($result->status_code, $result->response_data);

    echo ($allRow) ? '<option value="0" '.ifSelected(0, $typevalue).'>összes</option>' : false;
    
    if ($typeList !== null) {
        foreach ($typeList as $type) {
            $returnText .= '<option value="'.$type->id.'" '.ifSelected($type->typename, $typevalue).'>'.$type->typename.'</option>';
        }
    }

    return $returnText;
}

function productTypenameLoad($typenum) {
    $result = sendApi("GET", "?product=producttypelist", "");
    $typeList = apiResultProcessing($result->status_code, $result->response_data);

    if ($typenum == "0") { return "összes"; }

    foreach ($typeList as $list) {
        if ($list->id == $typenum) {
            return $list->typename;
        }
    }
}

function ifSelected($listValue, $value) {
    if ($listValue == $value) {
        return "selected";
    }
}

?>