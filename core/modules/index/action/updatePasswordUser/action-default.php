<?php
if (isset($_POST['pass'])) {
    $msj = "1-Password actualizada con exito.";
    $d = new UserData();
    $d->password = sha1(md5($_POST['pass']));
    $d->id = $_SESSION['user_id'];
    $re = $d->updatePassSesion();
    if ($re[0] == false) {
        $msj = "0-" . $re[2];
    }else{
        unset($_SESSION['onSesion']);
    }
    echo json_encode($msj);
}