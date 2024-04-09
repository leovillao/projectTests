<?php
$subcategorias = SubCategoriaData::getAllByEmpId($_SESSION['emp_id']);

$arrayp = array();
foreach ($subcategorias as $subcategoria) {
    $array = array(
        'id' =>$subcategoria->sbc_id,
        'nombre' => Encryption::decrypt($subcategoria->sbc_nombre),
        'cat_id' => $subcategoria->cat_id,
        'cat_nombre' => Encryption::decrypt($subcategoria->cat_nombre),
    );
    array_push($arrayp,$array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);