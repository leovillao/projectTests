<?php
$categoria = CategoryData::getById($_POST['id']);
$processFallo = false;
$msj = '';
if ($categoria->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $categoria::$Conx;
    $catname = $categoria->cat_nombre;

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , elimina el registro : " . $catname . "'";
    $addBit->bipage = "'cat_categorias'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $r = $categoria->del_t($conx1);
        $msj = "1-Registro eliminado con exito";
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