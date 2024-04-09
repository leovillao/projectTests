<?php
$empresa = EmpresaData::getById($_POST['emp_id']);

$prefijo = '';
$celular = '';

if($empresa->emp_cont_cel) {
    $emp_cont_cel = Encryption::decrypt($empresa->emp_cont_cel);
    $emp_cont_cel = explode('-', $emp_cont_cel);
    $prefijo = isset($emp_cont_cel[0])?$emp_cont_cel[0]:'';
    $celular = isset($emp_cont_cel[1])?$emp_cont_cel[1]:'';
}

$array = array(
    'id' => $empresa->emp_id,
    'nombre' => Encryption::decrypt($empresa->emp_nombre),
    'identificador_fiscal' => Encryption::decrypt($empresa->emp_idfiscal),
    'contacto_nombre' => Encryption::decrypt($empresa->emp_contacto),
    'contacto_email' => Encryption::decrypt($empresa->emp_cont_email),
    'contacto_prefijo' => $prefijo,
    'contacto_celular' => $celular,
    'pais_id' => $empresa->pai_id,
    'idioma_id' => $empresa->idm_id,
    'estado' => $empresa->emp_estado,
);

$s = new stdClass();
$s->data = $array;
echo json_encode($s,JSON_UNESCAPED_UNICODE);