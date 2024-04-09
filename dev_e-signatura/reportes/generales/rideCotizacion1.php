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

//require '../../core/autoload.php';


class PDF extends PDF_Code128
{
// Page header
    function Header()
    {
        // Logo
        $this->Image("../../storage/logoConfig/cotMayoCabecera.jpg", 0, 0, 210, "JPG");
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Ln(20);
    }

// Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        $this->Image("../../storage/logoConfig/cotMayoFooter.jpg", 0, 250, 209, "JPG");
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
$pdf->SetFont('Arial', 'B', 16);

//$pdf->Image("../../storage/logoConfig/cotMayoCabecera.jpg", 0, 0, 209, "JPG");
//
//$pdf->Cell(100, 6, '', 0, 0, 'R');
//$pdf->Cell(90, 6, $pathXml->em_comercial, 0, 1, 'C');
//$pdf->SetFont('Arial', '', 9);
//$pdf->Cell(100, 6, '', 0, 0, 'R');
//$pdf->Cell(90, 6, "Direccion : " . $pathXml->em_dirmatriz, 0, 1, 'C');
//$telefono = '';
//
//if (!is_null($pathXml->em_phono1)) {
//    $telefono .= $pathXml->em_phono1 . ',';
//}
//if (!is_null($pathXml->em_phono2)) {
//    $telefono .= $pathXml->em_phono2 . ',';
//}
//if (!is_null($pathXml->em_phono3)) {
//    $telefono .= $pathXml->em_phono3 . ',';
//}
//
//$emails = '';
//
//if (!is_null($pathXml->em_email1)) {
//    $emails .= $pathXml->em_email1 . ',';
//}
//if (!is_null($pathXml->em_email2)) {
//    $emails .= $pathXml->em_email2 . ',';
//}
//if (!is_null($pathXml->em_email3)) {
//    $emails .= $pathXml->em_email3 . ',';
//}
//
//if (!empty($telefono)) {
//    $pdf->Cell(100, 4, '', 0, 0, 'R');
//    $pdf->Cell(90, 4, "Telefono : " . trim($telefono, ','), 0, 1, 'C');
//}
//if (!empty($emails)) {
//    $pdf->SetFont('Arial', '', 7);
//    $pdf->Cell(100, 4, '', 0, 0, 'R');
//    $pdf->Cell(90, 4, "Email : " . trim($emails, ','), 0, 1, 'C');
//}
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
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(90, 5, "Razon Social : " . $cabeceraCotizacion->cliente, 0, 0, 'L');
$pdf->Cell(90, 5, utf8_decode("Atención : " . $cabeceraCotizacion->contacto ), 0, 1, 'L');
$pdf->Cell(90, 5, utf8_decode("Dirección : " . $direccion), 0, 1, 'L');
$pdf->Cell(60, 5, "Ciudad : " . utf8_decode($cabeceraCotizacion->ciudad), 0, 0, 'L');
$pdf->Cell(60, 5, "Telefono : " . trim($telefono, ","), 0, 0, 'L');
$pdf->Cell(60, 5, "R.U.C : " . $cabeceraCotizacion->ruc, 0, 1, 'R');
$pdf->Cell(190, 5, "Vendedor : " . $cabeceraCotizacion->vendedor, 0, 1, 'L');

$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(190, 10, "COTIZACION # " . $_GET['id'], 0, 1, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(28, 5, "CODIGO ", "T,B", 0, 'C');
$pdf->Cell(75, 5, "DESCRIPCION ", "T,B", 0, 'C');
$pdf->Cell(17, 5, "CANTIDAD ", "T,B", 0, 'R');
$pdf->Cell(17, 5, "PRECIO ", "T,B", 0, 'R');
$pdf->Cell(17, 5, "% DESC ", "T,B", 0, 'R');
$pdf->Cell(17, 5, "% DESC ", "T,B", 0, 'R');
$pdf->Cell(17, 5, "TOTAL ", "T,B", 1, 'R');

$pdf->SetFont('Arial', '', 8);
$total = 0;
$iva = 0;
foreach ($detalleCotizacions as $detalleCotizacion) {
    $pdf->Cell(28, 5, $detalleCotizacion->itcodigo, "", 0, 'C');
    $pdf->Cell(75, 5, $detalleCotizacion->itname, "", 0, 'L');
    $pdf->Cell(17, 5, $detalleCotizacion->cdcandig, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cdpvp, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cddscto1, "", 0, 'R');
    $pdf->Cell(17, 5, $detalleCotizacion->cddscto2, "", 0, 'R');
    $pdf->Cell(17, 5, number_format($detalleCotizacion->cdtotal,2), "", 1, 'R');
    $total += $detalleCotizacion->cdtotal;
    $desc += $detalleCotizacion->cddscto2 + $detalleCotizacion->cddscto1;
}
$pdf->Cell(188, 5, "", "T", 1, 'R');

$pdf->Ln(0);
$pdf->Cell(92, 5, "SON : " . NumerosEnLetras::convertir($total), "", 0, 'L');
$pdf->Cell(92, 5, "Venta Bruta : " . number_format($total, 2), "", 1, 'R');
$pdf->Cell(92, 5, "OBSERVACION : ", "", '', 'L');
$pdf->Cell(92, 5, "% Desc : " . number_format($desc, 2), "", 1, 'R');
$pdf->Cell(92, 5, utf8_decode("Comentario de observacion para la cotización"), "", '', 'L');
$pdf->Cell(92, 5, "Subtotal : " . number_format($desc, 2), "", 1, 'R');
$pdf->Cell(92, 5, '', "", '', 'L');
$pdf->Cell(92, 5, "IVA : " . number_format($iva, 2), "", 1, 'R');
$pdf->Cell(92, 5, '', "", '', 'L');
$pdf->Cell(92, 5, "Neto : " . number_format($total - $desc + $iva, 2), "", 1, 'R');

$pdf->Output();
