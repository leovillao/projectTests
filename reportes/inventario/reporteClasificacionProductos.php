<?php
//var_export($_POST);
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VwinfProductosData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require 'CabeceraReporte.php';
$where = 'where fi_fechadoc between "' . $_POST['fechaDesde'] . '" and "' . $_POST['fechaHasta'] . '"';
if ($_POST['secuencia'] != 0) { // pto de emision
    $sec = SecuenciaData::getById($_POST['secuencia']);
    $where .= 'and fi_codestab = "' . $sec->estab . '" and fi_ptoemi = "' . $sec->emision . '"';
}
if ($_POST['estado'] == 1) { // todos exepto anulados
    $where .= ' and fi_estado <> 3';
} elseif ($_POST['estado'] == 2) { // solo anulados
    $where .= ' and fi_estado = 3';
} // todos los documentos
if (!empty($_POST['cierre'])) { // por cierre de caja
    $where .= ' and box_id = ' . $_POST['cierre'];
}
if ($_POST["optionsRadios"] == "C") {
    if (isset($_POST['cliente']) && $_POST['cliente'] != 0) { // por cliente
        $where .= ' and ceid = ' . $_POST['cliente'];
    }
} elseif ($_POST["optionsRadios"] == "E") {
    if ((isset($_POST['etiquetas']) && !empty($_POST['etiquetas']))) {
        $where .= ' and subetqid in ('.implode(",",$_POST['etiquetas']).') '  ;
    }
}
if ($_POST['sucursal'] != 0) { // por cliente
    $where .= ' and sucursal_id = ' . $_POST['sucursal'];
}
if ($_POST['vendedor'] != 0) { // por vendedor
    $where .= ' and veid = ' . $_POST['vendedor'];
}
if ($_POST['ciudad'] != 0) { // por ciudad
    $where .= ' and city_id = ' . $_POST['ciudad'];
}
if ($_POST['provincia'] != 0) { // por provincia
    $where .= ' and prov_id = ' . $_POST['provincia'];
}
if ($_POST['pais'] != 0) { // por pais
    $where .= ' and pais_id = ' . $_POST['pais'];
}
$where .= ' and fi_tipo = "01" ';

$ventas = VwinfProductosData::getDataProductClasificacion($where);
$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['fechaDesde'] . ', Hasta : ' . $_POST['fechaHasta'], 0, 1, 'L', 0, 0);
$pdf->SetFont('Arial', 'B', 12); // titulos
//$pdf->Cell(190, 7, 'Cierre # ' . $_POST['cierre'], 0, 1, 'C', 0, 0);
/*=================
cabecera de reporte
=================*/
$pdf->SetFont('Arial', '', 9); // titulos
$altoTitulo = 8;
$alto = 6;
/**/
$nameSucursal = "";
$emision = 0;
$sucName = "";
$totalST = "";
/**/
$tiva = 0;
$tivan = 0;
$subt = 0;
$desc = 0;
$iva = 0;
$neto = 0;
/**/
$totalNeto = 0;
$totalIva = 0;
$totalDesc = 0;
$totalSubt = 0;
$totalivan = 0;
$totalivas = 0;
foreach ($ventas as $venta) {
    if (empty($nameCategoria)) {
        $nameCategoria = $venta->ctdescription;
    }
    if ($venta->ct2description != $subcategoria) {
        if ($totalNeto > 0) {
            $pdf->SetFillColor(231, 229, 229);
            $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
            $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(55, $alto, 'SUBTOTAL SUBCATEGORIA : ' . utf8_decode($subcategoria), 'T,B,R', 0, 'R', 1, 0);
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, number_format($totalST, 2), 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, number_format($totalIva, 2), 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, number_format($totalNeto, 2), 'T,B,R', 1, 'R', 1, 0);
            $totalST = 0;
            $totalIva = 0;
            $totalNeto = 0;
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 7); // titulos

        $pdf->Cell(189, $alto, utf8_decode("CATEGORIA : " . utf8_decode($nameCategoria)), 'L,T,B,R', 1, 'L', 0, 0);
        $pdf->Cell(189, $alto, utf8_decode("SUBCATEGORIA : " . $venta->ct2description), 'L,T,B,R', 1, 'L', 0, 0);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(15, $altoTitulo, 'Fecha', 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(55, $altoTitulo, 'Producto', 'T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Unidad', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Cantidad', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Precio', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 1, 0);
        $subcategoria = $venta->ct2description;
        $nameCategoria = $venta->ctdescription;
    }
    if ($venta->fi_estado != 3) {
        $pdf->SetTextColor(0, 0, 0); // color rojo
        $anulado = "";
    } else {
        $anulado = " - ANULADO";
        $pdf->SetTextColor(255, 8, 0); // color rojo
    }
    $pdf->SetFont('Arial', '', 7); // titulos
    $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'L,T,B,R', 0, 'L', 0, 0);
    $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
    $pdf->SetFont('Arial', '', 5); // titulos
    $pdf->Cell(55, $alto, substr(ProductData::getById($venta->itid)->itname . '-' . utf8_decode(ucwords(strtolower($venta->itname))) . $anulado, 0, 46), 'T,B,R', 0, 'L', 0, 0);
    $pdf->SetFont('Arial', '', 6); // titulos
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->undescrip : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 7); // titulos
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? number_format($venta->odcandig, 2) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? number_format($venta->odpvp, 2) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? number_format($venta->odsubtotal, 2) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? number_format($venta->odiva, 2) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? number_format($venta->odtotal, 2) : 0.00, 'T,B,R', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 7); // titulos
    /** TOTALES DE LA CATEGORIA */
    $totalST = $totalST + $venta->odsubtotal;
    $totalIva = $totalIva + $venta->odiva;
    $totalNeto = $totalNeto + $venta->odtotal;
    /**/
    $subt = $subt + $venta->odsubtotal;
    $iva = $iva + $venta->odiva;
    $neto = $neto + $venta->odtotal;
}
if ($totalNeto >= 0) {
    $pdf->SetFillColor(231, 229, 229);
    $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
    $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(55, $alto, 'SUBTOTAL SUBCATEGORIA : ' . utf8_decode($subcategoria), 'T,B,R', 0, 'R', 1, 0);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, number_format($totalST, 2), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, number_format($totalIva, 2), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, number_format($totalNeto, 2), 'T,B,R', 1, 'R', 1, 0);
}
//160, 160, 160
$pdf->SetFillColor(160, 160, 160);
$pdf->SetTextColor(0, 0, 0); // color negro
$pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
$pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(55, $alto, 'TOTAL CATEGORIAS ', 'T,B,R', 0, 'R', 1, 0);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, number_format($subt, 2), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, number_format($iva, 2), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, number_format($neto, 2), 'T,B,R', 1, 'R', 1, 0);
$pdf->Ln();
$pdf->Output();
