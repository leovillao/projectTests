<?php

include 'core/controller/TCPDF/tcpdf.php';
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$PDF_HEADER_TITLE="FICHA DE DATOS DEL EMPLEADO"; 
$PDF_HEADER_STRING="Fecha: _____/____/____";
$PDF_HEADER_IMAGE="Nombres: __________________________ Apellidos: __________________________ Sexo(M/F): __";
$pdf_datos="FICHA DE DATOS DEL EMPLEADO";
$pdf_fecha="Fecha: _____/____/____";
$pdf_data="Nombres: __________________________ Apellidos: __________________________ Sexo(M/F): __";

$pdf->SetHeaderData($PDF_HEADER_TITLE, $PDF_HEADER_STRING, $pdf_datos, $pdf_fecha, $pdf_data);

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN)); 
$pdf->SetPrintHeader(false);
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
//str_rep
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_);

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
$pdf->SetFont('times', '', 13, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->CustomHeaderText = "Header Page 1";
$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', 1, "FICHA DE DATOS DEL EMPLEADO", 0, 1, 0, true, 'C', true);
$pdf->writeHTMLCell(0, 1, '', 10, "'Fecha:________/____/____", 0, 1, 0, true, 'L', true);
$pdf->writeHTMLCell(0, 1, '', 20, "Nombres: ______________________ Apellidos: ______________________ Sexo(M/F): __", 0, 1, 0, true, 'L', true);
// set cell padding
$pdf->setCellPaddings(1, 1, 1, 1);

// set cell margins
$pdf->setCellMargins(1, 1, 1, 1);

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));



// Set some content to print
$html = '
Cedula Id.:  _________________
Fecha Nacimiento: ____/___/___
Pais Nacimiento: ___________
Provincia Nac.: ____________
Ciudad Nac.: ______________
Estado Civil: ______________
Fecha Matrimonio: ____/__/__
Conyuge: _________________
Conyuge Trabaja (S/N): ______
';

$html1 = '
Profesión:  ________________
Nacionalidad: ______________
Instrucción: _________________
Tipo Cuenta: ________________
No. Cuenta: _______________
';

$html2 = '
Tipo Sangre:  ________________
Estatura (MTS): _____________
Peso (Kilos): ______________
Vacunas:  ___________________________________
Alergias: ___________________________________
Enfermedades: _______________________________
';

$html3 = '
País Domicilio:  ______________
Prov. Domicilio: _____________
Ciudad Dom.: _______________
Calles: _____________________
Num. Villa: _________________
Alquila Vivienda (S/N): _______
Teléfono 1: _________________
Teléfono 2: __________________
Celular: _____________________
E-mail: _____________________
';

$html4 = '
<label>Cargas Familiares</label><br>
<table border="1" align="center" cellspacing="0">
<tr>
<td>Parentesco</td>
<td>Nombre /Apellido</td>
<td>Fec. Nacimiento</td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
</table>
';

$html5 = '
<label>Estudios Realizados</label><br>
<table border="1" align="center" cellspacing="0">
<tr>
<td>Nombre/Apellido</td>
<td>Teléfono</td>
<td>Relación</td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
<td></td>
</tr>
</table>
';

$html6 = '
<label>Estudios Realizados</label><br>
<table border="1" align="center" cellspacing="0">
<tr>
<td>Institución</td>
<td>Título/Diploma</td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
<tr>
<td></td>
<td></td>
</tr>
</table>
';
$pdf->Cell('33', '', "Datos Personales", 0, 'L', true);
$pdf->Cell('101', '', "Clasificaciones", 0, 'L', true);
$pdf->MultiCell(70, 10, $html , 1, 'L', 1, 0, '', '35', true, 40, 'T',true);
$pdf->MultiCell(70, 5, $html1, 1, 'L', 1, 0, '', '', true, 40, 'T', true);
$pdf->MultiCell(30, 35, "FOTO", 1, 'C', 1, 0, '160', '30', true, 0,false, true, 40 ,'M');
$pdf->Ln();
$pdf->Cell('155', '10', "Salud", 0, 'L', true);
$pdf->MultiCell(102, 5, $html2 , 1, 'J', 1, 1, 87, 75, true, 0, false, true, 40, 'M', true);
//$pdf->Cell('', '70', "Ubicación/Domicilio", 0, 'L', true);
$pdf->MultiCell(70, 5, "Ubicacion/Domicilio" , 0, 'L', 1, 0, 15, '90', true, 40, 'T', true);
$pdf->MultiCell(70, 5, $html3 , 1, 'L', 1, 0, '', '100', true, 40, 'T', true);
$pdf->writeHTMLCell(102, 0, '87','117', $html4, 115, true, 'L', true);
$pdf->writeHTMLCell(102, 0, '15','170', $html5, 115, true, 'L', true);
$pdf->writeHTMLCell(70, 0, '120','170', $html6, 115, true, 'L', true);
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('FichaEmpleado.pdf', 'D');

//============================================================+
// END OF FILE
//===========================================================