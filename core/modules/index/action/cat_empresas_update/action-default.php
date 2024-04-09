<?php
$empresa = EmpresaData::getById($_POST['emp_id']);
$processFallo = false;
$msj = '';
if ($empresa->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $empresa::$Conx;
    $emp_nombre = Encryption::encrypt($_POST['emp_nombre']);
    $emp_idfiscal = Encryption::encrypt($_POST['emp_idfiscal']);
    $emp_contacto = Encryption::encrypt($_POST['emp_contacto']);
    $emp_cont_email = Encryption::encrypt($_POST['emp_cont_email']);
    $emp_cont_cel = Encryption::encrypt($_POST['emp_cont_cel']);
    $pai_id = $_POST['pai_id'];
    $idm_id = $_POST['idm_id'];
    $emp_estado = $_POST['emp_estado'];

    $addBit = new BitacoraData();
    $addBit->user_id = $_SESSION['user_id'];
    $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , realiza actualizacion de registro : " . $empresa->emp_id . " '";
    $addBit->bipage = "'cat_empresas'";
    $addBit->biciclo = 1;
    $ad = $addBit->add_t($conx1);
    if ($ad[0] == false) {
        $processFallo = true;
        $msj = "0-" . $ad[2];
    }

    if ($processFallo == false) {
        $empresa->emp_nombre = "'" . $emp_nombre. "'";
        $empresa->emp_idfiscal = "'" . $emp_idfiscal. "'";
        $empresa->emp_contacto = "'" . $emp_contacto. "'";
        $empresa->emp_cont_email = "'" . $emp_cont_email. "'";
        $empresa->emp_cont_cel = "'" . $emp_cont_cel. "'";
        $empresa->pai_id = "'" . $pai_id. "'";
        $empresa->idm_id = "'" . $idm_id. "'";
        $empresa->emp_estado = "'" . $emp_estado. "'";

        $r = $empresa->update_t($conx1);
        $msj = "1-Registro actualizado con exito";

        if ($r[0] == false) {
            $processFallo = true;
            $msj = "0-" . $r[2];
        }
    }
}
if ($processFallo == true) {
    $empresa->CancelarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
} else {
    $empresa->CerrarTransaccion($conx1);
    echo json_encode($msj, JSON_UNESCAPED_UNICODE);
}
