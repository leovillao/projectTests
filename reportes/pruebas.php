
<?php

echo PdfData::GeneraPdfFactura("1710202101091292520300110010030000061251710202116","001003000006125",1,2159);

class PdfData {

  /** RECIBE EL ID DE LA TABLA RSMFILE (ARCHIVO XML), EL NUMERO DE FACTURA , EL RUC DEL CLIENTE**/
  public static function GeneraPdfFactura($claveAcceso, $numFact, $idCliente, $idFiles) {
    $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $emails = '';
    $clientMails = PersonData::getById($idCliente);
    if ($clientMails->ceemail1 != '') {
      $emails .= $clientMails->ceemail1 . ',';
    }
    if ($clientMails->ceemail2 != '') {
      $emails .= $clientMails->ceemail2 . ',';
    }
    if ($clientMails->ceemail3 != '') {
      $emails .= $clientMails->ceemail3 . ',';
    }
    /** EL ARCHIVO DEL WEBSERVICES */
    $ch = curl_init("http://pyme.e-piramide.net/downxml/getXml.php");
    $fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $claveAcceso);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $xml = curl_exec($ch);
    if (curl_errno($ch)) {
      $xml = curl_error($ch);
    }
    curl_close($ch);
    $factura = $numFact;
    $xmlFile = iconv('UTF-8', 'UTF-8//IGNORE', $xml);
    $xmls = self::xmlStruct($idFiles); // Funcion que que llama los datos de las tablas para crear el pdf
    $nombre = $xmls->cabecera['numDocumento'];
    $pdf = new PDFT();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Image($xmls->cabecera['logo'], 10, 5, 45, "JPG");
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
      $pdf->Cell(20, 5, number_format(0, 2, ',', ''), 'R,L', 0, 'C', 0, 1);
      $pdf->Cell(20, 5, number_format($xmls->productos[$td]['total'], 2, ',', ''), 'R,L', 1, 'C', 0, 1);
      $totDet = $totDet + $xmls->productos[$td]['total'];
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
      $pdf->Cell(20, 5, number_format($totDet, 2, ',', ''), 1, 1, 'C', 0, 1);
//  $pdf->SetFont('Arial', '', 10);
      $pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
//  $pdf->SetFont('Arial', '', 9);
      $pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
      $pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, number_format($xmls->cabecera['valorDescuentoGen'], 2, '.', ''), 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, $xmls->pago['formaPago'], 'R,L', 0, 'L', 0, 1);
      /*==========================*/
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Base 12% ', 1, 0, 'R', 0, 1);
      $sbIva = number_format($xmls->cabecera['ivasi'], 2, '.', '');
      $pdf->Cell(20, 5, $sbIva, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, 'Moneda : ' . $xmls->pago['moneda'], 'R,L', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Base 0% ', 1, 0, 'R', 0, 1);
      $subNIva = number_format($xmls->cabecera['ivano'], 2, '.', '');
      $pdf->Cell(20, 5, $subNIva, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, 'Total ' . number_format($xmls->pago['total'], 2, ',', '') . ' Plazo ' . $xmls->pago['plazo'] . ' ' . $xmls->pago['dias'], 'R,B,L', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Subtotal sin Impuestos ', 1, 0, 'R', 0, 1);
      $subT = number_format($sbIva + $subNIva, 2, '.', '');
      $pdf->Cell(20, 5, $subT, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
      $iva = number_format($xmls->cabecera['valorIva'], 2, '.', '');
      $pdf->Cell(20, 5, $iva, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, '', 0, 0, 'L');
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, number_format($xmls->cabecera['totalPagar'], 2, '.', ''), 1, 1, 'C', 0, 1);
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
//        $pdf->Output();
    $pdf->Output('D');


//    file_put_contents('xml/pdf/Fact' . $nombre . '.pdf', $archivo);
//    file_put_contents('xml/autorizados/Fact' . $nombre . '.xml', $xmlFile);
//    /*==============================================================================*/
////        $idFile = FilesData::getById($id);
//    $username = $pathXml->mail;
//    $pass = $pathXml->pass_mail;
//    $autnt = $pathXml->autentic;
//    $host = $pathXml->host;
//    $puerto = $pathXml->puerto;
//    $encryption = $pathXml->encry;
//    $title = $pathXml->em_comercial;
//    $mail = new PHPMailer();
//    //Server settings
//    $mail->SMTPDebug = 0;                                   //Alternative to above constant
//    $mail->isSMTP();                                        // Send using SMTP
//    $mail->Host = $host;                         // Set the SMTP server to send through
//    $autentication = false;
//    if ($autnt == 1) {
//      $autentication = true;
//    }
//    $mail->SMTPAuth = $autentication;                                 // Enable SMTP authentication
//    $mail->SMTPAutoTLS = $autentication; // Enable SMTP authentication
//    $mail->Username = $username;                  // SMTP username
//    $mail->Password = $pass;                           // SMTP password
//    $tls = 'ssl';
//    if ($encryption == 1) {
//      $tls = 'tls';
//    }
//    $mail->SMTPSecure = $tls;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
//    $mail->Port = $puerto;                                      // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
//    $mail->CharSet = 'UTF-8';
//    //Recipients
//    $mail->setFrom($username);
//    $msjNoCorreosCliente = "";
//    if (empty($emails)) {
//      $msjNoCorreosCliente = "1-Documento no pudo ser enviado a Cliente , no registra correos en su ficha";
//    }
//    $numChar = substr_count($emails, ','); //Cuento la coma que es el caracter especial para poder crear los array correspondientes de las direcciones
//    if ($numChar <= 1) { // Si este es igual a uno es por que es solo un correo y no necesito crear un array
//      $mail->addAddress(trim($emails, ','));
//    } else { // la otra opcion es que sea mas de uno asi podra crear el array y poder enviar el duplicado a varios correos
//      $email = trim($emails, ',');
//      $mails = explode(',', $email);
//      for ($i = 0; $i <= count($mails); $i++) {
//        $mail->addAddress($mails[$i]);
//      }
//    }
//    if (!empty($pathXml->em_email1)) {
//      $mail->addBCC($pathXml->em_email1);
//    }
//    if (!empty($pathXml->em_email2)) {
//      $mail->addBCC($pathXml->em_email2);
//    }
//    if (!empty($pathXml->em_email3)) {
//      $mail->addBCC($pathXml->em_email3);
//    }
//    /*=========== COPIAS DE LOS CORREOS ENVIADOS =========*/
//    // Attachments
//    $mail->AddStringAttachment($archivo, 'xml/pdf/Fact' . $nombre . '.pdf');          // Add attachments
//    $mail->AddStringAttachment($xmlFile, 'xml/autorizados/Fact' . $nombre . '.xml');          // Add attachments
//    // Content
//    $mail->isHTML(true);                                  // Set email format to HTML
//    $mail->Subject = $title . ' , Factura # ' . $nombre;
//    $mail->Body = '
//    <p>Estimado cliente</p>
//    <p>' . $title . ', le informa que el documento electrónico ' . $nombre . ' adjunto ha sido generado con éxito</p>';
//    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
//    $msj = '';
//    if ($mail->send()) {
//      if (empty($msjNoCorreosCliente)) {
//        $msj = '1-Mensaje enviado con exito';
//      } else {
//        $msj = $msjNoCorreosCliente;
//      }
//    } else {
//      $msj = "0-Mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
//    }
//    return $msj;
    /*==============================================================================*/
  }

  public static function xmlStruct($idFiles)
  {
    $empresa = EmpresasData::getByRuc($_SESSION['ruc']);
    $docCab = FilesData::getByIdOne($idFiles);
    $cliente = PersonData::getById($docCab->ceid);
    $docDets = OperationdetData::getFileId($idFiles);
    $docDifs = OperationdifData::getFileId($idFiles);
    $pagos = DeudasData::getByFiId($idFiles);
    $plazo = $docCab->fi_plazo;
    $formaPago = 20;
    if (FormasData::getById($cliente->id_pago)->cfcodSri) {
      $formaPago = FormasData::getById($cliente->id_pago)->cfcodSri;
    }
    $formaTexto = "";
    switch ($formaPago) {
      case 1:
        $formaTexto = 'SIN UTILIZACION DEL SISTEMA FINANCIERO';
        break;
      case 2:
        $formaTexto = 'CHEQUE PROPIO';
        break;
      case 3:
        $formaTexto = 'CHEQUE CERTIFICADO';
        break;
      case 4:
        $formaTexto = 'CHEQUE DE GERENCIA';
        break;
      case 5:
        $formaTexto = 'CHEQUE DEL EXTERIOR';
        break;
      case 6:
        $formaTexto = 'DÉBITO DE CUENTA';
        break;
      case 7:
        $formaTexto = 'TRANSFERENCIA PROPIO BANCO';
        break;
      case 8:
        $formaTexto = 'TRANSFERENCIA OTRO BANCO NACIONAL';
        break;
      case 9:
        $formaTexto = 'TRANSFERENCIA BANCO EXTERIOR';
        break;
      case 10:
        $formaTexto = 'TARJETA DE CRÉDITO NACIONAL';
        break;
      case 11:
        $formaTexto = 'TARJETA DE CRÉDITO INTERNACIONAL';
        break;
      case 12:
        $formaTexto = 'GIRO';
        break;
      case 13:
        $formaTexto = 'DEPOSITO EN CUENTA (CORRIENTE/AHORROS)';
        break;
      case 14:
        $formaTexto = 'ENDOSO DE INVERSIÒN';
        break;
      case 15:
        $formaTexto = 'COMPENSACIÓN DE DEUDAS';
        break;
      case 16:
        $formaTexto = 'TARJETA DE DÉBITO';
        break;
      case 17:
        $formaTexto = 'DINERO ELECTRÓNICO';
        break;
      case 18:
        $formaTexto = 'TARJETA PREPAGO';
        break;
      case 19:
        $formaTexto = 'TARJETA DE CRÉDITO';
        break;
      case 20:
        $formaTexto = 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO';
        break;
      default:
        $formaTexto = 'ENDOSO DE TITULOS';
        break;
    }
    $xml = new stdClass();
    $date = date_create($docCab->fi_fechadoc);
    $dateDoc = date_format($date, "d-m-Y");
    $xml->cliente = array(
            "fechaEmision" => $dateDoc,
            "rucComprador" => $cliente->cerut,
            "nameComercial" => $cliente->cename_com,
            "name" => $cliente->cename,
            "direccion" => $cliente->ceaddress1,
            "telefono" => array(
                    "telefono1" => $cliente->cephone1,
                    "telefono2" => $cliente->cephone2,
            ),
            "emails" => array(
                    "email1" => $cliente->ceemail1,
                    "email2" => $cliente->ceemail2,
                    "email3" => $cliente->ceemail3,
            ),
            "comentario" => $docCab->fi_glosa
    );
    $xml->cabecera = array(
            "ruc" => $empresa->em_ruc,
            "fechaEmision" => $empresa->fi_fechadoc,
            "numDocumento" => $docCab->fi_docum,
            "direccionMatriz" => $empresa->em_dirmatriz,
            "claveAcceso" => $docCab->fi_claveacceso,
            "comentarioGen" => $docCab->fi_glosa,
            "comercial" => $empresa->em_comercial,
            "slogan" => $empresa->em_slogan,
            "resolucion" => $empresa->em_ceresolucion,
            "obligado" => $empresa->em_obligado,
            "razonSocial" => $empresa->em_razon,
            "descuentoGeneral" => self::formatNumber($docCab->fi_porDesc),
            "valorDescuentoGen" => self::formatNumber($docCab->fi_desct),
            "ivasi" => self::formatNumber($docCab->fi_ivasi),
            "ivano" => self::formatNumber($docCab->fi_ivano),
            "valorIva" => self::formatNumber($docCab->fi_iva),
            "totalPagar" => self::formatNumber($docCab->fi_totaldoc),
            "logo" => $empresa->logo,
            "fechaAuto" => $docCab->fi_fecauto,
            "ambiente" => ($empresa->em_ambiente == 1) ? "PRUEBA" : "PRODUCCION",
            "emision" => ($empresa->em_emision == 1) ? "NORMAL" : "",
    );
    $xml->pago = array(
            "total" => $pagos->detotal,
            "moneda" => $empresa->em_moneda,
            "dias" => $empresa->em_dias,
            "formaPago" => $formaTexto,
            "plazo" => $plazo,
    );
    $nameComents = self::loadNameComment();
    if (!empty($docDets)) {
      foreach ($docDets as $docDet) {

        if ($docDet->odsubdes != 0) {
          $totalDet = $docDet->odsubdes;
        } else {
          $totalDet = $docDet->odpvp;
        }
        $arrayProds[] = array(
                "cantidad" => self::formatNumber($docDet->odtcandig),
                "codigo" => ProductData::getById($docDet->itid)->itcodigo,
                "descripcion" => ProductData::getById($docDet->itid)->itname,
                "precio" => self::formatNumber($docDet->odpvp),
                "desDet" => self::formatNumber($totalDet),
                "desGen" => self::formatNumber($docDet->odvdsctog),
                "unidad" => self::formatNumber($docDet->unid_dig),
                "subtotal" => self::formatNumber($docDet->odtdscto), /** Descuentos*/
                "total" => self::formatNumber($docDet->odtotal),
                "comentario" => array(
                        $nameComents[0] => $docDet->odcomenta,
                        $nameComents[1] => $docDet->odcomenta1,
                        $nameComents[2] => $docDet->odcomenta2,
                ),
        );
      }
    }
    if (!empty($docDifs)) {

      foreach ($docDifs as $docDif) {
        $valDesc = 0.00;
        if ($docDif->odsubdes != 0) {
          $valDesc = $docDif->odsubdes;
        }
        $subT = $docDif->odpvp - ($docDif->odtdscto);
        $arrayProds[] = array(
                "cantidad" => self::formatNumber($docDif->odtcandig),
                "codigo" => ProductData::getById($docDif->itid)->itcodigo,
                "descripcion" => ProductData::getById($docDif->itid)->itname,
                "precio" => self::formatNumber($docDif->odpvp),
                "valconDes" => self::formatNumber($docDif->odsubdes * $docDif->odtcandig),
                "valorDes" => self::formatNumber(($docDif->odtcandig * $docDif->odpvp) - ($docDif->odsubdes * $docDif->odtcandig)),
                "desGen" => self::formatNumber($docDif->odvdsctog),
                "unidad" => $docDif->unid_dig,
                "iva" => self::formatNumber($docDif->iva),
                "subtotal" => self::formatNumber($subT),
                "descuento" => self::formatNumber($docDif->odtdscto),
                "total" => self::formatNumber($docDif->odtcandig * $subT),
                "comentario" => array(
                        $nameComents[0] => $docDif->odcomenta,
                        $nameComents[1] => $docDif->odcomenta1,
                        $nameComents[2] => $docDif->odcomenta2,
                ),
        );
      }
    }
    $microEmpresa = ($empresa->micro_emp) ? " CONTRIBUYENTE REGIMEN MICROEMPRESAS" : '';
    if (!empty($cliente->ceemail1) && !is_null($cliente->ceemail1)) {
      $emails[] = $cliente->ceemail1;
    }
    if (!empty($cliente->ceemail2) && !is_null($cliente->ceemail2)) {
      $emails[] = $cliente->ceemail2;
    }
    if (!empty($cliente->ceemail3) && !is_null($cliente->ceemail3)) {
      $emails[] = $cliente->ceemail3;
    }
    if (!empty($cliente->cephone1)) {
      $fono[] = $cliente->cephone1;
    }
    if (!empty($cliente->cephone2)) {
      $fono[] = $cliente->cephone2;
    }
    $telefonos = '';
    $correos = '';
    if (!empty($fono)) {
      $telefonos = implode(",", $fono);
    }
    if (!empty($emails)) {
      $correos = implode(",", $emails);
    }
    $xml->infAdicional = array(
            "direccion Comprador" => $cliente->ceaddress1,
            "contribuyente" => $microEmpresa,
            "correos" => $correos,
            "telefonos" => $telefonos,
    );
    $xml->productos = $arrayProds;
    return $xml;

  }

  public static function loadNameComment()
  {
    $ids = ConfigurationData::getByIds('63,64,65');
    foreach ($ids as $id) {
      $idd[] = $id->cgdatov;
    }
    return $idd;
  }

  public static function formatNumber($numero)
  {
    $decimales = number_format($numero, 2, ',', '');
    return $decimales;
  }
}