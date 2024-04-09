<?php
$pais = PaisData::getById($_POST['id']);
$processFallo = false;
$msj = '';
if ($pais->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $pais::$Conx;
    $pai_nombre = $pais->pai_nombre;

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , elimina el registro : " . $pai_nombre . "'";
    $addBit->bipage = "'cat_paises'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $r = $pais->del_t($conx1);
        $msj = "1-Registro eliminado con exito";
        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $pais->CancelarTransaccion($conx1);
}else{
    $pais->CerrarTransaccion($conx1);
}
echo json_encode($msj, JSON_UNESCAPED_UNICODE);