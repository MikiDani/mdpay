<div class="text-center mb-2"><h4>Felhasználók</h4></div>
<?php

$apitext = "";

if (isset($_POST['userdelid'])) {
    
    if (isset($_POST['delcheckbox']) && ($_POST['delcheckbox'] == 'on')) {
        
        $data = ["token" => $_SESSION["login"], "userid" => $_POST['userdelid']];
        $deleteUser = sendApi('DELETE', '?user=userdelete', $data);
        
        if ($deleteUser->status_code == 200 || $deleteUser->status_code == 201) {
            unset($_POST['accordion-num']);
            unset($_POST['delcheckbox']);
        } else {
            $apitext = $deleteUser->response_data;
        }
    }   
}

if (isset($_POST['rankmodbutton'])) {

    $data = ["token" => $_SESSION['login'], "userrank" => $_POST['rankselect'], "userid" => $_POST['userid']];
    $result = sendApi('POST', '?user=usermod', $data);
    
    unset($_POST['rankselect']);
    unset($_POST['userid']);
    $apitext = $result->response_data;
}

$data = ["token" => $_SESSION['login']];
$result = sendApi('GET', '?user=allusers', $data);

if ($result->status_code==200) {
    if ($result->response_data !== null) {
        echo "<div class='accordion accordion-flush m-0 p-0' id='accordionFlushExample'>";
        $num = 0;
        foreach ($result->response_data as $users) {
            $num ++;
            echo '<div class="accordion-item"><h2 class="accordion-header bg-primary" id="flush-heading'.$num.'"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse'.$num.'" aria-expanded="false" aria-controls="flush-collapse'.$num.'">'.$num.'. '.$users->username.' | '.$users->useremail.' | '.rankValue($users->userrank).'</button></h2><div id="flush-collapse'.$num.'" class="accordion-collapse collapse '.ifShow($num).'" aria-labelledby="flush-heading'.$num.'" data-bs-parent="#accordionFlushExample"><div class="accordion-body">';
            
            echo '<table class="table">
            <td></td>
            <td>id:</td>
            <td>'.$users->id.'</td>
            <tr>
                <td></td><td>rang:</td>
                <td>
                <form method="post" action="index.php">
                    <input type="hidden" name="userid" value="'.$users->id.'">
                    <input type="hidden" name="accordion-num" value="'.$num.'">
                    <select class="form-control d-inline w-50" name="rankselect">
                        <option value="0" '.ifSelectedRank($users->userrank).'>felhasználó</option>
                        <option value="1" '.ifSelectedRank($users->userrank).'>admin</option>
                    </select>
                    <button type="submit" name="rankmodbutton" class="btn btn-success btn-sm w-25">módosítás</button>
                </form>
                </td>
            </tr>
            <tr>
                <td></td><td>felhasználónév:</td><td><strong>'.$users->username.'</strong></td></tr><tr>
                <td></td>
                <td>email:</td>
                <td><a href="mailto:'.$users->useremail.'" target="_blank">'.$users->useremail.'</a></td>
            </tr>
            <tr>
                <td colspan="3" class="text-center">Információk:</td></tr><tr><td colspan="3">'.$users->userinfo.'</td>
            </tr>
            </table>';

            echo ifMessage($num, $apitext);

            echo '<form class="d-flex justify-content-center align-items-center" method="post" action="index.php"><input class="form-check-input me-2" type="checkbox" name="delcheckbox"><input type="hidden" name="accordion-num" value="'.$num.'"><button name="userdelid" value="'.$users->id.'" class="btn btn-danger btn-sm">Felhasználó törlése</button></form>';

            echo '</div></div></div>'; // Accordion close
        }
        echo "</div>";
    } else { echo "<div class='text-center text-warning'>Nincsen találat.</div>"; }
} else { unauthorized(); }

function rankValue($rank) {
    $name = "";
    if ($rank == 0) { $name = "felhasználó"; }
    if ($rank == 1) { $name = "admin"; }
    return $name;
}

function ifSelectedRank($rank) {
    $selected = "";
    if ($rank == 1) { $selected = "selected"; }
    return $selected;
}

?>