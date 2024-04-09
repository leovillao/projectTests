<?php
$usuario = UserData::getById($_POST['id']);
$processFallo = false;
$msj = '';
if ($usuario->AbrirTransaccion() == false) {
    $processFallo = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $usuario::$Conx;

    $user_id = $_POST['id'];

    $count_bitacora = BitacoraData::getByUsuarioId($user_id);

    if(count($count_bitacora) > 0) {
        $processFallo = true;
        $msj = "0-Usuario contiene registros, no se puede eliminar";
    }
    else {
        $addBit = new BitacoraData();
        $addBit->user_id = $_SESSION['user_id'];
        $addBit->biaccion = "'Usuario " . UserData::getById($_SESSION['user_id'])->usr_user . " , elimina el registro : " . $user_id . "'";
        $addBit->bipage = "'cat_usuarios'";
        $addBit->biciclo = 1;
        $ad = $addBit->add_t($conx1);
        if ($ad[0] == false) {
            $processFallo = true;
            $msj = "0-" . $ad[2];
        }

        if ($processFallo == false) {
            $r = $usuario->del_t($conx1);
            $msj = "1-Registro eliminado con exito";
            if ($r[0] == false) {
                $processFallo = true;
                $msj = "0-" . $r[2];
            }
        }
    }

    
}
if ($processFallo == true) {
    $usuario->CancelarTransaccion($conx1);
}else{
    $usuario->CerrarTransaccion($conx1);
}
echo json_encode($msj, JSON_UNESCAPED_UNICODE);