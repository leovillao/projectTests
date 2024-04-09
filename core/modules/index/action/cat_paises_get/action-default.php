<?php
$paises = PaisData::getAll();

$arrayp = array();
foreach ($paises as $pais) {
    $array = array(
        'id' => $pais->pai_id,
        'codigo' => $pais->pai_codigo,
        'nombre' => $pais->pai_nombre,
        'prefijo' => $pais->pai_prefijo,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);