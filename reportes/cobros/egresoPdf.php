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
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';


$cPagos = PagosData::getById($_GET['id']);
$dPagos = DetPagosData::getByAllByPago($_GET['id']);

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(150,6,EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre,0,0);$pdf->Cell(40,5.5,'Egreso N.'.$_GET['id'],0,1);
$pdf->SetFont('Arial','',10);
$pdf->Cell(40,5.5,'Ruc:'.$_SESSION['ruc'],0,1);
$pdf->Cell(150,5.5,'Fecha de Emision :'.$cPagos->fecha,0,0);
$pdf->Cell(65,5.5,'Total : $'.$cPagos->total,0,1);

$pdf->SetFont('Arial','',10);
$pdf->Cell(150,5.5,'Beneficiario : '.ProveeData::getById($cPagos->idproveedor)->razon,0,0);
$pdf->Cell(50,5.5,'Banco : '.EntidadesFinData::getById($cPagos->identidadfin)->bcnombre,0,1);
$pdf->Cell(150,5.5,'Glosa : '.$cPagos->comentario,0,0);
$pdf->Cell(50,5.5,'# Cuenta : '.EntidadesFinData::getById($cPagos->identidadfin)->bcnumero,0,1);

$pdf->ln(5);

$pdf->Cell(35,5.5,'# Documento','T,L,B,R',0); $pdf->cell(125,5.5,'Tipo Documento','T,L,B,R',0);$pdf->cell(30,5.5,'Total','T,B,L,R',1);
foreach($dPagos as $pagos){
  switch (FilesData::getById($pagos->deid)->fi_tipo){
    case 01:
      $pdf->Cell(35,5.5,FilesData::getById($pagos->deid)->fi_docum,'L,R',0); $pdf->cell(125,5.5,'Factura','L,R',0); $pdf->cell(30,5.5,$pagos->dpvalor,'L,R',1,0);
      break;
    case 05:
      $pdf->Cell(35,5.5,FilesData::getById($pagos->deid)->fi_docum,'L,R',0); $pdf->cell(30,5.5,'Nota de Debito','L,R',0); $pdf->cell(85,5.5,FilesData::getById($pagos->deid)->fi_glosa,'L,R',0);$pdf->cell(30,5.5,$pagos->dpvalor,'L,R',1,0);
      break;
    default:
      $pdf->Cell(35,5.5,FilesData::getById($pagos->deid)->fi_docum,'L,R',0); $pdf->cell(30,5.5,'Liquidacion de Compras','L,R',0); $pdf->cell(85,5.5,FilesData::getById($pagos->deid)->fi_glosa,'L,R',0);$pdf->cell(30,5.5,$pagos->dpvalor,'L,R',1,0);
      break;
  }
  $total += $pagos->dpvalor;
}
$pdf->Cell(190,5.5,'','T',1);
$pdf->Cell(160,5.5,'Total :', 0, 0, 'R', 0, 0);
$pdf->Cell(30,5.5,$total, 0, 0, 'R', 0, 0);
$pdf->Ln(35);
$pdf->Cell(35,5.5,UserData::getById($_SESSION['user_id'])->name.' '.UserData::getById($_SESSION['user_id'])->lastname,0,1);
$pdf->Cell(35,5.5,'','T',0);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'','T',0);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'','T',0);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'','T',1);
$pdf->Cell(35,5.5,'Preparado por:', 0, 0, 'C', 0, 0);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'Contador', 0, 0, 'C', 0, 1);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'Autorizado por:', 0, 0, 'C', 0, 0);$pdf->Cell(5,5.5,'','',0);$pdf->Cell(35,5.5,'Recibido por:', 0, 0, 'C', 0, 1);

$tipo = EntidadesFinData::getById($cPagos->identidadfin)->bctipo;

if($tipo == 1){
  $chid = ChequeData::getByBcId($cPagos->identidadfin);
  $pdf->AddPage();
  $pdf->SetFont('Arial', '', 10);
  $pdf->SetXY($chid->cxben / 3 , $chid->cyben / 3);
  $pdf->Cell(0, 5.5, ProveeData::getById($cPagos->idproveedor)->razon , 0, 0);
  $pdf->SetXY($chid->cxval / 3, $chid->cyval / 3);
  $pdf->Cell(0, 5.5, '***'.number_format($total,2), 0, 0);
  $pdf->SetXY($chid->cxvall / 3 , $chid->cyvall / 3);
  $pdf->Cell(300, 5.5, NumerosEnLetras::convertir(number_format($total,2),'Dolares',false,'Centavos') , 0, 1);
  $pdf->SetXY($chid->cxfecha /3, $chid->cyfecha /3 );
  $pdf->Cell(0, 5.5, 'Guayaquil, ' . str_replace('-','/',$cPagos->fecha), 0, 1);
}

/* Salida del Documento PDF creado por la consulta */
$pdf->Output('EgresoNumero#'.$_GET['id'].'.pdf','I');
?>