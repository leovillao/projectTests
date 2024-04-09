<?php

class EnviaNcrFile {
  /** ENVIO DEL DOCUMENTO ELECTRONICO*/
  /** DEFINO LOS PARAMETROS DEL ENVIO*/
  public static function procesar($nameDocum) {
    $ch = curl_init("http://pyme.e-piramide.net:8080/FE/Procesar?wsdl");
    $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $fxml = 'xml/sinfirma/Fact' . $nameDocum . '.xml';
    /** ARCHIVO CONSTRUIDO*/
    $xml = self::makeCurlFile($fxml);
    /** ===============================
     * La ruta de la firma esta en 'C:¥home¥NOMBRE_COMPAﾃ選A¥' del SERVIDOR WEB , no en las carpetas de archivos de la pagina web
     * ==================================*/
    $ambiente = $pathXml->em_ambiente;  // Ambiente de trabajo
    $filep12 = $pathXml->firma;  // ruta del file en sel servidor de FE
    $pswp12 = $pathXml->em_clave_p_12;  // Clave de la firma.
    /**PREPARO EL ARREGLO DE PARAMETROS*/
    $data = array('file' => $xml, 'ambiente' => $ambiente, 'fp12' => $filep12, 'pp12' => $pswp12);
    /**CONFIGURO LA CONEXION*/
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
      $result = curl_error($ch);
    }
    curl_close($ch);
    return $result;
  }

  public static function procesarDoc($nameDocum,$idFiles) {
    $ch = curl_init("http://pyme.e-piramide.net:8080/FE/Procesar?wsdl");
    $pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
    $fxml = 'xml/sinfirma/NCR' . $nameDocum . '.xml';
    /** ARCHIVO CONSTRUIDO */
    $xml = self::makeCurlFile($fxml);
    $resultXml = '';
    /** ===============================
     * La ruta de la firma esta en 'C:¥home¥NOMBRE_COMPAﾃ選A¥' del SERVIDOR WEB , no en las carpetas de archivos de la pagina web
     * ==================================*/
    $ambiente = $pathXml->em_ambiente;  // Ambiente de trabajo
    $filep12 = $pathXml->firma;  // ruta del file en sel servidor de FE
    $pswp12 = $pathXml->em_clave_p_12;  // Clave de la firma.
    /**PREPARO EL ARREGLO DE PARAMETROS*/
    $data = array('file' => $xml, 'ambiente' => $ambiente, 'fp12' => $filep12, 'pp12' => $pswp12);
    /**CONFIGURO LA CONEXION*/
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $resultFileXml = curl_exec($ch);
    if (curl_errno($ch)) {
      $resultFileXml = curl_error($ch);
    }
    curl_close($ch);
    $resXml = iconv('UTF-8', 'UTF-8//IGNORE', $resultFileXml);
    $arrayXml = simplexml_load_string($resXml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $msjauto = "";
    if ($arrayXml->estado == 'AUTORIZADO') {

      $fecauto = substr($arrayXml->fechaAutorizacion, 6, 4);
      $fecauto .= "-";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 3, 2);
      $fecauto .= "-";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 0, 2);
      $fecauto .= " ";
      $fecauto .= substr($arrayXml->fechaAutorizacion, 10, 8);
      $claveautorizacion = $arrayXml->numeroAutorizacion;
      /** ======= FUNCION PARA REALIZAR PROCESOS REALICIONADOS CON LA AUTORIZACION DEL LOS DOCUMENTOS ELECTRONICOS
       * ===
       */

      $msjSuccess = "";
      $msjError = "";
//// WR
      $updateState = new FilesData();
      if ($updateState->AbrirTransaccion() == false) {
        $error = "0-Error al iniciar la transaccion del estado del documento electrónico";
      } else {
        $conx2 = $updateState::$Conx; // SE ACTUALZA EL ESTADO DE AUTORIZACION
        $idfc = FilesData::getByDocum($nameDocum,'04')->fi_id;
        $updateState->fi_estauto = 1;
        $updateState->fi_id = $idfc;
        $updateState->fi_fecauto = $fecauto;
        $upSt = $updateState->updateStAuto_t($conx2);


        /** EL PROCESO DE GRABACION DEL ARCHIVO EN LA TABLA RSM , Y TAMBIEN EL PROCESO QUE ACTUALIZA EL ID DE LA TABLA ID_FILE,  ES ACTUAILZADO CON EL PROCESO DE CARGAR EL XML AL WEBSERVICE - NUEVO SERVIDOR DONDE SE ALMACENARAN LOS XML GENERADOS Y TAMBIEN SE LLAMARAN LOS ARCHIVOS DE LOS XML PARA SU VISUALIZACION EN PDF O IMPRESION */

        /** MEDIANTE CURL SE ENVIA EL ARCHIVO PARA SU GRABACIÓN */
        $ch = curl_init("http://pyme.e-piramide.net/downxml/saveXml.php");
        $fields = array('ruc' => $_SESSION['ruc'],'razon'=>EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre,'clave' => $claveautorizacion, 'archivo' => $resXml);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
          $result = curl_error($ch);
        }
        curl_close($ch);
//        $insertDoc = new RsmData();
//        $insertDoc->id_user = $_SESSION['user_id'];
//        $insertDoc->fl_file = addslashes($result);
//        $insertDoc->tipo_doc = '04';
//        $insertDoc->em_ruc_id = UserData::getById($_SESSION['user_id'])->em_ruc;
//        $r = $insertDoc->add_t($conx2);
//        if ($r[0] == true) {
//          /** SE VALIDA SI GUARDO EL ARCHIVO XML EN LA TABLA RSM*/
//          unlink('xml/sinfirma/NCR' . $nameDocum . '.xml');
//          $updateFileId = new FilesData();
//          $updateFileId->fi_id = $idFiles;
//          $updateFileId->fi_idfile = $r[1];
//          $upRsm = $updateFileId->updateF($conx2);
//          if ($upRsm[0] == false) {
//            /** SE VALIDA SI SE ACTUALIZO LA TABLA DE FILES CON EL ID DEL XML GRABADO EN FILES_RSM*/
//            $error = true;
//            $msjError = "0-Error al actualizar el ID referencial de rsm con de_files";
//          }
//        } else {
//          $error = false;
//          $msjError = "0-Fallo en grabar archivo XML, ";
//        }

        if ($upSt[0] == false) {
          /** SE VALIDA SI SE ACTUALIZO LA FECHA Y CLAVE DE AUTORIZACION*/
          $error = true;
          $updateState->CancelarTransaccion($conx2);
        } else {
          $resultEnvio = self::loadFileRsm($claveautorizacion, $nameDocum, $idFiles);
          $updateState->CerrarTransaccion($conx2);
          $resultAuto = "1-Documento #" . $nameDocum . " autorizado con exito";
        }
      }
    } else {

      $xmlsClave = simplexml_load_file($fxml);
      $updateClave = new FilesData();
      $updateClave->fi_claveacceso = '"' . $xmlsClave->infoTributaria->claveAcceso . '"';
      $updateClave->fi_estauto = 0;
      $updateClave->fi_fecauto = '"' . null . '"';
      $updateClave->fi_id = $idFiles;
      $updateClave->updateStateAuto();

      $updateState = new FilesData();
      $updateState->fi_logauto =  $arrayXml->mensaje;
      $updateState->fi_id = $idFiles;
      $updateState->updateLogAuto();
      $resultAuto = "0-" . $arrayXml->mensaje;
    }

    $array = array();
    array_push($array,array( "msjAuto" => $resultAuto));
    array_push($array, array("msjMail" => $resultEnvio));
    return $array;

  }

  public static function makeCurlFile($file) {
    $mime = mime_content_type($file);
    $info = pathinfo($file);
    $name = $info['basename'];
    $output = new CURLFile($file, $mime, $name);
    return $output;
  }

  public static function loadFileRsm($idRsmFiles, $numFact,$idFiles) {
    /**====== CON ESTA FUNCION SE GRABA EL ARCHIVO XML EN LA TABLA RSMFILE SI EL DOCUMENTO FUE AUTORIZADO =======*/
    $load = '';
    $error = false;
    $msjError = '';
    $msCorreo = '';
    $msjSuccess = "";

//    if ($r[0] == true) {
//      /** SE VALIDA SI GUARDO EL ARCHIVO XML EN LA TABLA RSM*/
//      unlink('xml/sinfirma/NCR' . $numFact . '.xml');
//      $msjSuccess .= "Archivo XML guardado con exito, ";
//      $updateFileId = new FilesData();
//      $updateFileId->fi_id = FilesData::getByFiDocum($numFact)->fi_id;
//      $updateFileId->fi_idfile = $r[1];
//      $upRsm = $updateFileId->updateFile_t($conn);
//      if ($upRsm[0] == false) {
//        /** SE VALIDA SI SE ACTUALIZO LA TABLA DE FILES CON EL ID DEL XML GRABADO EN FILES_RSM*/
//        $error = true;
//        $msjError .= "0-Error al actualizar el ID referencial de rsm con de_files";
//      } else {
//        $msjSuccess .= "0-Campo fl_file actualizado con exito, ";
//      }
//    } else {
//      $error = false;
//      $msjError .= "0-Fallo en grabar archivo XML, ";
//    }
    /** ======== FUNCION PARA EL PROCESO DE ENVIO DEL DOCUMENTO POR CORREO ELECTRONICO ========= */
    $msjCorreos = PdfData::GeneraPdfNcr($idRsmFiles, $numFact, FilesData::getByIdOne($idFiles)->ceid,$idFiles);
    if (substr($msjCorreos, 0, 1) == 1) {
      // SE VALIDA SI EL ENVIO DE CORREO FUE CORRECTO
      $upd_Send = new FilesData();
      $upd_Send->fi_stasend = 1;
      $upd_Send->fi_id = FilesData::getByFiDocum($numFact)->fi_id;
      $upSend = $upd_Send->updateStateSendFiles();
      if ($upSend[0] == false) {
        $error = true;
        $msjError .= "0-Documento no fue enviado por correo";
      } else {
        $msjSuccess .= "0-Estado de envio actualizado con exito, ";
      }
      unlink('xml/pdf/NCR' . $numFact . '.pdf');
      unlink('xml/autorizados/NCR' . $numFact . '.xml');
    } else {
      $error = true;
      $msjError .= "0-Documento no se envio por correo";
      unlink('xml/pdf/NCR' . $numFact . '.pdf');
      unlink('xml/autorizados/NCR' . $numFact . '.xml');
    }

    $arrau = array();
    if ($error == false) {
      $msjXml = "1-Grabado con exito";
      $mjsMail = "dd";
      array_push($arrau, $msjXml);
      array_push($arrau, $mjsMail);
      array_push($arrau, $msjSuccess);
      array_push($arrau, $msjError);
    } else {
      $msjXml = $msjError;
      $mjsMail = "dd";
      array_push($arrau, $msjXml);
      array_push($arrau, $mjsMail);
    }
    return $r = $arrau;
  }

}
?>