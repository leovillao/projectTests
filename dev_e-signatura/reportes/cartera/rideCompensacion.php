<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';

require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CrucedeudasData.php';
require '../../core/modules/index/model/CruceanticiposData.php';
require '../../core/modules/index/model/DeudasData.php';

require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require 'cabeceraCartera.php';
//require '../../core/controller/Fpdf/code128.php';


$cPagos = CrucecabData::getById($_GET['id']);
$anPagos = CruceanticiposData::getByCrId($_GET['id']);
$dePagos = CrucedeudasData::getByCrId($_GET['id']);

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(140, 6, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 0, 0);
$pdf->Cell(40, 5.5, utf8_decode('Compensación # ') . $_GET['id'], 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 5.5, 'Ruc:' . $_SESSION['ruc'], 0, 1);
$pdf->Cell(150, 5.5, 'Fecha de Emision :' . $cPagos->crfecha, 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(65, 5.5, 'Total : $ ' . $cPagos->crtotal, 0, 1);
$pdf->SetFont('Arial', '', 10);


$pdf->ln(5);
$pdf->SetFont('Arial', 'B', 13);
$pdf->cell(190, 10, 'ANTICIPOS', 'L,T,B,R', 1, 'L', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(63, 5.5, '# Documento',  'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(63, 5.5, 'Fecha',  'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(64, 5.5, 'Total',  'L,T,B,R', 1, 'R', 0, 0);
$totalAnt = 0;
foreach ($anPagos as $anPago){
    $pdf->Cell(63, 5.5, $anPago->anid,  'L,T,B,R', 0, 'C', 0, 0);
    $pdf->cell(63, 5.5, AnticipocabData::getById($anPago->anid)->anfecha,  'L,T,B,R', 0, 'C', 0, 0);
    $pdf->cell(64, 5.5, $anPago->cavalor,  'L,T,B,R', 1, 'R', 0, 0);
    $totalAnt = $totalAnt + $anPago->cavalor;
}
    $pdf->ln(1);
    $pdf->Cell(63, 5.5, '',  '', 0, 'C', 0, 0);
    $pdf->cell(63, 5.5, '',  '', 0, 'C', 0, 0);
    $pdf->SetFillColor(222,222,222);
    $pdf->cell(64, 5.5, $totalAnt,  '', 1, 'R', 1, 0);

$pdf->ln(5);
$pdf->SetFont('Arial', 'B', 13);
$pdf->cell(190, 10, 'DOCUMENTOS DEUDA', 'L,T,B,R', 1, 'L', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(63, 5.5, '# Deuda',  'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(63, 5.5, 'Numero Factura',  'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(64, 5.5, 'Total',  'L,T,B,R', 1, 'R', 0, 0);
$totalDed = 0;
foreach ($dePagos as $dePago){
    $pdf->Cell(63, 5.5, $dePago->deid,  'L,T,B,R', 0, 'C', 0, 0);
    $pdf->cell(63, 5.5, DeudasData::getById($dePago->deid)->derefer,  'L,T,B,R', 0, 'C', 0, 0);
    $pdf->cell(64, 5.5, $dePago->cdvalor,  'L,T,B,R', 1, 'R', 0, 0);
    $totalDed = $totalDed + $dePago->cdvalor;
}
    $pdf->ln(1);
    $pdf->Cell(63, 5.5, '',  '', 0, 'C', 0, 0);
    $pdf->cell(63, 5.5, '',  '', 0, 'C', 0, 0);
    $pdf->SetFillColor(222,222,222);
    $pdf->cell(64, 5.5, $totalDed,  '', 1, 'R', 1, 0);

/* Salida del Documento PDF creado por la consulta */
$pdf->Output('CmpensaciónNumero#' . $_GET['id'] . '.pdf', 'I');
?>