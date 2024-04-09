<?php
$usuario = new UserData();
$processFallo = false;
$msj = '';
if ($usuario->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $usuario::$Conx;
    $usr_perfil = $_POST['usr_perfil'];
    $usr_nombre = Encryption::encrypt($_POST['usr_nombre']);
    $usr_user = $_POST['usr_user'];
    $usr_psw = sha1(md5($_POST['usr_psw']));
    $usr_ultimoUpdate = date("Y-m-d H:i:s");
    $usr_email = Encryption::encrypt($_POST['usr_email']);
    $usr_numcel = Encryption::encrypt($_POST['usr_numcel']);
    $usr_caducapsw = $_POST['usr_caducapsw'];
    $usr_periodo = $_POST['usr_periodo'];
    $usr_accesoxdia = $_POST['usr_accesoxdia'];
    $usr_dias1_7 = $_POST['usr_dias1_7'];
    $usr_rangohorario = $_POST['usr_rangohorario'];
    $usr_rangodesde = $_POST['usr_rangodesde'];
    $usr_rangohasta = $_POST['usr_rangohasta'];
    $usr_controlpais = $_POST['usr_controlpais'];
    $usr_paisespermitidos = $_POST['usr_paisespermitidos'];
    $usr_id_create = $_SESSION['user_id'];
    $usr_id_update = $_SESSION['user_id'];
    $usr_estado = $_POST['usr_estado'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , crea el registro : " . $_POST['usr_nombre'] . "'";
    $addBit->bipage = "'cat_usuarios'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }
    if ($processFallo == false) {
        $usuario->usr_perfil = "'" . $usr_perfil. "'";
        $usuario->usr_nombre = "'" . $usr_nombre. "'";
        $usuario->usr_user = "'" . $usr_user. "'";
        $usuario->usr_psw = "'" . $usr_psw. "'";
        $usuario->usr_ultimoUpdate = "'" . $usr_ultimoUpdate. "'";
        $usuario->usr_email = "'" . $usr_email. "'";
        $usuario->usr_numcel = "'" . $usr_numcel. "'";
        $usuario->usr_caducapsw = "'" . $usr_caducapsw. "'";
        $usuario->usr_periodo = $usr_periodo;
        $usuario->usr_accesoxdia = "'" . $usr_accesoxdia. "'";
        $usuario->usr_dias1_7 = "'" . $usr_dias1_7. "'";
        $usuario->usr_rangohorario = "'" . $usr_rangohorario. "'";
        $usuario->usr_rangodesde = "'" . $usr_rangodesde. "'";
        $usuario->usr_rangohasta = "'" . $usr_rangohasta. "'";
        $usuario->usr_controlpais = "'" . $usr_controlpais. "'";
        $usuario->usr_paisespermitidos = "'" . $usr_paisespermitidos. "'";
        $usuario->usr_id_create = "'" . $usr_id_create. "'";
        $usuario->usr_id_update = "'" . $usr_id_update. "'";
        $usuario->usr_estado = "'" . $usr_estado. "'";
        $usuario->emp_id = $_SESSION['emp_id'];
        $r = $usuario->add_t($conx1);
        $msj = "1-Registro creado con exito";
        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $usuario->CancelarTransaccion($conx1);
}else{
    $usuario->CerrarTransaccion($conx1);
}

echo json_encode($msj, JSON_UNESCAPED_UNICODE);
