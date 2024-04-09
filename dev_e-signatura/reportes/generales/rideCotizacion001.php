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
require '../../core/modules/index/model/CotizacionData.php';
require '../../core/modules/index/model/CotizacionDetData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';

require '../../core/modules/index/model/VwPagosCreData.php';
require '../../core/modules/index/model/VwPagosData.php';
require '../../core/modules/index/model/FactFEData.php';
require '../../core/modules/index/model/FactFEdetData.php';
require '../../core/modules/index/model/FactFEdifData.php';

require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/NumerosEnLetras.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';

class PDF extends PDF_Code128
{
    function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-35);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
//    $this->Image("../../storage/logo/logosmart.jpg", 10, 1, 10, "JPG");
//    $this->Cell(30,5,'Plataforma de Negocios',0,0,'L');
//    $this->Image("../../".$_SESSION['logoFooter'], 20, null, 180,"JPG"); ok
//    $this->Image("../../storage/logo/logo01.jpg", 5, null, 100);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
    }
}

$pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$cabeceraCotizacion = CotizacionData::getByCoidForPDF($_GET['id']);
$detalleCotizacions = CotizacionDetData::getByCoidForPDF($_GET['id']);
//var_dump($detalleCotizacions);

if (!empty($pathXml->logo)) {
    $logo = "../../" . $pathXml->logo;
} else {
    $logo = '';
}

$numDecimales = ConfigurationData::getByShortName("conf_num_decimal_precio")->cgdatoi;

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 9);

$pdf->Image("../../storage/logo/Logo_DAAS.jpg", 5, 25, 65, "JPG");
// block del nombre de la compa;ia
$pdf->Cell(60, 6, '', 0, 0, 'R');
$pdf->Cell(70, 6, $pathXml->em_comercial, 0, 1, 'L');

$pdf->SetFont('Arial', '', 7);
//$pdf->Cell(60, 6, '', 0, 0, 'R');
//$pdf->Cell(70, 6, "Direccion : " . $pathXml->em_dirmatriz, 0, 1, 'L');

$pdf->SetXY(71,15);
$pdf->MultiCell(50,5,"Direccion : ".$pathXml->em_dirmatriz, 0);

$telefono = '';

if (!is_null($pathXml->em_phono1)) {
    $telefono .= $pathXml->em_phono1 . ',';
}
if (!is_null($pathXml->em_phono2)) {
    $telefono .= $pathXml->em_phono2 . ',';
}
if (!is_null($pathXml->em_phono3)) {
    $telefono .= $pathXml->em_phono3 . ',';
}

$emails = '';

if (!is_null($pathXml->em_email1)) {
    $emails .= $pathXml->em_email1 . ',';
}
if (!is_null($pathXml->em_email2)) {
    $emails .= $pathXml->em_email2 . ',';
}
if (!is_null($pathXml->em_email3)) {
    $emails .= $pathXml->em_email3 . ',';
}

if (!empty($telefono)) {
//    $pdf->Cell(60, 4, '', 0, 0, 'R');
//    $pdf->Cell(70, 4, "Telefono : " . trim($telefono, ','), 0, 1, 'L');
    $pdf->SetXY(71,25);
    $pdf->MultiCell(50,5,"Telefono : ".trim($telefono, ','), 0);
}
if (!empty($emails)) {
    $pdf->SetFont('Arial', '', 7);
//    $pdf->Cell(60, 4, '', 0, 0, 'R');
//    $pdf->Cell(70, 4, "Email : " . trim($emails, ','), 0, 1, 'L');
    $pdf->SetXY(71,38);
    $pdf->MultiCell(50,5,"Email : ".trim($emails, ','), 0);

}

$direccion = $cabeceraCotizacion->direccion1;
if ($direccion == NULL) {
    $direccion = $cabeceraCotizacion->direccion2;
}
$telefono = "";
if (!is_null($cabeceraCotizacion->telefono1)) {
    $telefono .= $cabeceraCotizacion->telefono1 . ",";
}
if (!is_null($cabeceraCotizacion->telefono2)) {
    $telefono .= $cabeceraCotizacion->telefono2 . ",";
}

$pdf->SetXY(140,9);
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(60, 10, "COTIZACION # " . $_GET['id'], 0, 1, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(140,17);
$pdf->Cell(19, 5, "Entrega :", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->cosubtotal, "", 1, 'R');
$pdf->SetXY(140,22);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "Fecha :", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->cofecha, "", 1, 'R');
$pdf->SetXY(140,27);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "Pago :", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(25, 5, $cabeceraCotizacion->cfname, "", 1, 'R');
$pdf->SetXY(140,32);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "Vendedor :", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->vendedor, "", 1, 'R');
$pdf->SetXY(140,37);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "Telefono :", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
if (!empty($cabeceraCotizacion->veemail)) {
    $pdf->Cell(19, 5, $cabeceraCotizacion->vefono, "", 0, 'R');
    $pdf->Cell(19, 5, $cabeceraCotizacion->veemail, "", 1, 'R');
}else{
    $pdf->Cell(19, 5, $cabeceraCotizacion->vefono, "", 0, 'R');
    $pdf->Cell(19, 5, "", "", 1, 'R');
}



$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(60, 5, "", 0, 0, 'R');
$pdf->Cell(70, 5, "", 0, 0, 'R');
$pdf->SetXY(5,50);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(90, 5, "Razon Social : " . $cabeceraCotizacion->cliente, 0, 0, 'L');
$pdf->Cell(90, 5, utf8_decode("Atención : " . $cabeceraCotizacion->contacto), 0, 1, 'L');
$pdf->Cell(90, 5, utf8_decode("Dirección : " . $direccion), 0, 1, 'L');
$pdf->Cell(60, 5, "Ciudad : " . utf8_decode($cabeceraCotizacion->ciudad), 0, 0, 'L');
$pdf->Cell(60, 5, "Telefono : " . trim($telefono, ","), 0, 0, 'L');
$pdf->Cell(60, 5, "R.U.C : " . $cabeceraCotizacion->ruc, 0, 1, 'L');
//$pdf->Cell(190, 5, "Vendedor : " . $cabeceraCotizacion->vendedor, 0, 1, 'L');

$pdf->Ln(6);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "Subtotal ", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->cosubtotal, "", 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "0% ", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->coivano, "", 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "12 % ", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->coivasi, "", 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "I.V.A ", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->coiva, "", 0, 'R');
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(19, 5, "TOTAL ", "", 0, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(19, 5, $cabeceraCotizacion->cototal, "", 1, 'R');

$pdf->SetFillColor(152,241,125);
//$pdf->Cell();
$pdf->Cell(28, 5, "CODIGO ", "", 0, 'L',1);
$pdf->Cell(75, 5, "DESCRIPCION ", "", 0, 'L',1);
$pdf->Cell(17, 5, "CANTIDAD ", "", 0, 'R',1);
$pdf->Cell(17, 5, "PRECIO ", "", 0, 'R',1);
$pdf->Cell(17, 5, "% DESC ", "", 0, 'R',1);
$pdf->Cell(17, 5, "% DESC ", "", 0, 'R',1);
$pdf->Cell(17, 5, "TOTAL ", "", 1, 'R',1);

$pdf->SetFont('Arial', '', 8);
$total = 0;
$iva = 0;
foreach ($detalleCotizacions as $detalleCotizacion) {
    $pdf->Cell(28, 5, $detalleCotizacion->itcodigo, "", 0, 'L');
    $pdf->Cell(75, 5, $detalleCotizacion->itname, "", 0, 'L');
    $pdf->Cell(17, 5, $detalleCotizacion->cdcandig, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cdpvp, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cddscto1, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cddscto2, "", 0, 'R');
    $pdf->Cell(17, 5, number_format($detalleCotizacion->cdtotal, 2), "", 1, 'R');
    $total += $detalleCotizacion->cdtotal;
    $desc += $detalleCotizacion->cddscto2 + $detalleCotizacion->cddscto1;
}
$pdf->Cell(188, 5, "", "T", 1, 'R');

$pdf->Ln(0);
$pdf->Cell(92, 5, "SON : " . NumerosEnLetras::convertir($total), "", 0, 'L');
$pdf->Cell(92, 5, "", "", 1, 'R');
$pdf->Cell(92, 5, "OBSERVACION : ", "", '', 'L');
$pdf->Cell(92, 5, "", "", 1, 'R');
$pdf->Cell(92, 5, utf8_decode("Comentario de observacion para la cotización"), "", '', 'L');
$pdf->Cell(92, 5, "", "", 1, 'R');
$pdf->Cell(92, 5, '', "", '', 'L');
$pdf->Cell(92, 5, "" , "", 1, 'R');
$pdf->Cell(92, 5, '', "", '', 'L');
$pdf->Cell(92, 5, "", "", 1, 'R');

$pdf->Output();
