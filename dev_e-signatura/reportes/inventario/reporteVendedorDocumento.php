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
require '../../core/modules/index/model/FData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/VendedorData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VwinfventasData.php';
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
$where .= " and fi_tipo = '01 '";

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
        $where .= ' and setq_id in ('.implode(",",$_POST['etiquetas']).') '  ;
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
$ventas = VwinfventasData::getDataDefault($where);
$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['fechaDesde'] . ', Hasta : ' . $_POST['fechaHasta'], 0, 1, 'L', 0, 0);
//$pdf->Cell(190, 7, 'Reporte Productos', 0, 1, 'L', 0, 0);
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
/**/
foreach ($ventas as $venta) {
    if (empty($nameSucursal)) {
        $nameSucursal = $venta->suname;
    }
    $vendedor = 0;
    if ($venta->veid != $vendedor) {
        if ($venta->fi_ptoemi != $emision) {
            if ($totalFP > 0) {
                $pdf->Cell(190, 6, "Subtotal " . utf8_decode("dd") . " : $ " . $totalFP, 'T,B,R,L', 1, 'R', 1, 1);
                $totalFP = 0;
                $nmfp = "";
            }
            if ($totalNeto > 0) {
                $pdf->SetFillColor(231, 229, 229);
                $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
                $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(55, $alto, 'Subtotal sucursal ' . utf8_decode($nameSucursal), 'T,B,R', 0, 'R', 1, 0);
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivas), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivan), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalSubt), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalDesc), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalIva), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalNeto), 'T,B,R', 1, 'R', 1, 0);

                $totalivas = 0;
                $totalivan = 0;
                $totalSubt = 0;
                $totalDesc = 0;
                $totalIva = 0;
                $totalNeto = 0;

                $totalNetoV = 0;
                $totalIvaV = 0;
                $totalDescV = 0;
                $totalSubtV = 0;
                $totalivanV = 0;
                $totalivasV = 0;
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'B', 7); // titulos
            $pdf->Cell(189, $alto, utf8_decode("SUCURSAL " . utf8_decode($nameSucursal)), 'L,T,B,R', 1, 'L', 0, 0);
            $pdf->Cell(189, $alto, utf8_decode("VENDEDOR " . utf8_decode(VendedorData::getById($venta->veid)->venombre)), 'L,T,B,R', 1, 'L', 0, 0);
            $pdf->Cell(189, $alto, utf8_decode("PUNTO EMISIÓN " . $venta->fi_codestab . '-' . $venta->fi_ptoemi), 'L,T,B,R', 1, 'C', 0, 0);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(55, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Desc', 'T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(16, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 1, 0);
            $emision = $venta->fi_ptoemi;
            $nameSucursal = $venta->suname;
        }
        $vendedor = $venta->veid;
        $vendedorName = VendedorData::getById($venta->veid)->venombre;
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
    $pdf->Cell(55, $alto, substr(utf8_decode(ucwords(strtolower($venta->fi_er_name))) . $anulado, 0, 46), 'T,B,R', 0, 'L', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_ivasi) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_ivano) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_subtotal) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_desc) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_iva) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? FData::formatoNumeroReportes($venta->fi_neto) : 0.00, 'T,B,R', 1, 'R', 0, 0);

    $pdf->SetFont('Arial', '', 7); // titulos

    $totalNetoV = $totalNetoV + $venta->fi_neto;
    $totalIvaV = $totalIvaV + $venta->fi_iva;
    $totalDescV = $totalDescV + $venta->fi_desc;
    $totalSubtV = $totalSubtV + $venta->fi_subtotal;
    $totalivanV = $totalivanV + $venta->fi_ivano;
    $totalivasV = $totalivasV + $venta->fi_ivasi;

    /*SUBTOTALES SUCURSAL*/
    $totalNeto = $totalNeto + $venta->fi_neto;
    $totalIva = $totalIva + $venta->fi_iva;
    $totalDesc = $totalDesc + $venta->fi_desc;
    $totalSubt = $totalSubt + $venta->fi_subtotal;
    $totalivan = $totalivan + $venta->fi_ivano;
    $totalivas = $totalivas + $venta->fi_ivasi;

    /*TOTALES SUCURSAL*/
    $tiva = $tiva + $venta->fi_ivasi;
    $tivan = $tivan + $venta->fi_ivano;
    $subt = $subt + $venta->fi_subtotal;
    $desc = $desc + $venta->fi_desc;
    $iva = $iva + $venta->fi_iva;
    $neto = $neto + $venta->fi_neto;

}
if ($totalNetoV >= 0) {
    $pdf->SetFillColor(231, 229, 229);
    $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
    $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(55, $alto, 'SUBTOTAL VENDEDOR : ' . utf8_decode($vendedorName), 'T,B,R', 0, 'R', 1, 0);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalNetoV), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalIvaV), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalDescV), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalSubtV), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivanV), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivasV), 'T,B,R', 1, 'R', 1, 0);
}
if ($totalNeto >= 0) {
    $pdf->SetFillColor(231, 229, 229);
    $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
    $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(55, $alto, 'SUBTOTAL SUCURSAL : ' . utf8_decode($nameSucursal), 'T,B,R', 0, 'R', 1, 0);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivas), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalivan), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalSubt), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalDesc), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalIva), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalNeto), 'T,B,R', 1, 'R', 1, 0);
}
//160, 160, 160
$pdf->SetFillColor(160, 160, 160);
$pdf->SetTextColor(0, 0, 0); // color negro
$pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
$pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(55, $alto, 'TOTALES ', 'T,B,R', 0, 'R', 1, 0);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($tiva), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($tivan), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($subt), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($desc), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($iva), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($neto), 'T,B,R', 1, 'R', 1, 0);
$pdf->Ln();


$pdf->Output();
