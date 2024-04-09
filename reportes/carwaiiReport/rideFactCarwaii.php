<?php
session_start();
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/TaServiciosData.php';
require '../../core/modules/index/model/TaMarcasData.php';
require '../../core/modules/index/model/TaAgendaData.php';
require '../../core/modules/index/model/TaVehiculosData.php';
require '../../core/modules/index/model/TaCartillaData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
//require '../../core/controller/Fpdf/fpdf.php';
//require '../../core/controller/Fpdf/code128.php';
require 'PDF_MC_Table.php';


$files = FilesData::getByIdOne($_GET['id']);
$agenda = TaAgendaData::getByFiId($_GET['id']);
$detalles = OperationdifData::getFileId($_GET['id']);

$pdf = new PDF_MC_Table('P', 'mm', array(150, 170));
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 25);
//$pdf->Cell(80,1,'', 'T', 1, 'C', 0, 0);
$pdf->Cell(40, 15, 'SERVICIO AUTOMOTRIZ', 0, 1, 'L', 0, 0);
$pdf->SetFont('Arial', 'B', 35);
//$pdf->Cell(80,1,'', 'T', 1, 'C', 0, 0);
$pdf->Cell(40, 6, 'CARWAII', 0, 1, 'L', 0, 0);
//$pdf->Cell(80,1,'', 'B', 1, 'C', 0, 0);
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 13);
$pdf->MultiCell(50, 5, 'CARMEN BARCIA MALDONADO', 0, 'L', 0);
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 15);
$pdf->Image('../../storage/logo/carwaii_.jpg', 90, 25, 55, '', "JPG");
$pdf->SetFont('Arial', 'B', 30);
$pdf->Cell(40, 5, 'FACTURA #', 0, 1, 'L', 0, 0);
$pdf->SetFont('Arial', 'B', 25);
$pdf->Cell(40, 15, $files->fi_docum, 0, 1, 'L', 0, 0);
$pdf->Ln(2);
$pdf->Cell(80, 10, 'Fecha : ' . date('Y-m-d'), 0, 1, 'L', 0, 0);
$pdf->Cell(80, 10, 'Cliente : ' . PersonData::getById($files->ceid)->cename, 0, 1);
$pdf->SetFont('Arial', '', 15);
$pdf->Cell(80, 6, 'Direccion : ' . PersonData::getById($files->ceid)->ceaddress1, 0, 1);
$pdf->Cell(80, 6, 'Telefono : ' . PersonData::getById($files->ceid)->cephone1, 0, 1);
$pdf->Cell(80, 6, 'Fecha y Hora autorizacion: ' . $files->fi_fecauto, 0, 1);
$pdf->Cell(80, 6, 'Clave Acceso ', 0, 1, 'L', 0, 0);
$pdf->SetFont('Arial', '', 14);
//$pdf->Cell(60,5,str_pad('1234',49,0),0, 1, 'L', 0, 0);
$pdf->MultiCell(140, 3, $files->fi_claveacceso, 0, 0, 0);
$pdf->Ln(3);
$pdf->SetFont('Arial', '', 15);
//$pdf->Cell(20, 6, 'Cant', 'B,T,L,R', 0, 'L', 0, 0);
//$pdf->Cell(70, 6, 'Descrp', 'B,T,L,R', 0, 'L', 0, 0);
//$pdf->Cell(20, 6, 'Und', 'B,T,L,R', 0, 'L', 0, 0);
//$pdf->Cell(20, 6, 'Pvp', 'B,T,L,R', 1, 'L', 0, 0);

$pdf->SetWidths(array(20,70,20,20));
srand(microtime()*1000000);
$pdf->Row(array("Cant","Descrp","Und","Pvp"));


foreach ($detalles as $detalle) {
    $pdf->Row(array(number_format($detalle->odcandig,2),ProductData::getById($detalle->itid)->itname,UnitData::getById($detalle->unid_dig)->undescrip,number_format($detalle->odpvp,2)));

//    $pdf->Cell(20, 6, number_format($detalle->odcandig,2), 'B,T,L,R', 0, 'L', 0, 0);
//    $pdf->Cell(70, 6, ProductData::getById($detalle->itid)->itname, 'B,T,L,R', 0, 'L', 0, 0);
//    $pdf->Cell(20, 6, UnitData::getById($detalle->unid_dig)->undescrip, 'B,T,L,R', 0, 'L', 0, 0);
//    $pdf->Cell(20, 6, number_format($detalle->odpvp,2), 'B,T,L,R', 1, 'L', 0, 0);
    $st = $st + $detalle->odpvp;
    $iva = $iva + $detalle->odiva;
    $t = $t + $detalle->odtotal;
}
//$pdf->Cell(80, 6, '  1                    Lavado                   Und              15.00', 0, 1);

$pdf->Cell(110, 6, 'Subtotal', '', 0, 'R', 0, 0);
$pdf->Cell(20, 6, $st, '', 1, 'R', 0, 0);
$pdf->Cell(110, 6, 'IVA', '', 0, 'R', 0, 0);
$pdf->Cell(20, 6, $iva, '', 1, 'R', 0, 0);
$pdf->SetFont('Arial', '', 25);
$pdf->Cell(100, 6, 'Total', '', 0, 'R', 0, 0);
$pdf->Cell(30, 6, number_format($t,2), '', 1, 'R', 0, 0);

$pdf->SetFont('Arial', 'B', 30);
$pdf->Cell(90, 15, 'PLACA : ' . TaVehiculosData::getById($agenda->vhid)->vhplaca, 0, 0, 'L', 0, 0);
$pdf->SetFont('Arial', 'B', 24);
$ptosConsumidos = TaAgendaData::getPtosAcum($agenda->vhid)->ptsacum - TaCartillaData::getPtosConsumidos($agenda->vhid)->ptscons;
$pdf->Cell(5, 50, 'Puntos Disponibles : ' . number_format($ptosConsumidos,2) , 0, 1, 'R', 0, 0);
//$pdf->Cell(40, 8, 'Pts Consumidos : ' . TaAgendaData::getPtosAcum($agenda->vhid)->ptsacum, 0, 1, 'L', 0, 0);




$pdf->output();
