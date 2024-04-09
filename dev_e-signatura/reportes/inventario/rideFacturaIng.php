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
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/ProductData.php';
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

/*if (VwPagosCreData::getAllByIdFiles($_GET['id'])) {
    $detPagos = VwPagosCreData::getById($_GET['id']);
}else{
    $detPagos = VwPagosData::getById($_GET['id']);
}*/
$rowFactura = FactFEData::getById($_GET['id']);/* Obntego la Cabecera de la factura */

$rowDetFacturas = OperationdetData::getByIdFiId($_GET['id']);/* Detalle de la factura */
$rowDifFacturas = OperationdifData::getByIdFiId($_GET['id']);/* Detalle de la factura */
//$rowDifFacturas = FactFEdifData::getByIdFcNumber($_GET['id']);/* Detalle de la factura */
$empresa = EmpresasData::getEmpresaData();
$pathXml = EmpresasData::getEmpresaData();
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
//    $this->Image("../../".$_SESSION['logoFooter'], 20, null, 180); // ok
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
$ambiente = "";
$emision = '';
//$pdf->Image($logo, 35, 5, 45, "JPG");
$pdf->Ln();
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 5, 'RUC : ' . $filesDocumento->fi_er_ruc, 0, 1, 'C', 0, 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(95, 7, 'FACTURA ', 0, 1, 'C', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 19);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(90, 7, $filesDocumento->fi_codestab . '-' . $filesDocumento->fi_ptoemi . '-' . substr($filesDocumento->fi_docum,6,9), 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(100, 5, utf8_decode($filesDocumento->fi_er_comercial), 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(100, 4, '', 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(98, 5, $filesDocumento->fi_er_name, 0, 0, 'L', 0, 1);
$pdf->SetFont('Arial', '', 8);
//$empresa = $rowFactura->razonSocial;
$pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, utf8_decode(''), 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :', 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
$pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :', 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
$code = $claveacceso;
if(!empty($claveacceso)) {
    $pdf->Code128(105, 73, $code, 100, 9);
}
$pdf->Ln(12);

$nombre = $filesDocumento->fi_docum;
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(100, 5, 'RAZON SOCIAL : ' . $empresa->em_nombre, 0, 0, 'L', 0, 1);
$pdf->Cell(98, 5, 'IDENTIFICACION COMPRADOR : ' . $empresa->em_ruc, 0, 1, 'L', 0, 1);
$pdf->Cell(98, 5, 'DIRECCION COMPRADOR : ' . $empresa->em_dirmatriz, 0, 1, 'L', 0, 1);
$pdf->Cell(98, 5, 'FECHA DE EMISION : ' . $filesDocumento->fi_fechadoc, 0, 1, 'L', 0, 1);

if (!empty($filesDocumento->fi_glosa)) {
    $pdf->Cell(95, 5, 'COMENTARIO :' . ' ' . $filesDocumento->fi_glosa, 0, 1, 'L', 0, 0);
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
$valIVA = 0;
$totalIVA = 0;
$totalIVANO = 0;
if ($rowDetFacturas){
    foreach ($rowDetFacturas as $rowDetFactura) {
        $pdf->Cell(20, 5, ProductData::getById($rowDetFactura->itid)->itcodigo, 1, 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode(ProductData::getById($rowDetFactura->itid)->itname), 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->odcandig, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->odcostoudig, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 0, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDetFactura->odcostotot, 1, 1, 'C', 0, 1);
        if ($rowDetFactura->iva != 0) {
            $valIVA += $rowDetFactura->odiva; // se suman los valores de iva para mostrarlos en el campo de valor iva del ride
            $totalIVA += $rowDetFactura->odcostotot; // se suma el valor q graba iva en la variable de totalIVA para dividirlos
        } else {
            $totalIVANO += $rowDetFactura->odcostotot;  // se suma el valor q graba iva en la variable de totalIVANO para dividirlos
        }
    }
}
if ($rowDifFacturas){
    foreach ($rowDifFacturas as $rowDifFactura) {
        $pdf->Cell(20, 5, ProductData::getById($rowDifFactura->itid)->itcodigo, 1, 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode(ProductData::getById($rowDifFactura->itid)->itname), 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->odcandig, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->odcostouni, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 0, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $rowDifFactura->odcostotot, 1, 1, 'C', 0, 1);
        if ($rowDifFactura->iva != 0) {
            $valIVA += $rowDifFactura->odiva; // se suman los valores de iva para mostrarlos en el campo de valor iva del ride
            $totalIVA += $rowDifFactura->odcostotot; // se suma el valor q graba iva en la variable de totalIVA para dividirlos
        } else {
            $totalIVANO += $rowDifFactura->odcostotot;  // se suma el valor q graba iva en la variable de totalIVANO para dividirlos
        }
    }
}

$pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
$pdf->Cell(40, 5, 'Subtotal ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, number_format($totalIVA + $totalIVANO,2), 1, 1, 'R', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
$pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, validaFormato($totDesc), 1, 1, 'R', 0, 1);

$pdf->Cell(100, 5, 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 'T,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(40, 5, 'base 12% ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, $totalIVA, 1, 1, 'R', 0, 1);
$pdf->Cell(100, 5, 'Moneda : ' . $pathXml->em_moneda, 'R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(40, 5, 'base 0% ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, $totalIVANO, 1, 1, 'R', 0, 1);
$pdf->Cell(100, 5, 'Total ' . $totalIVA + $totalIVANO . ' ,  Plazo ' . $filesDocumento->fi_plazo . ' Dias ', 'R,B,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(40, 5, 'SubTotalSin Impuestos ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, number_format($totalIVA + $totalIVANO,2), 1, 1, 'R', 0, 1);
$pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, number_format($valIVA, 2), 1, 1, 'R', 0, 1);
$pdf->Cell(100, 5, '', 0, 0, 'L');
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, number_format($valIVA + $totalIVA + $totalIVANO, 2), 1, 1, 'R', 0, 1);
$pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
$pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
$pdf->Cell(100, 5, utf8_decode('INFORMACIÓN ADICIONAL'), 'T,L,R', 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 9);
//$campoAdi = $xmls->infoAdicional->campoAdicional;
//for ($j = 0; $j < $totCampos; $j++) {
//  if ($campoAdi[$j]['nombre'] != 'IdTransac' && $campoAdi[$j]['nombre'] != 'comentario') {
$pdf->Cell(100, 5, "DireccionComprador :".utf8_decode($rowFactura->direccionComprador), 'L,R', 1, 'L', 0, 1);
$pdf->Cell(100, 5, "Telefonos :".utf8_decode($rowFactura->telefono1), 'L,R', 1, 'L', 0, 1);
$pdf->Cell(100, 5, "Correos:".utf8_decode($emails), 'L,R', 1, 'L', 0, 1);
//  }
//}
$pdf->Cell(100, 5, '', 'B,L,R', 1, 'R', 0, 1);
//}
$archivo = $pdf->Output();

function validaFormato($number){
    return number_format($number,2,',','');
}
