<?php
$idiomas = IdiomaData::getAll();
$vwi_id = $_POST['vwi_id'];

$arrayp = array();
foreach ($idiomas as $idioma) {
    $traduccion = ViewObjectTextData::getByObjetIdAndIdiomaId($vwi_id, $idioma->idm_id);
    $array = array(
        'vwi_id' => $vwi_id,
        'idm_id' => $idioma->idm_id,
        'idm_nombre' => $idioma->idm_nombre,
        'vot_texto' => is_null($traduccion)?'':$traduccion->vot_texto
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);