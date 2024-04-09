<?php
$usuarios = UserData::getAllByEmpId($_SESSION['emp_id']);

$arrayp = array();
foreach ($usuarios as $usuario) {
    $array = array(
        'id' => $usuario->usr_id,
        'nombre' => Encryption::decrypt($usuario->usr_nombre),
        'usuario_login' => $usuario->usr_user,
        'email' => Encryption::decrypt($usuario->usr_email),
        'estado' => $usuario->usr_estado,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);