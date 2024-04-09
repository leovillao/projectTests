<?php
$usuario = UserData::getById($_POST['usr_id']);

$prefijo = '';
$celular = '';

if($usuario->usr_numcel) {
    $usr_numcel = Encryption::decrypt($usuario->usr_numcel);
    $usr_numcel = explode('-', $usr_numcel);
    $prefijo = $usr_numcel[0];
    $celular = $usr_numcel[1];
}

$usr_dias1_7 = $usuario->usr_dias1_7;

$dias = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo");

$acceso_dias = array();

for ($i = 0; $i < strlen($usr_dias1_7); $i++) {
    $acceso_value = ($usr_dias1_7[$i] === "S") ? true : false;

    $acceso_object = array(
        "dia" => $dias[$i],
        "acceso" => $acceso_value
    );

    array_push($acceso_dias, $acceso_object);
}

$paises_permitidos = [];

if($usuario->usr_paisespermitidos) {
    $usr_paisespermitidos = $usuario->usr_paisespermitidos;

    $idsArray = explode(',', $usr_paisespermitidos);

    foreach ($idsArray as $id) {
        $pais = PaisData::getById($id);
        $paises_permitidos[] = ['value' => $id, 'label' => $pais->pai_nombre];
    }
}

$array = array(
    'id' => $usuario->usr_id,
    'perfil' => $usuario->usr_perfil,
    'nombre' => Encryption::decrypt($usuario->usr_nombre),
    'nombre_usuario' => $usuario->usr_user,
    'password' => '',
    'prefijo' => $prefijo,
    'celular' => $celular,
    'email' => Encryption::decrypt($usuario->usr_email),
    'is_caduca_password' => $usuario->usr_caducapsw=='S'?true:false,
    'dias_caduca_password' => $usuario->usr_periodo,
    'is_acceso_x_dia' => $usuario->usr_accesoxdia=='S'?true:false,
    'acceso_dias' => $acceso_dias,
    'is_acceso_horas' => $usuario->usr_rangohorario=='S'?true:false,
    'acceso_hora_inicio' => $usuario->usr_rangodesde,
    'acceso_hora_fin' => $usuario->usr_rangohasta,
    'is_acceso_control_pais' => $usuario->usr_controlpais=='S'?true:false,
    'paises_permitidos' => $paises_permitidos,
    'estado' => $usuario->usr_estado=="1"?true:false,
);

$s = new stdClass();
$s->data = $array;
echo json_encode($s,JSON_UNESCAPED_UNICODE);