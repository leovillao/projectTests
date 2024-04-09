<?php
$categorias = CategoryData::getAllByEmpId($_SESSION['emp_id']);

$arrayp = array();
foreach ($categorias as $categoria) {
    $array = array(
        'id' =>$categoria->cat_id,
        'nombre' => Encryption::decrypt($categoria->cat_nombre),
    );
    array_push($arrayp,$array);
}
$s = new stdClass();
$s->data = $arrayp;
echo json_encode($s,JSON_UNESCAPED_UNICODE);