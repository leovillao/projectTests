<?php

$menuData = new MenuViewData();
$processFallo = false;
$msj = '';
if ($menuData->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $menuData::$Conx;
    $men_id = $_POST['men_id'];
    $idm_id = $_POST['idm_id'];
    $mnv_descripcion = $_POST['mnv_descripcion'];
    $mnv_nombre = $_POST['mnv_nombre'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . $menuData->men_id. " '";
    $addBit->bipage = "'cat_menu_data'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }
    
    if ($processFallo == false) {
        $menuDataExistente = MenuViewData::getByIdiomaAndMenuId($_POST['idm_id'], $_POST['men_id']);

        if(is_null($menuDataExistente )) {
            $menuDataNuevo = new MenuViewData();
            $menuDataNuevo->men_id = $men_id;
            $menuDataNuevo->idm_id = $idm_id;
            $menuDataNuevo->mnv_descripcion = $mnv_descripcion;
            $menuDataNuevo->mnv_nombre = $mnv_nombre;

            $r = $menuDataNuevo->add_t($conx1);
            
            $msj = "1-Registro actualizado con exito c";

            if ($r[0] == false) {
                $processFallo = true;
                $msj = "0-" . $r[2];
            }
        }
        else {
            $menuDataExistente->men_id = "'" . $men_id. "'";
            $menuDataExistente->idm_id = "'" . $idm_id . "'";
            $menuDataExistente->mnv_descripcion = "'" . $mnv_descripcion . "'";
            $menuDataExistente->mnv_nombre = "'" . $mnv_nombre . "'";
            
            $r = $menuDataExistente->update_t($conx1);

            $msj = "1-Registro actualizado con exito";
        }

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $menuData->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $menuData->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
