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
$empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$emails = '';
$dpdf = FilesData::getByIdOne($_GET['id']);
$clientMails = PersonData::getByCeRut($dpdf->fi_er_ruc);
if ($clientMails->ceemail1 != '') {
  $emails .= $clientMails->ceemail1 . ',';
}
if ($clientMails->ceemail2 != '') {
  $emails .= $clientMails->ceemail2 . ',';
}
if ($clientMails->ceemail3 != '' || $clientMails->ceemail3 != null) {
  $emails .= $clientMails->ceemail3 . ',';
}
//var_export($emails);
$ch = curl_init("http://pyme.e-piramide.net/downxml/getXml.php");
$fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $_GET['clave']);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$xml = curl_exec($ch);
if (curl_errno($ch)) {
  $xml = curl_error($ch);
  exit();
}
curl_close($ch);
/** SE LEE EL ARCHIVO RECIBIDO DESDE EL WEBSERVICES CON LA FUNCION ICONV*/
$xmlFile = iconv('UTF-8', 'UTF-8//IGNORE', $xml);
/** SE DEVUELVE EL ARCHIVO A LA FUNCION SIMPLEXML_LOAD_STRING PARA PARSEAR LAS ETIQUETAS Y VISULIZAR EL CONTENIDO */
$Xmls = simplexml_load_string($xmlFile, 'SimpleXmlElement', LIBXML_NOCDATA);

$savePdf = $Xmls->numeroAutorizacion;

class PDF extends PDF_Code128 {
  function Footer() {
    // Go to 1.5 cm from bottom
    $this->SetY(-35);
    // Select Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Print centered page number
//    $this->Image("../../storage/logo/logosmart.jpg", 10, 1, 10, "JPG");
//    $this->Cell(30,5,'Plataforma de Negocios',0,0,'L');
    $this->Image("../../storage/logo/logo03.jpg", 20, null, 100);
//    $this->Image("../../storage/logo/logo01.jpg", 5, null, 100);
    $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
  }
}

$pdf = new PDF();
$pdfile = $pdf->AddPage();
$pdfile = $pdf->SetFont('Arial', 'B', 16);
$claveacceso = $Xmls->numeroAutorizacion;
$fechauto = $Xmls->fechaAutorizacion;
if (!empty($pathXml->logo)) {
  $logo = "../../" . $pathXml->logo;
} else {
  $logo = '';
}
foreach ($Xmls->comprobante as $comprobante) {
  $xmls = simplexml_load_string($comprobante, 'SimpleXmlElement', LIBXML_NOCDATA);
  $ambiente = "PRUEBA";
  foreach ($xmls->infoTributaria as $valores) {
    if ($valores->ambiente == 2) {
      $ambiente = "PRODUCCION";
    }
    if ($valores->tipoEmision == 1) {
      $emision = "NORMAL";
    }
    $pdf->Image($logo, 35, 5, 45, "JPG");
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 16);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(90, 5, 'RUC : ' . $empresa->em_ruc, 0, 1, 'C', 0, 1);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(95, 7, 'NOTA DE CREDITO ', 0, 1, 'C', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 19);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(90, 7, $valores->estab . '-' . $valores->ptoEmi . '-' . $valores->secuencial, 0, 1, 'C', 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 13);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(100, 5, $pathXml->em_comercial, 0, 1, 'C', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(100, 4, $pathXml->em_slogan, 0, 1, 'C', 0, 1);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(98, 5, $valores->razonSocial, 0, 0, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 8);
    $empresa = $valores->razonSocial;
    $pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, $valores->dirMatriz, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $xmls->infoNotaCredito->contribuyenteEspecial, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $xmls->infoNotaCredito->obligadoContabilidad, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
    $code = $claveacceso;
    if (isset($valores->regimenMicroempresa) && !empty($valores->regimenMicroempresas)) {
      $pdf->Cell(98, 5, utf8_decode("CONTRIBUYENTE RÉGIMEN MICROEMPRESAS"), 0, 1, 'L', 0, 1);
    }
    if (isset($valores->agenteRetencion) && !empty($valores->agenteRetencion)) {
      $pdf->Cell(98, 5, utf8_decode("AGENTE DE RETENCION RESOLUCION : NAC-DNCRASC20-00000001"), 0, 1, 'L', 0, 1);
    }

    $pdf->Code128(105, 73, $code, 100, 9);
    $pdf->Ln(12);

    $nombre = $valores->estab . $valores->ptoEmi . $valores->secuencial;

  }
  $totAdi = count($xmls->infoAdicional->campoAdicional);
  $campoAdi = $xmls->infoAdicional->campoAdicional;

  $pdf->SetFont('Arial', '', 8);
  $pdf->Cell(60, 5, 'FECHA DE EMISION : ' . $xmls->infoNotaCredito->fechaEmision, 'T,L', 0, 'L', 0, 1);
  $pdf->Cell(65, 5, 'IDENTIFICACION COMPRADOR : ' . $xmls->infoNotaCredito->identificacionComprador, 'T', 0, 'L', 0, 1);
  $pdf->Cell(65, 5, 'DOC MODIFICADO : ' . $xmls->infoNotaCredito->numDocModificado, 'T,R', 1, 'R', 0, 1);
  $pdf->Cell(190, 5, 'RAZON SOCIAL : ' . $xmls->infoNotaCredito->razonSocialComprador, 'L,R', 1, 'L', 0, 1);
  $pdf->Cell(190, 5, 'MOTIVO ANULACION : ' . $xmls->infoNotaCredito->motivo, 'L,R,B', 1, 'L', 0, 1);

  $pdf->Ln();
  $pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
  $pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
  $pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
  $pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
  $pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
  $pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
  $pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
  $totDet = COUNT($xmls->detalles->detalle);
  $td = 0;
  $totDesc = 0;
  for ($td = 0; $td < $totDet; ++$td) {

    $toDet = COUNT($xmls->detalles->detalle[$td]->detallesAdicionales->detAdicional);
    $adiDetalle = $xmls->detalles->detalle[$td]->detallesAdicionales->detAdicional;
    $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->codigoInterno, 'R,L', 0, 'C', 0, 1);
    $pdf->Cell(70, 5, utf8_decode($xmls->detalles->detalle[$td]->descripcion), 'R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->cantidad, 'R,L', 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Und', 'R,L', 0, 'C', 0, 1);
    $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioUnitario, 'R,L', 0, 'C', 0, 1);
    $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->descuento, 'R,L', 0, 'C', 0, 1);
    $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioTotalSinImpuesto, 'R,L', 1, 'C', 0, 1);
    $tdd = 0;
    if (!empty($adiDetalle)) {
      for ($j = 0; $j < $toDet; $j++) {
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(70, 4, '"' . utf8_decode($adiDetalle[$j]['nombre']) . '" : "' . utf8_decode($adiDetalle[$j]['valor']) . '"', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L,R', 1, 'L', 0, 1);
      }
    }
    $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(70, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
    $pdf->Cell(20, 0, '', 'T,R', 1, 'C', 0, 1);
    $totDesc = $totDesc + $xmls->detalles->detalle[$td]->descuento;
  }
  $pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(40, 5, 'Venta Bruta ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->totalSinImpuestos, 1, 1, 'C', 0, 1);
  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(100, 5, utf8_decode(''), 0, 0, 'L', 0, 1);
  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
  $pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, number_format($totDesc, 2, '.', ''), 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Base 12% ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->totalConImpuestos->totalImpuesto[0]->baseImponible, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Base 0% ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->totalConImpuestos->totalImpuesto[1]->baseImponible, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Subtotal S/I ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->totalSinImpuestos, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->totalConImpuestos->totalImpuesto[0]->valor, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L');
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $xmls->infoNotaCredito->valorModificacion, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
  $pdf->Cell(100, 5, utf8_decode('INFORMACIÓN ADICIONAL'), 'T,L,R', 1, 'C', 0, 1);
  $pdf->SetFont('Arial', '', 9);
  $totCampos = count($xmls->infoAdicional->campoAdicional);
  $campoAdi = $xmls->infoAdicional->campoAdicional;
  for ($j = 0; $j < $totCampos; $j++) {
    if ($campoAdi[$j]['nombre'] != 'IdTransac' && $campoAdi[$j]['nombre'] != 'comentario') {
      $pdf->Cell(100, 5, utf8_decode($campoAdi[$j]['nombre']) . ' :' . ' ' . utf8_decode($campoAdi[$j]), 'L,R', 1, 'L', 0, 1);
    }
  }
  $pdf->Cell(100, 5, '', 'B,L,R', 1, 'R', 0, 1);

}
$archivo = $pdf->Output();