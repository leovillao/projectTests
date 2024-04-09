<?php
session_start();
include "core/controller/Core.php";
include "core/controller/Database.php";
include "core/controller/Executor.php";
include "core/controller/Model.php";
include "core/modules/index/model/UserData.php";
include "core/modules/index/model/FilesData.php";
include "core/modules/index/model/OperationdetData.php";
include "core/modules/index/model/ProductData.php";
include "core/modules/index/model/ConfigurationData.php";
include "core/controller/tcpdf/tcpdf.php";
$sell = FilesData::getById($_GET["id"]);
$operations = OperationdetData::getFileId($_GET["id"]);
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: WriteHTML and RTL support
 * @author Nicola Asuni
 * @since 2008-03-04
 */
// Include the main TCPDF library (search for installation path).
//require_once('tcpdf_include.php');
// create new PDF document
$width = 80;
$height = auto;
/*$pageLayout = array($width, $height); //  or array($height, $width)
$pdf = new TCPDF('mm', 'mm', $pageLayout, true, 'UTF-8', false);*/
$medidas = array(80, 600); // Ajustar aqui segun los milimetros necesarios;
$pdf = new TCPDF('P', 'mm', $medidas, true, 'UTF-8', false);
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
/*$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 006');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
//$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(3,5,3);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
  require_once(dirname(__FILE__) . '/lang/eng.php');
  $pdf->setLanguageArray($l);
}
// set font
$pdf->SetFont('dejavusans', '', 8);
// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
// output the HTML content
$pdf->writeHTML($html, false, false, false, false, '');
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table
// add a page
$pdf->AddPage();
// create some HTML content
$html = '<table cellpadding="2px" cellspacing="2px">';
$html .= '
<thead>
    <tr>
        <th align="center">CANT</th>
        <th align="center">PRODUCTO</th>
        <th align="right">VALOR</th>
    </tr>    
</thead>';
$html .= '<tbody>';
foreach ($operations as $operation) {
  $html .= '<tr>
        <td align="center">' . $operation->odtcandig . '</td>
        <td align="center">' . ProductData::getById($operation->itid)->itname . '</td>
        <td align="right">' . $operation->odbruta . '</td>
    </tr>';
$subtotal = $subtotal + $operation->odbruta;
$descuento = $descuento + $operation->odtdscto;
$iva = $iva + $operation->odiva;
$total = $total + $operation->odtotal;
}
$html .= '<tr>
<td></td>
<td align="center">Subtotal</td>
<td align="right">'.$subtotal.'</td>
</tr>';
$html .= '<tr>
<td></td>
<td align="center">Descuento</td>
<td align="right">'.$descuento.'</td>
</tr>';
$html .= '<tr>
<td></td>
<td align="center">Iva</td>
<td align="right">'.$iva.'</td>
</tr>';
$html .= '<tr>
<td></td>
<td align="center">Total</td>
<td align="right">'.$total.'</td>
</tr>';
$html .= '</tbody></table>';
// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');
// Print some HTML Cells
//$pdf->SetFillColor(255, 255, 0);
//$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'L', true);
//$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 1, true, 'C', true);
//$pdf->writeHTMLCell(0, 0, '', '', $html, 'LRTB', 1, 0, true, 'R', true);
// reset pointer to the last page
$pdf->lastPage();
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
// Print a table
//Close and output PDF document
//$pdf->Output('https://iflujo.e-piramide.net/pdf/example_006.pdf', 'F');
$pdf->Output('example_001.pdf', 'I');
//$pdf->Output(__DIR__ . '/pdf/example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
