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
require '../../core/modules/index/model/DeudasData.php';
require '../../core/modules/index/model/FactFEdetData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/modules/index/model/PdfData.php';
$xmls = pdfData::xmlStruct($_GET['id']);
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Image('../../' . $xmls->cabecera['logo'], 10, 5, 45, "JPG");
$pdf->Ln();
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 5, 'RUC : ' . $xmls->cabecera['ruc'], 0, 1, 'C', 0, 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(95, 7, 'FACTURA ', 0, 1, 'C', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 19);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 7, $xmls->cabecera['numDocumento'], 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(98, 5, $xmls->cabecera['claveAcceso'], 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(100, 5, $xmls->cabecera['comercial'], 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(100, 4, $xmls->cabecera['slogan'], 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(98, 5, $xmls->cabecera['razonSocial'], 0, 0, 'L', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $xmls->cabecera['fechaAuto'], 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'AMBIENTE : ' . $xmls->cabecera['ambiente'], 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, utf8_decode($xmls->cabecera['direccionMatriz']), 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'EMISION : ' . $xmls->cabecera['emision'], 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $xmls->cabecera['resolucion'], 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $xmls->cabecera['obligado'], 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, $xmls->cabecera['claveAcceso'], 0, 1, 'L', 0, 1);
$code = $xmls->cabecera['claveAcceso'];
$pdf->Ln(12);
$pdf->Cell(64, 5, 'IDENTIFICACION COMPRADOR : ' . $xmls->cliente['rucComprador'], 'T,L', 0, 'L', 0, 1);
$pdf->Cell(64, 5, 'FECHA DE EMISION : ' . $xmls->cliente['fechaEmision'], 'T', 0, 'C', 0, 1);
$pdf->Cell(64, 5, 'TELEFONO : ' . implode(",", $xmls->cliente['telefono']), 'T,R', 1, 'C', 0, 1);
$pdf->Cell(192, 5, 'RAZON SOCIAL : ' . $xmls->cliente['nameComercial'], 'R,L', 1, 'L', 0, 1);
$pdf->Cell(192, 5, 'DIRECCION COMPRADOR : ' . $xmls->cliente['direccion'], 'R,L', 1, 'L', 0, 1);
$pdf->Cell(192, 5, 'COMENTARIO : ' . $xmls->cliente['comentario'], 'B,R,L', 1, 'L', 0, 1);
$pdf->Ln();
$pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
$pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
$todProductos = count($xmls->productos);
$toIva = 0;
$toNIva = 0;
$totDet = 0;
for ($td = 0; $td < $todProductos; ++$td) {

  $pdf->Cell(20, 5, $xmls->productos[$td]['codigo'], 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(70, 5, utf8_decode($xmls->productos[$td]['descripcion']), 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, number_format($xmls->productos[$td]['cantidad'], 2, '.', ''), 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, $xmls->productos[$td]['unidad'], 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, number_format($xmls->productos[$td]['precio'], 2, '.', ''), 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, $xmls->productos[$td]['subtotal'], 'R,L', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, $xmls->productos[$td]['subtotal'] * $xmls->productos[$td]['cantidad'], 'R,L', 1, 'C', 0, 1);
  $totDet = $totDet + $xmls->productos[$td]['subtotal'] * $xmls->productos[$td]['cantidad'];
  if ($xmls->productos[$td]['iva'] == 12) {
    $toIva += $totDet;
  } else {
    $toNIva += $totDet;
  }
  if (!empty($xmls->productos[$td]['comentario'])) {
    foreach ($xmls->productos[$td]['comentario'] as $indice => $descripcion) {
      if (!empty($descripcion)) {
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(70, 4, '' . utf8_decode($indice) . ' : ' . utf8_decode($descripcion) . '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L', 0, 'L', 0, 1);
        $pdf->Cell(20, 4, '', 'L,R', 1, 'L', 0, 1);
      }
    }
  }
  $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(70, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
  $pdf->Cell(20, 0, '', 'T,R', 1, 'C', 0, 1);
  /*==========================*/
  $pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(40, 5, 'Venta Bruta', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, $totDet, 1, 1, 'C', 0, 1);
//  $pdf->SetFont('Arial', '', 10);
  $pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
//  $pdf->SetFont('Arial', '', 9);
  $pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
  $pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, number_format(($totDet * $xmls->cabecera['descuentoGeneral']) / 100, 2, '.', ''), 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, $xmls->pago['formaPago'], 'R,L', 0, 'L', 0, 1);
  /*==========================*/
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Base 12% ', 1, 0, 'R', 0, 1);
  $sbIva = number_format($toIva - ($toIva * $xmls->cabecera['descuentoGeneral']) / 100, 2, '.', '');
  $pdf->Cell(20, 5, $sbIva, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, 'Moneda : ' . $xmls->pago['moneda'], 'R,L', 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Base 0% ', 1, 0, 'R', 0, 1);
  $subNIva = number_format($toNIva - ($toNIva * $xmls->cabecera['descuentoGeneral']) / 100, 2, '.', '');
  $pdf->Cell(20, 5, $subNIva, 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, 'Total ' . $xmls->pago['total'] . ' Plazo ' . $xmls->pago['plazo'] . ' ' . $xmls->pago['dias'], 'R,B,L', 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Subtotal sin Impuestos ', 1, 0, 'R', 0, 1);
  $subT = number_format(($toIva - ($toIva * $xmls->cabecera['descuentoGeneral']) / 100) + ($toNIva - ($toNIva * $xmls->cabecera['descuentoGeneral']) / 100),2,'.','');
  $pdf->Cell(20, 5,$subT , 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
  $iva = number_format(($toIva - ($toIva * $xmls->cabecera['descuentoGeneral']) / 100) * 12 /100,2,'.','');
  $pdf->Cell(20, 5,$iva , 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L');
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
  $pdf->Cell(20, 5, number_format($subT + $iva,2,'.',''), 1, 1, 'C', 0, 1);
  $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
  $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
  $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
  $pdf->Cell(100, 5, utf8_decode('INFORMACIÓN ADICIONAL'), 'T,L,R', 1, 'C', 0, 1);
  foreach ($xmls->infAdicional as $indice => $value) {
    $pdf->Cell(100, 5, '' . utf8_decode($indice) . ' : ' . utf8_decode($value) . '', 'L,R', 1, 'L', 0, 1);
  }
  $pdf->Cell(100, 4, '', 'T', 1, 'L', 0, 1);

}
//}
$pdf->Output();