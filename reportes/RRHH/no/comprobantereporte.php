<?php

ob_start();
require __DIR__ . '/core/controller/Executor.php';
require __DIR__ . '/core/controller/Database.php';
require __DIR__ . '/core/controller/Model.php';
require __DIR__ . '/core/modules/index/model/ro_haberdesccabeceraData.php';
require __DIR__ . '/core/modules/index/model/ro_haberdescdetalleData.php';
require __DIR__ . '/core/modules/index/model/ro_periodosData.php';
require __DIR__ . '/core/modules/index/model/ro_empleadosData.php';
require __DIR__ . '/core/modules/index/model/ro_camposdefData.php';
require __DIR__ . '/core/modules/index/model/ro_cabtablaData.php';
include 'core/controller/TCPDF/tcpdf.php';
$cabecera = ro_haberdesccabeceraData::getByIdhd($_GET['id']);
$detalle = ro_haberdescdetalleData::getByIdhd($cabecera->hdid);
$empleado = ro_empleadosData::getByIdName($cabecera->emid);
$campos = ro_camposdefData::getById($cabecera->cdid);
$tipo = ro_cabtablaData::getByTB($cabecera->tbid);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$PDF_HEADER_TITLE="-"; 
$PDF_HEADER_STRING="-";
$PDF_HEADER_IMAGE="-";
$pdf_datos="-";
$pdf_fecha="-";
$pdf_data="-"; 



$pdf->SetHeaderData($PDF_HEADER_TITLE, $PDF_HEADER_STRING, $pdf_datos, $pdf_fecha, $pdf_data);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN)); 
$pdf->SetPrintHeader(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);
// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('dejavusans', '', 11, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->CustomHeaderText = "Header Page 1";
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', 7, "Fecha de emisión: " . date("Y-m-d")  , 0, 1, 0, true, 'L', true);
$pdf->writeHTMLCell(0, 0, '', 13, "Hora de emisión: " . date("H:m:s"), 0, 1, 0, true, 'L', true);
$pdf->writeHTMLCell(0, 0, '', 11, $campos->cddescrip, 0, 1, 0, true, 'C', true); //Campo definible
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));



// Set some content to print

$html6header = '
<table border="1" align="center" cellspacing="0">
<thead>
<tr>
<td>Nº de Cuota</td>
<td>Periodo</td>
<td>Año</td>
<td>Valor</td>
</tr>
</thead>
<tbody>';
$suma=0;
foreach($detalle as $detcab){
    $periodo = ro_periodosData::getByIdMonth($detcab->peid);
    $cuota=$detcab->ddcuota;
    $per=$periodo->pedescrip;
    $anio=$detcab->ddanio;
    $valor=$detcab->ddvalor;
    
    
    
$html6.=<<<EOD

<tr><td>$cuota</td><td>$per</td><td>$anio</td><td>$valor</td></tr>

EOD;
    $suma += $valor ;
}

$html6footer='
<tr><td colspan="2"></td><td>Totales</td><td>'.$suma.'</td></tr>
</tbody>
</table>';
$pdf->Multicell(0, 0, "Transacción Nº: " .$cabecera->hdid, 0, 'L', 0, 0, 14, 20, true, 0,false, true, 0 ,'M'); //Numero de transaccion
$pdf->Multicell(0, 0, "Fecha: " .$cabecera->hdfecha, 0, 'L', 0, 0, 60, 20, true, 0,false, true, 0 ,'M'); //Fecha de transaccion
$pdf->Multicell(0, 0, "Tipo: " .$tipo->tbdescrip, 0, 'L', 0, 0, 106, 20, true, 0,false, true, 0 ,'M'); // Tipo
$pdf->Multicell(0, 0, "Total: " . $cabecera->hdtotal, 0, 'L', 0, 0, 150, 20, true, 0,false, true, 0 ,'M'); //Total de la transaccion
$pdf->Ln();
$pdf->Multicell(0, 0, "Empleado: " . $empleado->emidlegal . " - " .$empleado->emapellido . " " . $empleado->emnombre, 0, 'L', 0, 0, 14, 30, true, 0,false, true, 0 ,'M'); //Empleado
$pdf->Multicell(0, 0, "Observación: " .$cabecera->hdobserva, 0, 'L', 0, 0, 110, 30, true, 0,false, true, 0 ,'M');
$pdf->writeHTMLCell(180, 0, '14','40', $html6header . $html6 . $html6footer, 115, true, 'C', true);
$pdf->Multicell(0, 0, "____________________", 0, 'L', 0, 0, 14, 110, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
$pdf->Multicell(0, 0, "____________________", 0, 'L', 0, 0, 64, 110, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
$pdf->Multicell(0, 0, "____________________", 0, 'L', 0, 0, 114, 110, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
$pdf->Multicell(0, 0, "Elaborado por:", 0, 'L', 0, 0, 14, 115, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
$pdf->Multicell(0, 0, "Autorizado por:", 0, 'L', 0, 0, 64, 115, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
$pdf->Multicell(0, 0, "Recibí conforme:", 0, 'L', 0, 0, 114, 115, true, 0,false, true, 0 ,'M');// ---------------------------------------------------------
ob_end_clean();
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('FichaEmpleado.pdf', 'I');

//============================================================+
// END OF FILE
//===========================================================