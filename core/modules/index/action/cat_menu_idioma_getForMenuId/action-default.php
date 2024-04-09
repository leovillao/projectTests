<?php
$idiomas = IdiomaData::getAll();
$men_id = $_POST['men_id'];

$arrayp = array();
foreach ($idiomas as $idioma) {
    $traduccion = MenuViewData::getByIdiomaAndMenuId($idioma->idm_id, $men_id);
    $array = array(
        'men_id' => $men_id,
        'idm_id' => $idioma->idm_id,
        'idm_nombre' => $idioma->idm_nombre,
        'mnv_nombre' => is_null($traduccion)?'':$traduccion->mnv_nombre,
        'mnv_descripcion' => is_null($traduccion)?'':$traduccion->mnv_descripcion,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);