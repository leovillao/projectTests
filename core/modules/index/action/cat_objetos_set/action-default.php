<?php
$objeto = new ViewObjectData();
$processFallo = false;
$msj = '';
if ($objeto->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $objeto::$Conx;
    $vwi_codigo = $_POST['vwiCodigo'];
    $vwi_nombre = $_POST['vwiNombre'];
    $men_id = $_POST['menId'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , crea el registro : " . $vwi_codigo . "'";
    $addBit->bipage = "'cat_objetos'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }
    if ($processFallo == false) {
        $objeto->vwi_codigo = "'" . $vwi_codigo . "'";
        $objeto->vwi_nombre = "'" . $vwi_nombre . "'";
        $objeto->men_id = "'" . $men_id . "'";
        $r = $objeto->add_t($conx1);
        $msj = "1-Registro creado con exito";
        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $objeto->CancelarTransaccion($conx1);
}else{
    $objeto->CerrarTransaccion($conx1);
}

echo json_encode($msj, JSON_UNESCAPED_UNICODE);
