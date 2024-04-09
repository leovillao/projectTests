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
require '../../core/modules/index/model/VwPagosCreData.php';
require '../../core/modules/index/model/VwPagosData.php';
require '../../core/modules/index/model/FactFEData.php';
require '../../core/modules/index/model/FactFEdifData.php';
require '../../core/modules/index/model/FactFEdetData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';

if (VwPagosCreData::getAllByIdFiles($_GET['id'])) {
    $detPagos = VwPagosCreData::getById($_GET['id']);
}else{
    $detPagos = VwPagosData::getById($_GET['id']);
}
$rowFactura = FactFEData::getById($_GET['id']);/* Obntego la Cabecera de la factura */
$rowDetFacturas = FactFEdetData::getByIdFcNumber($_GET['id']);/* Detalle de la factura */
$rowDifFacturas = FactFEdifData::getByIdFcNumber($_GET['id']);/* Detalle de la factura */
$empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$emails = '';
$filesDocumento = FilesData::getByIdOne($_GET['id']); // documentos Files no autorizado
$clientMails = PersonData::getByCeRut($rowFactura->identificacionComprador);
if ($clientMails->ceemail1 != '' && $clientMails->ceemail1 != NULL) {
    $emails .= $clientMails->ceemail1 . ',';
}
if ($clientMails->ceemail2 != '' && $clientMails->ceemail2 != NULL) {
    $emails .= $clientMails->ceemail2 . ',';
}
if ($clientMails->ceemail3 != '' && $clientMails->ceemail3 != NULL) {
    $emails .= $clientMails->ceemail3 . ',';
}
//$xml = RsmData::getById(FilesData::getByIdOne($_GET['id'])->fi_idfile); // valida el fi_id de la tabla de RSM archivo XML
$factura = $filesDocumento->fi_docum;
$savePdf = $filesDocumento->fi_claveacceso;

class PDF extends PDF_Code128 {
    function Footer() {
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

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$claveacceso = $filesDocumento->fi_claveacceso;
$fechauto = $filesDocumento->fi_fecauto;
if (!empty($pathXml->logo)) {
    $logo = "../../" . $pathXml->logo;
} else {
    $logo = '../../storage/logo/sinlogo.jpg';
}
//foreach ($Xmls->comprobante as $comprobante) {
//  $xmls = simplexml_load_string($comprobante, 'SimpleXmlElement', LIBXML_NOCDATA);
$ambiente = "PRUEBA";
//  foreach ($xmls->infoTributaria as $valores) {
if ($rowFactura->ambiente == 2) {
    $ambiente = "PRODUCCION";
}
if ($rowFactura->tipoEmision == 1) {
    $emision = "NORMAL";
}
//$pdf->Image($logo, 35, 5, 45, "JPG");
$pdf->Ln();
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 5, 'RUC : ' . $empresa->em_ruc, 0, 1, 'C', 0, 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(95, 7, 'LIQUIDACION DE SERVICIOS ', 0, 1, 'C', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 19);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 7, $rowFactura->secuencial, 0, 1, 'C', 0, 1);
/*$pdf->SetFont('Arial', '', 8);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);*/
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(100, 5, utf8_decode($pathXml->em_comercial), 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(100, 4, $pathXml->em_slogan, 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(98, 5, $rowFactura->razonSocial, 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 8);
$empresa = $rowFactura->razonSocial;
//$pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'Direccion Matriz :', 0, 1, 'L', 0, 1);
//$pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, utf8_decode($rowFactura->dirMatriz), 0, 1, 'L', 0, 1);
//$pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $rowFactura->contribuyenteEspecial, 0, 1, 'L', 0, 1);
//$pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $rowFactura->obligadoContabilidad, 0, 1, 'L', 0, 1);
//$pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
//$code = $claveacceso;
//if(!empty($claveacceso)) {
//    $pdf->Code128(105, 73, $code, 100, 9);
//}
//        $pdf->SetXY(5,195);
//        $pdf->Write(5,'"'.$code.'"');
$pdf->Ln(5);
if (isset($rowFactura->regimenMicroempresa) && !empty($rowFactura->regimenMicroempresas)) {
    $pdf->Cell(98, 5, utf8_decode("CONTRIBUYENTE RÉGIMEN MICROEMPRESAS"), 0, 1, 'L', 0, 1);
}
if (isset($rowFactura->agenteRetencion) && !empty($rowFactura->agenteRetencion)) {
    $pdf->Cell(98, 5, utf8_decode("AGENTE DE RETENCION RESOLUCION : NAC-DNCRASC20-00000001"), 0, 1, 'L', 0, 1);
}
$nombre = $rowFactura->estab . $rowFactura->ptoEmi . $rowFactura->secuencial;
//  }
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(100, 5, 'RAZON SOCIAL : ' . $rowFactura->razonSocialComprador, 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'IDENTIFICACION COMPRADOR : ' . $rowFactura->identificacionComprador, 0, 1, 'L', 0, 1);
$pdf->Cell(98, 5, 'DIRECCION COMPRADOR : ' . $rowFactura->direccionComprador, 0, 1, 'L', 0, 1);
$pdf->Cell(98, 5, 'FECHA DE EMISION : ' . $rowFactura->fechaEmision, 0, 1, 'L', 0, 1);
if (!empty($rowFactura->telefono1)) {
    $pdf->Cell(95, 5, 'Telefono :' . ' ' . $rowFactura->telefono1, 0, 1, 'L', 0, 0);
} else {
//  $pdf->Cell(80, 5, ' ', '', 1, 'L', 0, 0);
}
if (!empty($rowFactura->comentariofac)) {
    $pdf->Cell(95, 5, 'COMENTARIO :' . ' ' . $rowFactura->comentariofac, 0, 1, 'L', 0, 0);
} else {
  $pdf->Cell(80, 5, ' ', '', 1, 'L', 0, 0);
}
$pdf->Ln();
//$pdf->Line(10, 108, 200, 108);
//$pdf->Ln();
$pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
$pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
$totDesc = 0;
if($rowDifFacturas){
    foreach ($rowDifFacturas as $rowDifFactura) {
        $pdf->Cell(20, 5, $rowDifFactura->codigoPrincipal, "T", 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode($rowDifFactura->descripcion), "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->cantidad, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->precioUnitario, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->descuento, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->precioTotalSinImpuesto, "T", 1, 'C', 0, 1);
        $totDesc = intval($totDesc) + intval($rowDifFactura->descuento);
        if (!empty($rowDifFactura->comentario)){
            $pdf->Cell(20, 5, '', 0, 0, 'C', 0, 1);
            $pdf->MultiCell(70, 5,  utf8_decode($rowDifFactura->comentario), '',"L", 0);
        }
        if (!empty($rowDifFactura->comentario1)) {
            $pdf->Cell(20, 5, '', "T", 0, 'C', 0, 1);
            $pdf->MultiCell(90, 5,  utf8_decode($rowDifFactura->comentario1), 0,"C", 0);
        }
        if (!empty($rowDifFactura->comentario2)) {
            $pdf->Cell(20, 5, '', "T", 0, 'C', 0, 1);
            $pdf->MultiCell(90, 5,  utf8_decode($rowDifFactura->comentario2), 0,"C", 0);
        }
    }
}
if ($rowDetFacturas){
    foreach ($rowDetFacturas as $rowDetFactura) {
        $pdf->Cell(20, 5, $rowDetFactura->codigoPrincipal, "T", 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode($rowDetFactura->descripcion), "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->cantidad, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->precioUnitario, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->descuento, "T", 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->precioTotalSinImpuesto, "T", 1, 'C', 0, 1);
        $totDesc = intval($totDesc) + intval($rowDetFactura->descuento);
        if (!empty($rowDetFactura->comentario1)){
            $pdf->MultiCell(90, 5,  $rowDetFactura->comentario1, 0,"C", 0);
        }
        if (!empty($rowDetFactura->comentario2)) {
            $pdf->MultiCell(90, 5,  $rowDetFactura->comentario2, 0,"C", 0);
        }
        if (!empty($rowDetFactura->comentario3)) {
            $pdf->MultiCell(90, 5,  $rowDetFactura->comentario3, 0,"C", 0);
        }
    }
}
$pdf->Cell(130, 1, '', "B", 1, 'L', 0, 1);

$pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
$pdf->Cell(40, 5, 'Subtotal ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, $rowFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
//$pdf->SetFont('Arial', '', 10);
//$pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
//$pdf->SetFont('Arial', '', 9);
//$pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
//$pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $totDesc, 1, 1, 'C', 0, 1);
//
////}
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->Cell(40, 5, 'base 12% ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $rowFactura->grabado, 1, 1, 'C', 0, 1);
//$pdf->Cell(100, 5, 'Moneda : ' . $pathXml->em_moneda, 'R,L', 0, 'L', 0, 1);
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->Cell(40, 5, 'base 0% ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $rowFactura->exento, 1, 1, 'C', 0, 1);
//$pdf->Cell(100, 5, 'Total ' . $detPagos->total . ' Plazo ' . $detPagos->plazo . ' ' . $detPagos->unidadTiempo, 'R,B,L', 0, 'L', 0, 1);
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->Cell(40, 5, 'SubTotalSin Impuestos ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $rowFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
//$pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $rowFactura->iva, 1, 1, 'C', 0, 1);
//$pdf->Cell(100, 5, '', 0, 0, 'L');
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
//$pdf->Cell(20, 5, $detPagos->total, 1, 1, 'C', 0, 1);
//$pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
//$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
//$pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
//$pdf->Cell(100, 5, utf8_decode('INFORMACIÓN ADICIONAL'), 'T,L,R', 1, 'C', 0, 1);
//$pdf->SetFont('Arial', '', 9);
//$campoAdi = $xmls->infoAdicional->campoAdicional;
//for ($j = 0; $j < $totCampos; $j++) {
//  if ($campoAdi[$j]['nombre'] != 'IdTransac' && $campoAdi[$j]['nombre'] != 'comentario') {
//$pdf->Cell(100, 5, "DireccionComprador :".utf8_decode($rowFactura->direccionComprador), 'L,R', 1, 'L', 0, 1);
//$pdf->Cell(100, 5, "Telefonos :".utf8_decode($rowFactura->telefono1), 'L,R', 1, 'L', 0, 1);
//$pdf->Cell(100, 5, "Correos:".utf8_decode($emails), 'L,R', 1, 'L', 0, 1);
//  }
//}
//$pdf->Cell(100, 5, '', 'B,L,R', 1, 'R', 0, 1);
//}
$archivo = $pdf->Output();

function validaFormato($number){
    return number_format($number,2,',','');
}