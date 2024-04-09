<?php
$idioma = IdiomaData::getById($_POST['idmId']);
$processFallo = false;
$msj = '';
if ($idioma->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $idioma::$Conx;
    $idm_nombre = $_POST['idmNombre'];
    $idm_codigo = $_POST['idmCodigo'];
    $idm_estado = $_POST['idmEstado'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . $idioma->idm_id. " '";
    $addBit->bipage = "'cat_idiomas'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $idioma->idm_nombre = "'" . $idm_nombre. "'";
        $idioma->idm_codigo = "'" . $idm_codigo . "'";
        $idioma->idm_estado = "'" . $idm_estado . "'";
        $r = $idioma->update_t($conx1);
        $msj = "1-Registro actualizado con exito";

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $idioma->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $idioma->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
