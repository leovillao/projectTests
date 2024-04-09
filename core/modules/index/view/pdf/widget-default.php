<?php
$pathXml = EmpresasData::getByRuc($_SESSION['ruc']);
$idFile = FilesData::getById($_GET['id']);
$mails = ProveeData::getByRuc($idFile->fi_er_ruc);
$xml = RsmData::getById($idFile->fi_idfile)->fl_file;
$factura = $idFile->fi_docum;
if (!empty($pathXml->logo)){
$logo = $pathXml->logo;
}else{
$logo = '';
}
$xmlFile = RsmData::getById($idFile->fi_idfile);
foreach ($xmlFile as $key => $value) {
$content = iconv('UTF-8', 'UTF-8//IGNORE', $value);
$xmlDecode = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
$xml = simplexml_load_string($xmlDecode, 'SimpleXmlElement', LIBXML_NOCDATA);
foreach ($xml->comprobante as $comp) {
$xmls = simplexml_load_string($comp, 'SimpleXmlElement', LIBXML_NOCDATA);

$pdf = new PDF();
$pdf->AddPage();
$pdf->Cell(80);
$nombre = 0;
$pdf->Image($logo, 5, 5, 100);
$pdf->Ln();
foreach ($xmls->infoCompRetencion as $valor) {
foreach ($xmls->infoTributaria as $valores) {
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->SetTextColor(255, 0, 0);
$pdf->Cell(98, 7, 'Retencion ', 0, 1, 'C', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 19);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(98, 7, $valores->estab . '-' . $valores->ptoEmi . '-' . $valores->secuencial, 0, 1, 'C', 0, 1);
$nombre = $valores->estab . $valores->ptoEmi . $valores->secuencial;
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$pdf->Cell(95, 7, $valor->fechaEmision, 0, 1, 'C', 0, 1);
$pdf->Ln(12);
$periodoFiscal = $valor->periodoFiscal;
}
}
$pdf->SetFont('Arial', '', 9);
foreach ($xml->comprobante as $comp) {
$xmls = simplexml_load_string($comp, 'SimpleXmlElement', LIBXML_NOCDATA);
foreach ($xmls->infoCompRetencion as $valor) {
foreach ($xmls->infoTributaria as $valores) {
$pdf->SetFont('Arial', '', 16);
$pdf->Cell(95, 5, $valores->razonSocial, 0, 0, 'L', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, 'Autorizacion : ', 0, 1, 'L', 0, 1);
$pdf->Cell(95, 5, 'RUC : ' . $valores->ruc, 0, 0, 'L', 0, 1);
$pdf->Cell(95, 5, $xml->numeroAutorizacion, 0, 1, 'L', 0, 1);
$pdf->Cell(95, 5, 'Matriz : ' . $valores->dirMatriz, 0, 0, 'L', 0, 1);
$pdf->Cell(95, 5, 'Fecha : ' . $xml->fechaAutorizacion, 0, 1, 'L', 0, 1);
$pdf->Cell(95, 5, 'Establecimiento : ' . $valor->dirEstablecimiento, 0, 0, 'L', 0, 1);
switch ($valores->ambiente) {
case 1:
$pdf->Cell(95, 5, 'Ambiente Pruebas ', 0, 1, 'L', 0, 1);
break;
default:
$pdf->Cell(95, 5, 'Ambiente Produccion ', 0, 1, 'L', 0, 1);
break;
}
$pdf->Cell(95, 5, 'Contribuyente Especial : ' . $valor->contribuyenteEspecial, 0, 0, 'L', 0, 1);
switch ($valores->tipoEmision) {
case 1:
$pdf->Cell(95, 5, 'Emision : Normal', 0, 1, 'L', 0, 1);
break;
default:
$pdf->Cell(95, 5, 'Emision : Normal', 0, 1, 'L', 0, 1);
break;
}
$pdf->Cell(95, 5, 'Obligado a llevar Contabilidad : ' . $valor->obligadoContabilidad, 0, 0, 'L', 0, 1);
$pdf->Cell(95, 5, 'Clave : ', 0, 1, 'L', 0, 1);
$pdf->Ln();
$pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
$code = $valores->claveAcceso;
$pdf->Code128(100, 72, $code, 105, 8);
$pdf->SetXY(109, 80);
$pdf->Write(5, $code);
//$pdf->Line(10, 85, 200, 85);

}
}
$pdf->Ln();
$pdf->Cell(190, 8, 'Razon Social :' . ' ' . $valor->razonSocialSujetoRetenido, 'T,L,R', 1, 'L', 0, 0);
//$pdf->Cell(95, 5, ' ', 'T,R', 1, 'L', 0, 1);
switch ($valor->tipoIdentificacionSujetoRetenido) {
case 04:
$pdf->Cell(190, 5, 'Ruc :' . ' ' . $valor->identificacionSujetoRetenido, 'L,R', 1, 'L', 0, 0);
break;
case 05:
$pdf->Cell(190, 5, 'Cedula :' . ' ' . $valor->identificacionSujetoRetenido, 'L,R', 1, 'L', 0, 0);
break;
}
$totAdi = count($xmls->infoAdicional->campoAdicional);
$campoAdi = $xmls->infoAdicional->campoAdicional;
for ($i = 0; $i < $totAdi; ++$i) {
$correo = $campoAdi[$i]['nombre'] == "correoCliente";
if (isset($correo)) {
if ($campoAdi[$i]['nombre'] == "correoCliente") {

$pdf->Cell(95, 5, 'Correo :' . ' ' . $campoAdi[$i], 'R', 1, 'L', 0, 0);
}
} else {
$pdf->Cell(95, 5, ' ', 'R', 1, 'L', 0, 0);
}
}

$pdf->Cell(190, 5, utf8_decode('Fecha Emisión :') . ' ' . $valor->fechaEmision, 'L,R', 1, 'L', 0, 1);
$totAdi = count($xmls->infoAdicional->campoAdicional);
$campoAdi = $xmls->infoAdicional->campoAdicional;
for ($i = 0; $i < $totAdi; ++$i) {
$telefono = $campoAdi[$i]['nombre'] == "telefonoCliente";
if (isset($telefono)) {
if ($campoAdi[$i]['nombre'] == "telefonoCliente") {
$pdf->Cell(95, 5, 'Telefono :' . ' ' . $campoAdi[$i], 'R', 1, 'L', 0, 0);
}
} else {
$pdf->Cell(80, 5, ' ', 'R', 1, 'L', 0, 0);
}
}
if (isset($valor->direccionComprador)) {
$pdf->Cell(190, 5, utf8_decode('Dirección :') . ' ' . $valor->direccionComprador, 'L,B,R', 1, 'L', 0, 1);
} else {
$pdf->Cell(190, 5, ' ', 'L,B,R', 1, 'L', 0, 1);
}
$pdf->Ln();
//$pdf->Line(10, 108, 200, 108);
//$pdf->Ln();
$pdf->Cell(23, 5, utf8_decode('Comprobante'), 1, 0, 'C', 0, 1);
$pdf->Cell(28, 5, utf8_decode('Número'), 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, utf8_decode('Fecha emision'), 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, 'Ejercicio fiscal', 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, 'Base Imponible', 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, 'Impuesto', 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, 'Porcentaje', 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, 'Valor retenido', 1, 1, 'C', 0, 1);
$totDet = COUNT($xmls->impuestos->impuesto);
$td = 0;
$totDesc = 0;
for ($td = 0; $td < $totDet; ++$td) {
if ($xmls->impuestos->impuesto[$td]->codigo == 1){
$tipoRetencion = 'RENTA';
}else{
$tipoRetencion = 'IVA';
}
$pdf->Cell(23, 5, 'Factura', 1, 0, 'C', 0, 1);
$pdf->Cell(28, 5, $xmls->impuestos->impuesto[$td]->numDocSustento, 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, $xmls->impuestos->impuesto[$td]->fechaEmisionDocSustento, 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, $periodoFiscal, 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, $xmls->impuestos->impuesto[$td]->baseImponible, 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5,$tipoRetencion , 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, $xmls->impuestos->impuesto[$td]->porcentajeRetener, 1, 0, 'C', 0, 1);
$pdf->Cell(23, 5, $xmls->impuestos->impuesto[$td]->valorRetenido, 1, 1, 'C', 0, 1);
$totDesc = $totDesc + $xmls->impuestos->impuesto[$td]->valorRetenido;
}
$pdf->Cell(129, 5, '', 0, 0, 'L', 0, 1);
$pdf->Cell(40, 5, '', 0, 0, 'R', 0, 1);
$pdf->Cell(20, 5, '', 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(130, 5, '', 0, 1, 'L', 0, 1);
/*$pdf->SetFont('Arial', '', 9);
$pdf->Cell(40, 5, '', 1, 0, 'R', 0, 1);
$pdf->Cell(20, 5, '', 1, 1, 'C', 0, 1);*/

if (!is_null($xmls->infoAdicional->campoAdicional)) {
// total datos en el array */
$totAdi = count($xmls->infoAdicional->campoAdicional);
$campoAdi = $xmls->infoAdicional->campoAdicional;
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(130, 5, utf8_decode('Información Adicional'), 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 9);
for ($i = 0; $i < $totAdi; ++$i) {
$pdf->Cell(130, 5, $campoAdi[$i]['nombre'] . ' ' . $campoAdi[$i], 1, 1, 'L', 0, 1);
}
}
}
$documento = $pdf->Output($nombre . '.pdf', 'I');
}
}
/*
$to = $pathXml->mail;
$from = $pathXml->mail; // Correo de cliente en este caso PROCHEFF
$username = $to;
$password = $pathXml->pass_mail;
$host = $pathXml->host;
$fromname = 'Documentos Electronicos';
$toname = $fromname;
$subject = 'Envio de Archivos de Retencion';
// CREATE THE MAIL
if ($pathXml->autentic == 1) {
$varSmtp = true;
} elseif ($pathXml->autentic == 0) {
$varSmtp = false;
}
if ($pathXml->encry == 1) {
$varEncr = 'tls'; // Enable TLS encryption, `ssl` also accepted
} elseif ($pathXml->encry == 0) {
$varEncr = 'ssl'; // Enable TLS encryption, `ssl` also accepted
}
// todo Inicio de envio de msj por medio de PHPMAILER
// SETUP FOR SMTP
$mail = new PHPMailer(true);
$mail->SMTPDebug = 0; // Enable verbose debug output
$mail->isSMTP(); // Set mailer to use SMTP
$mail->Host = $host; // Specify main and backup SMTP servers
$mail->SMTPAuth = $varSmtp; // Enable SMTP authentication
$mail->Username = $username; // SMTP username
$mail->Password = $password; // SMTP password
$mail->SMTPSecure = $varEncr; // Enable TLS encryption, `ssl` also accepted
$mail->Port = $pathXml->puerto; // TCP port to connect to
//Recipients
$mail->setFrom($from, $fromname);
$mail->addAddress($mails->mail1); // Direccion de correo de destino
if(!empty($mails->mail2)){
$mail->AddCC($mails->mail2); // mail para copiar el correo enviado // Correo del Cliente
}
if(!empty($mails->mail3)){
$mail->AddCC($mails->mail3); // mail para copiar el correo enviado // Correo del Cliente
}

// $mail->addReplyTo('leo.villao@yahoo.com'); // Direccion para copia de correo
//Attachments
$xmlFile = ($pathXml->path_xml .'/autorizados/envio-'.$factura.'.xml');
//    $pdfFile = ($documento);
$mail->AddAttachment($xmlFile);
$mail->addStringAttachment($xmlFile,'docXml.xml','base64','application/octet-stream');
//    $mail->addStringAttachment($pdfFile,'documento.pdf' , 'base64', 'application/pdf');
//Content
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = $subject;
$mail->Body = 'cuerpo del Correo';
$mail->AltBody = 'PDF Attached';
//    $mail->Send();
if ($mail->Send()) {
$msjMail = 1;
} else {
$msjMail = $mail->ErrorInfo;
}
return $msjMail;*/