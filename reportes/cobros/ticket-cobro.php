<?php
session_start();
include "../../core/controller/Core.php";
include "../../core/controller/Database.php";
include "../../core/controller/Executor.php";
include "../../core/controller/Model.php";
include "../../core/modules/index/model/FormasData.php";
include "../../core/modules/index/model/DeudasData.php";
include "../../core/modules/index/model/UserData.php";
include "../../core/modules/index/model/BancosclienteData.php";
include "../../core/modules/index/model/PersonData.php";
include "../../core/modules/index/model/CobroscabData.php";
include "../../core/modules/index/model/CobrostData.php";
include "../../core/modules/index/model/CobrosdetData.php";
include "../../core/modules/index/model/EmpresasData.php";
/** ========= */
$user = UserData::getById($_SESSION['user_id']);
$empresa = EmpresasData::getById(1);
/** ========= */
require '../../core/controller/Fpdf/fpdf.php';

/** ========= */

$cabCobros = CobroscabData::getById($_GET["id"]); /** ======= CABECERA DE COBRO ======= */
$detCobros = CobrosdetData::getByIdCoid($_GET["id"]); /** ========= DETALLE DE COBRO ======== */
$deudas = CobrostData::getAllDeudas($_GET["id"]); /** ========= DETALLE DE DEUDA ======== */
$docDeudas = DeudasData::getByCeId($cabCobros->ceid);
/*=========*/
$cliente = PersonData::getById($cabCobros->ceid);
$pdf = new FPDF($orientation = 'P', $unit = 'mm', array(57, 250));
$pdf->AddPage();
$pdf->setY(2);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(45, 5, $cabCobros->cocreate_at, 0, 1, 'R');
$pdf->setX(2);
$pdf->SetFont('Arial', 'B', 11);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Multicell(50, 4, $empresa->em_nombre, 0, 'C', 0);
$pdf->SetFont('Arial', '', 7);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(50, 3, $empresa->em_ruc, 0, 1, 'C');
$pdf->setX(2);
$pdf->Cell(5, 3, 'CLIENTE : ', 0, 1);
$pdf->setX(2);
$pdf->Multicell(50, 3, $cliente->cename, 0, 'L', 0);
$pdf->setX(2);
$pdf->Cell(5, 3, 'C.I/RUC : ' . $cliente->cerut, 0, 1);
$pdf->setX(2);
$pdf->Cell(5, 3, 'DIR : ', 0, 1);
if (!empty($cliente->ceaddress1)) {
  $pdf->setX(2);
  $pdf->Multicell(50, 3, $cliente->ceaddress1, 0, 'L', 0);
}
$pdf->setX(2);
$pdf->SetFont('Arial', '', 8);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(5, 4, 'USUARIO : ' . $user->name, 0, 1);
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 14);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(32, 7, 'RECIBO #', 'L,T,B', 0, 'R', 0);
$pdf->SetFont('Arial', 'B', 14);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(20, 7, $_GET['id'], 'R,T,B', 1, 'L', 0);
$pdf->Ln(2);
$pdf->setX(2);
//$pdf->SetFont('Arial', '', 7);
//$pdf->setFillColor(230,230,0);
$pdf->SetFont('Arial', '', 7);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(52, 1, '', 'T,L,R', 1, 'L', 0);
$pdf->setX(2);
$pdf->MultiCell(52, 3, utf8_decode("Este es el único documento que respaldará el pago de su deuda"), 'R,L', 'C', 0);
$pdf->setX(2);
$pdf->Cell(52, 1, '', 'B,L,R', 1, 'L', 0);
//$pdf->setFillColor(0,0,0);
$pdf->Ln();
$pdf->SetFont('Arial', '', 6);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(10, 4, 'FORMAS DE PAGO. ', 0, 1);
$pdf->setX(2);
$pdf->Cell(3, 1, '------------------------------------------------------------------------', 0, 1);
foreach ($detCobros as $det) {
  $pdf->setX(2);
  $pdf->Cell(20, 4, utf8_decode(FormasData::getById($det->cfid)->cfname), 0, 0, 'L');
  $banco = '';
  if (!empty($det->bbid) || $det->bbid != 0) {
    $banco = utf8_decode(BancosclienteData::getById($det->bbid)->bbnombre);
  }
  $pdf->Cell(15, 4, $banco, 0, 0, 'L');
  $pdf->Cell(17, 4, "$ " . $det->fcvalor, 0, 1, 'R');
  $totFp = $totFp + $det->fcvalor;
}
$pdf->setX(2);
$pdf->Cell(40, 2, '------------------------------------------------------------------------', 0, 1);
$pdf->Ln(1);
$pdf->SetFont('Arial', '', 10);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(38, 2, "Total : ", 0, 0);
$pdf->SetFont('Arial', 'B', 11);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(10, 2, "$ " . number_format($totFp, 2), 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 6);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(10, 4, 'DOCUMENTOS. ', 0, 1);
$pdf->setX(2);
$pdf->Cell(3, 1, '------------------------------------------------------------------------', 0, 1);
foreach ($deudas as $deud) {
  $pdf->setX(2);
  $pdf->Cell(35, 4, trim($deud->derefer), 0, 0, 'L');
  $pdf->Cell(17, 4, "$ " . $deud->cdvalor, 0, 1, 'R');
  $totFpD = $totFpD + $deud->cdvalor;
}
$pdf->setX(2);
$pdf->Cell(40, 2, '------------------------------------------------------------------------', 0, 1);
$pdf->Ln(1);
$pdf->SetFont('Arial', '', 10);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(38, 2, "Total : ", 0, 0);
$pdf->SetFont('Arial', 'B', 11);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(10, 2, "$ " . number_format($totFpD, 2), 0, 1);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 6);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(10, 4, 'DETALLE DE SALDOS.', 0, 1);
$pdf->setX(2);
$pdf->Cell(3, 3, '----------------------------------------------------------------------', 0, 1);
$pdf->setX(2);
$pdf->Cell(10, 4, 'DOCUMENTO                  ABONADO         SALDO. ', 0, 1);
$pdf->setX(2);
$pdf->Cell(3, 3, '----------------------------------------------------------------------', 0, 1);
$pdf->SetFont('Arial', '', 7);    //Letra Arial, negrita (Bold), tam. 20
foreach ($docDeudas as $deuda) {
  $pdf->setX(2);
  $pdf->Cell(27, 4, trim($deuda->derefer), 0, 0, 'L');
  $pdf->Cell(10, 4, trim($deuda->deabono), 0, 0, 'R');
  $pdf->Cell(12, 4, trim($deuda->desaldo), 0, 1, 'R');
  $totDoc = $totDoc + $deuda->deabono;
  $saldoT += DeudasData::getById($deuda->deid)->desaldo;
}
$pdf->SetFont('Arial', '', 10);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(2);
$pdf->Cell(3, 1, '------------------------------------------------------------', 0, 1);
$pdf->Ln(1);
$pdf->setX(2);
$pdf->Cell(22, 2, "Total : ", 0, 0);
$pdf->SetFont('Arial', 'B', 7);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(17, 2, "$ ".number_format($totDoc, 2),0, 'C', 0);
$pdf->Cell(17, 2, "$ ".number_format($saldoT, 2),0, 'C', 1);
/** ** */
if (!empty($cabCobros->coobserva)) {
  $pdf->SetFont('Arial', 'B', 8);    //Letra Arial, negrita (Bold), tam. 20
  $pdf->Ln(5);
  $pdf->setX(2);
  $pdf->Cell(52, 5, "Comentario : ", 'T,L,R', 1, 'L', 0);
  $pdf->SetFont('Arial', '', 8);    //Letra Arial, negrita (Bold), tam. 20
  $pdf->SetFont('Arial', '', 7);    //Letra Arial, negrita (Bold), tam. 20
  $pdf->setX(2);
  $pdf->MultiCell(52, 6, utf8_decode($cabCobros->coobserva), 'R,B,L', 'C', 0);
}
$pdf->output();

