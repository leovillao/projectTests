<?php
$traducciones = ViewObjectTextData::getByIdiomaId($_SESSION['idm_id']);

$data = array();

if ($_SESSION['idm_codigo'] == 'en') {
    $data['datatable.idioma'] = 'English';
}
else if ($_SESSION['idm_codigo'] == 'fr') {
    $data['datatable.idioma'] = 'French';
}
else {
    $data['datatable.idioma'] = 'Spanish';
}

foreach($traducciones as $traduccion) {
    $clave = $traduccion->vwi_codigo;
    $valor = $traduccion->vot_texto;
    $data[$clave] = $valor;
}

echo $json = json_encode($data);
?>