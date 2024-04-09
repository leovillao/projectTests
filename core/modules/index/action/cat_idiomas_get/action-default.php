<?php
$idiomas = IdiomaData::getAll();

$arrayp = array();
foreach ($idiomas as $idioma) {
    $array = array(
        'id' => $idioma->idm_id,
        'nombre' => $idioma->idm_nombre,
        'codigo' => $idioma->idm_codigo,
        'estado' => $idioma->idm_estado,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);