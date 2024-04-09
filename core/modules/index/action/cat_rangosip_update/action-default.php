<?php
$rangoip = RangoIpData::getById($_POST['ripId']);
$processFallo = false;
$msj = '';
if ($rangoip->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $rangoip::$Conx;
    $pai_id = $_POST['paiId'];
    $rip_rangoini = $_POST['ripRangoInicial'];
    $rip_rangofin = $_POST['ripRangoFinal'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . $rangoip->pai_id. " '";
    $addBit->bipage = "'cat_rangosip'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $rangoip->pai_id = "'" . $pai_id . "'";
        $rangoip->rip_rangoini = "'" . $rip_rangoini . "'";
        $rangoip->rip_rangofin = "'" . $rip_rangofin . "'";
        $r = $rangoip->update_t($conx1);
        $msj = "1-Registro actualizado con exito";

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $rangoip->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $rangoip->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
