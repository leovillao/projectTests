<?php
$categoria = new CategoryData();
$processFallo = false;
$msj = '';
if ($categoria->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $categoria::$Conx;
    $cat_nombre = $_POST['nombreCat'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , crea el registro : " . $cat_nombre . "'";
    $addBit->bipage = "'cat_categorias'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }
    if ($processFallo == false) {
        $categoria->cat_nombre = "'" . Encryption::encrypt($cat_nombre) . "'";
        $categoria->emp_id = $_SESSION['emp_id'];
        $r = $categoria->add_t($conx1);
        $msj = "1-Registro creado con exito";
        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $categoria->CancelarTransaccion($conx1);
}else{
    $categoria->CerrarTransaccion($conx1);
}

echo json_encode($msj, JSON_UNESCAPED_UNICODE);
