<?php
$objetos = ViewObjectData::getAll();

$arrayp = array();
foreach ($objetos as $objeto) {
    $array = array(
        'id' => $objeto->vwi_id,
        'codigo' => $objeto->vwi_codigo,
        'nombre' => $objeto->vwi_nombre,
        'men_id' => $objeto->men_id,
        'men_nombre' => $objeto->men_nombre,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);