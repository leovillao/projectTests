<?php
$subcategoria = SubCategoriaData::getById($_POST['idSbc']);
$processFallo = false;
$msj = '';
if ($subcategoria->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $subcategoria::$Conx;
    $sbc_nombre = $_POST['nombreSbc'];
    $cat_id = $_POST['idCat'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . Encryption::decrypt($subcategoria->sbc_nombre) . " por el registro : " . $sbc_nombre . " '";
    $addBit->bipage = "'cat_subcategorias'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $subcategoria->sbc_nombre = "'" . Encryption::encrypt($sbc_nombre) . "'";
        $subcategoria->cat_id = $cat_id;
        $r = $subcategoria->update_t($conx1);
        $msj = "1-Registro actualizado con exito";

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $subcategoria->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $subcategoria->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
