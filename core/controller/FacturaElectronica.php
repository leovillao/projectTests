<?php
class FacturaElectronica {

  static public function procesarFactura($documento)
  {
    $id = FilesData::getByDocum($documento);
    $cliente = PersonData::getById($id->person_id);
    $rowPagos = VwPagosData::getAllByIdFiles($id->fi_id);/*Se obtiene la forma de pago de esta factura */
    $rowFactura = FactFEData::getById($id->fi_id);/* Obntego la Cabecera de la factura */
    $rowDetFactura = FactFEdetData::getByIdFcNumber($documento);/* Detalle de la factura */
    $Objfactura = FabricaFact::CreaFactura($rowFactura, $rowDetFactura, $rowPagos);/* Se crea el XML de la Factura */
    $exito = FabricaFact::GetXML($Objfactura);
    $sresponse = EnviaFile::procesar($documento);
    $resXml = iconv('UTF-8', 'UTF-8//IGNORE', $sresponse);
    $arrayXml = simplexml_load_string($resXml, 'SimpleXMLElement', LIBXML_NOCDATA);

    if ($arrayXml->estado == 'AUTORIZADO') {
      $idFiles = $id->fi_id;
      $mensajeAuto = "1-Documento # " . $documento . " Autorizado con exito .";

      /*========================================================
      Se graba el archivo en la tabla RSM_FILE
      ==========================================================*/

      $insertDoc = new RsmData();
      $insertDoc->id_user = $_SESSION['user_id'];
      $insertDoc->fl_file = addslashes($sresponse);
      $insertDoc->tipo_doc = '01';
      $insertDoc->em_ruc_id = UserData::getById($_SESSION['user_id'])->em_ruc;
      $r = $insertDoc->add_t();

      $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc); // recupero los datos de la empresa para la configruacion de rutas

      if ($r[0] == true) {
        unlink('xml/sinfirma/Fact' . $documento . '.xml');
        /*========================================================
        Se actualiza el campo con el ID del archivo en la tabla de_files ,
        ==========================================================*/
        $updateFileId = new FilesData();
        $updateFileId->fi_id = $idFiles;
        $updateFileId->fi_idfile = $r[1];
        $updateFileId->updateFile_t();
        $emails = '';
        $clientMails = PersonData::getByIdForRut($cliente->rut);
        if ($clientMails->email1 != '') {
          $emails .= $clientMails->email1 . ',';
        }
        if ($clientMails->email2 != '') {
          $emails .= $clientMails->email2 . ',';
        }
        if ($clientMails->email3 != '') {
          $emails .= $clientMails->email3 . ',';
        }
        $msjSend = self::generaPdf($r[1], $documento, $cliente->rut, $emails);

        if (substr($msjSend, 0, 1) == 1) {
          $upd_Send = new FilesData();
          $upd_Send->fi_stasend = 1;
          $upd_Send->fi_id = $idFiles;
          $upd_Send->updateStateSend();
          unlink('xml/pdf/Fact' . $documento . '.pdf');
          unlink('xml/autorizados/Fact' . $documento . '.xml');
          $envioMail = "1-Documento # " . $documento . ", enviado por correo con exito ";
        } else {
          $envioMail = "0-Documento no enviado por correo";
          unlink('xml/pdf/Fact' . $documento . '.pdf');
          unlink('xml/autorizados/Fact' . $documento . '.xml');
        }
      }
      /*==================== Se actualiza el estado de Actualizacion ========================*/
      $updateState = new FilesData();
      $updateState->fi_estauto = 1;
      $updateState->fi_id = $idFiles;
      $updateState->fi_claveacceso = $arrayXml->numeroAutorizacion;
      $fecauto = substr($arrayXml->fechaAutorizacion, 6, 4);
      $fecauto .= "-";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 3, 2);
      $fecauto .= "-";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 0, 2);
      $fecauto .= " ";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 10, 8);
      $updateState->fi_fecauto = $fecauto;
      $updateState->updateStateAuto_t();

      /*========================================================
                      Se cierra la Transaccion
      ==========================================================*/
    }

  } // fin function para enviar factura electronica

  static public function generaPdf($idFiles,$factura,$cliente,$correos)
  {
    $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $xml = RsmData::getById($idFiles);
    $mails = PersonData::getByIdForRut($cliente);
    $factura = $factura;
    $xmlFile = iconv('UTF-8', 'UTF-8//IGNORE', RsmData::getById($idFiles)->fl_file);
    $Xmls = simplexml_load_string($xmlFile, 'SimpleXmlElement', LIBXML_NOCDATA);
    $savePdf = $Xmls->numeroAutorizacion;
    $pdf = new FPDF();
    $pdfile = $pdf->AddPage();
    $pdfile = $pdf->SetFont('Arial', 'B', 16);
    $claveacceso = $Xmls->numeroAutorizacion;
    $fechauto = $Xmls->fechaAutorizacion;
    $logo = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc)->logo;
    if (!empty($pathXml->logo)) {
      $logo = $pathXml->logo;
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
        $pdf->Image($logo, 5, 5, 75);
        $pdf->Ln();
        $pdfile = $pdf->SetFont('Arial', '', 16);
        $pdfile = $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
        $pdfile = $pdf->SetTextColor(255, 0, 0);
        $pdfile = $pdf->Cell(98, 7, 'FACTURA ', 0, 1, 'C', 0, 1);
        $pdfile = $pdf->SetTextColor(0, 0, 0);
        $pdfile = $pdf->SetFont('Arial', '', 19);
        $pdfile = $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
        $pdfile = $pdf->Cell(98, 7, $valores->estab . '-' . $valores->ptoEmi . '-' . $valores->secuencial, 0, 1, 'C', 0, 1);
        $pdfile = $pdf->SetFont('Arial', '', 8);
        $pdfile = $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
        $pdfile = $pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
        $pdfile = $pdf->SetFont('Arial', '', 13);
        $pdfile = $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
        $pdfile = $pdf->SetFont('Arial', '', 8);
        $pdfile = $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
        $pdfile = $pdf->Cell(100, 5, $valores->razonSocial, 0, 0, 'C', 0, 1);
        $empresa = $valores->razonSocial;
        $pdfile = $pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
        $pdfile = $pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
        $pdfile = $pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
        $pdfile = $pdf->Cell(100, 5, $valores->dirMatriz, 0, 0, 'L', 0, 1);
        $pdfile = $pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
          if ($xmls->infoFactura->contribuyenteEspecial) {
              $pdfile = $pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $xmls->infoFactura->contribuyenteEspecial, 0, 0, 'L', 0, 1);
          }
        $pdfile = $pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
        $pdfile = $pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $xmls->infoFactura->obligadoContabilidad, 0, 0, 'L', 0, 1);
        $pdfile = $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
        $nombre = $valores->estab . $valores->ptoEmi . $valores->secuencial;
        $pdfile = $pdf->Ln(12);
      }
      $pdfile = $pdf->SetFont('Arial', '', 8);
      $pdfile = $pdf->Cell(100, 5, 'RAZON SOCIAL : ' . $xmls->infoFactura->razonSocialComprador, 0, 0, 'L', 0, 1);
      $pdfile = $pdf->Cell(98, 5, 'IDENTIFICACION COMPRADOR : ' . $xmls->infoFactura->identificacionComprador, 0, 1, 'L', 0, 1);
      $pdfile = $pdf->Cell(98, 5, 'DIRECCION COMPRADOR : ' . $xmls->infoFactura->direccionComprador, 0, 1, 'L', 0, 1);
      $pdfile = $pdf->Cell(98, 5, 'FECHA DE EMISION : ' . $xmls->infoFactura->fechaEmision, 0, 1, 'L', 0, 1);
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
      $pdfile = $pdf->Ln();
      //$pdf->Line(10, 108, 200, 108);
      //$pdf->Ln();
      $pdfile = $pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
      $pdfile = $pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
      $totDet = COUNT($xmls->detalles->detalle);
      $td = 0;
      $totDesc = 0;
      for ($td = 0; $td < $totDet; ++$td) {
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->codigoPrincipal, 1, 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode($xmls->detalles->detalle[$td]->descripcion), 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->cantidad, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioUnitario, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->descuento, 1, 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioTotalSinImpuesto, 1, 1, 'C', 0, 1);
        $totDesc = $totDesc + $xmls->detalles->detalle[$td]->descuento;
      }
      $pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(40, 5, 'Subtotal ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $xmls->infoFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
      $pdf->SetFont('Arial', '', 10);
      $pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
      $pdf->SetFont('Arial', '', 9);
      $pdf->Cell(30, 5, ' ', 0, 0, 0, 0, 1);
      $pdf->Cell(40, 5, 'Descuento ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $totDesc, 1, 1, 'C', 0, 1);
      foreach ($xmls->infoFactura->pagos->pago as $pago) {
        switch ($pago->formaPago) {
          case 1:
            $pdf->Cell(100, 5, 'SIN UTILIZACION DEL SISTEMA FINANCIERO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 2:
            $pdf->Cell(100, 5, 'CHEQUE PROPIO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 3:
            $pdf->Cell(100, 5, 'CHEQUE CERTIFICADO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 4:
            $pdf->Cell(100, 5, 'CHEQUE DE GERENCIA', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 5:
            $pdf->Cell(100, 5, 'CHEQUE DEL EXTERIOR', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 6:
            $pdf->Cell(100, 5, 'DÉBITO DE CUENTA', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 7:
            $pdf->Cell(100, 5, 'TRANSFERENCIA PROPIO BANCO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 8:
            $pdf->Cell(100, 5, 'TRANSFERENCIA OTRO BANCO NACIONAL', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 9:
            $pdf->Cell(100, 5, 'TRANSFERENCIA BANCO EXTERIOR', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 10:
            $pdf->Cell(100, 5, 'TARJETA DE CRÉDITO NACIONAL', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 11:
            $pdf->Cell(100, 5, 'TARJETA DE CRÉDITO INTERNACIONAL', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 12:
            $pdf->Cell(100, 5, 'GIRO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 13:
            $pdf->Cell(100, 5, 'DEPOSITO EN CUENTA (CORRIENTE/AHORROS)', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 14:
            $pdf->Cell(100, 5, 'ENDOSO DE INVERSIÒN', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 15:
            $pdf->Cell(100, 5, 'COMPENSACIÓN DE DEUDAS', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 16:
            $pdf->Cell(100, 5, 'TARJETA DE DÉBITO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 17:
            $pdf->Cell(100, 5, 'DINERO ELECTRÓNICO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 18:
            $pdf->Cell(100, 5, 'TARJETA PREPAGO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 19:
            $pdf->Cell(100, 5, 'TARJETA DE CRÉDITO', 'T,R,L', 0, 'L', 0, 1);
            break;
          case 20:
            $pdf->Cell(100, 5, 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO', 'R,L', 0, 'L', 0, 1);
            break;
          default:
            $pdf->Cell(100, 5, 'ENDOSO DE TITULOS', 'T,R,L', 0, 'L', 0, 1);
            break;
        }
      }
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'base 12% ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $xmls->infoFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, 'Moneda : ' . $xmls->infoFactura->moneda, 'R,L', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'base 0% ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $totDesc, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, 'Total ' . $pago->total . ' Plazo ' . $pago->plazo . ' ' . $pago->unidadTiempo, 'R,B,L', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'SubTotalSin Impuestos ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $xmls->infoFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Iva 12% ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $xmls->infoFactura->totalConImpuestos->totalImpuesto[0]->valor, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, '', 0, 0, 'L');
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
      $pdf->Cell(20, 5, $pago->total, 1, 1, 'C', 0, 1);
      $pdf->Cell(100, 5, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
      $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
      $pdf->Cell(100, 5, utf8_decode('INFORMACIÓN ADICIONAL'), 'T,L,R', 1, 'C', 0, 1);
      $totCampos = count($xmls->infoAdicional->campoAdicional);
      $campoAdi = $xmls->infoAdicional->campoAdicional;
      for ($j = 0; $j < $totCampos; $j++) {
        if ($campoAdi[$j]['nombre'] != 'IdTransac') {
          $pdf->Cell(100, 5, $campoAdi[$j]['nombre'] . ' :' . ' ' . $campoAdi[$j], 'L,R', 1, 'L', 0, 1);
        }
      }
//        $pdf->Cell(100, 5, 'Total : $' . $pago->total, 'L,R', 1, 'L', 0, 1);
      $pdf->Cell(100, 5, '', 'B,L,R', 1, 'R', 0, 1);
    }
    $archivo = $pdf->Output('xml/pdf/Fact' . $nombre . '.pdf', 'S');
    file_put_contents('xml/pdf/Fact' . $nombre . '.pdf', $archivo);
    file_put_contents('xml/autorizados/Fact' . $nombre . '.xml', $xmlFile);
    /*==============================================================================*/
    $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $idFile = FilesData::getById($idFiles);
    $host = ConfigurationData::getByPreffix('host');
    $puerto = ConfigurationData::getByPreffix('puerto');
    $autnt = ConfigurationData::getByPreffix('authentication');
    /*====================================================================================
    Se valida la configuracion de los correos para el envio de los documentos electronicos
    =====================================================================================*/
    $setCorreos = ConfigurationData::getByPreffix('setcorreos');
    if ($setCorreos->val == 0) {
      // En cero se realiza el envio de los correos desde el correo principal
      $username = ConfigurationData::getByPreffix('username');
      $pass = ConfigurationData::getByPreffix('pass');
    } else {
      // Se realiza el envio desde el correo con su usuario y contraseña correspondiente
      $username = UserData::getById($_SESSION['user_id'])->email;
      $pass = UserData::getById($_SESSION['user_id'])->passemail;
    }
    $encryption = ConfigurationData::getByPreffix('encryption');
    $title = ConfigurationData::getByPreffix('title');
    $mail = new PHPMailer();
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                  // Enable verbose debug output
    $mail->SMTPDebug = 0;                                   //Alternative to above constant
    $mail->isSMTP();                                        // Send using SMTP
    $mail->Host = $host->val;                         // Set the SMTP server to send through
    if ($autnt->val == 1) {
      $autentication = true;
    } else {
      $autentication = false;
    }
    $mail->SMTPAuth = $autentication;                                 // Enable SMTP authentication
    $mail->Username = $username->val;                  // SMTP username
    $mail->Password = $pass->val;                           // SMTP password
    if ($encryption->val == 1) {
      $tls = 'tls';
    } else {
      $tls = 'ssl';
    }
    $mail->SMTPSecure = $tls;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port = $puerto->val;                                      // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    $mail->CharSet = 'UTF-8';
    //Recipients
    $mail->setFrom($username->val, $title->val);
    $numChar = substr_count($correos, ','); //Cuento la coma que es el caracter especial para poder crear los array correspondientes de las direcciones
    if ($numChar <= 1) { // Si este es igual a uno es por que es solo un correo y no necesito crear un array
      $mail->addAddress(trim($correos, ','));
    } else { // la otra opcion es que sea mas de uno asi podra crear el array y poder enviar el duplicado a varios correos
      $email = trim($correos, ',');
      $emails = explode(',', $email);
      for ($i = 0; $i <= count($emails); $i++) {
        $mail->addAddress($emails[$i]);
      }
    }
    /*$mail->addCC('cc@example.com'); =========== COPIAS DE LOS CORREOS ENVIADOS =========*/
    // Attachments
    $mail->AddStringAttachment($archivo, 'xml/pdf/Fact' . $nombre . '.pdf');          // Add attachments
    $mail->AddStringAttachment($xmlFile, 'xml/autorizados/Fact' . $nombre . '.xml');          // Add attachments
    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $empresa . ' , Factura # ' . $nombre;
    $mail->Body = '
    <p>Estimado cliente</p>
    <p>' . $empresa . ', le informa que el documento electrónico ' . $nombre . ' adjunto ha sido generado con éxito</p>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    $msj = '';
    if ($mail->send()) {
      $msj = '1-Mensaje enviado con exito';
    } else {
      $msj = "0-Mensaje no pudo ser enviado. Mailer Error: {$mail->ErrorInfo}";
    }
    return $msj;
    /*==============================================================================*/


  }

}
