<?php
$pais = new PaisData();
$processFallo = false;
$msj = '';
if ($pais->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $pais::$Conx;
    $pai_codigo = $_POST['paiCodigo'];
    $pai_nombre = $_POST['paiNombre'];
    $pai_prefijo = $_POST['paiPrefijo'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , crea el registro : " . $pai_nombre . "'";
    $addBit->bipage = "'cat_paises'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }
    
    if ($processFallo == false) {
        $pais->pai_codigo = "'" . $pai_codigo . "'";
        $pais->pai_nombre = "'" . $pai_nombre . "'";
        $pais->pai_prefijo = "'" . $pai_prefijo . "'";
        $r = $pais->add_t($conx1);
        $msj = "1-Registro creado con exito";
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
