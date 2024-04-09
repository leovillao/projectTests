<?php
$where = ' where date(bicreate_at) between "'.$_POST['desde'].'" and "'.$_POST['hasta'].'" ';

if (!empty($_POST['usuario'])) {
    $where .= ' and user_id = ' . $_POST['usuario'];
}

$bicatoras = BitacoraData::getAllForWhere($where);
$arrayp = array();
foreach ($bicatoras as $bitacora) {
    $array = array(
        'usuario' => UserData::getById($bitacora->user_id)->usr_nombre,
        'accion' => $bitacora->biaccion,
        'fecha' => $bitacora->bicreate_at,
        'pagina' => $bitacora->bipage,
    );
    array_push($arrayp,$array);
}
$d = new stdClass();
$d->data = $arrayp;
echo json_encode($d);