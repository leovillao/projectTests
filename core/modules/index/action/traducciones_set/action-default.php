<?php

$objectText = new ViewObjectTextData();
$processFallo = false;
$msj = '';
if ($objectText->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $objectText::$Conx;
    $vwi_id = $_POST['vwi_id'];
    $idm_id = $_POST['idm_id'];
    $vot_texto = $_POST['vot_texto'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . $objectText->vwi_id. " '";
    $addBit->bipage = "'cat_objetos'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    
    if ($processFallo == false) {
        $objectTextExistente = ViewObjectTextData::getByObjetIdAndIdiomaId($_POST['vwi_id'], $_POST['idm_id']);



        if(is_null($objectTextExistente )) {
            $objectTextNuevo = new ViewObjectTextData();
            $objectTextNuevo->vwi_id = $vwi_id;
            $objectTextNuevo->idm_id = $idm_id;
            $objectTextNuevo->vot_texto = $vot_texto;

            $r = $objectTextNuevo->add($conx1);
            
            $msj = "1-Registro actualizado con exito c";

            if ($r[0] == false) {
                $processFallo = true;
                $msj = "0-" . $r[2];
            }
        }
        else {
            $objectTextExistente->vwi_id = "'" . $vwi_id. "'";
            $objectTextExistente->idm_id = "'" . $idm_id . "'";
            $objectTextExistente->vot_texto = "'" . $vot_texto . "'";
            
            $r = $objectTextExistente->update_t($conx1);

            $msj = "1-Registro actualizado con exito";
        }
        
        
        

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $objectText->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $objectText->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
