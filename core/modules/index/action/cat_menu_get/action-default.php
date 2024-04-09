<?php
$menus = MenuData::getAll();

$arrayp = array();
foreach ($menus as $menu) {
    $array = array(
        'id' => $menu->men_id,
        'codigo' => $menu->men_view,
        'nombre' => $menu->men_nombre,
        'descripcion' => $menu->men_descripcion,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);