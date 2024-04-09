<?php
$rangosip = RangoIpData::getAll();

$arrayp = array();
foreach ($rangosip as $rangoip) {
    $array = array(
        'id' => $rangoip->rip_id,
        'pai_id' => $rangoip->pai_id,
        'pai_nombre' => $rangoip->pai_nombre,
        'rango_inicial' => $rangoip->rip_rangoini,
        'rango_final' => $rangoip->rip_rangofin,
    );
    array_push($arrayp, $array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);