<?php

class EnviaFactFile
{
    /** ENVIO DEL DOCUMENTO ELECTRONICO*/
    /** DEFINO LOS PARAMETROS DEL ENVIO*/
    public static function procesar($nameDocum)
    {
        $pathXml = EmpresasData::getEmpresaData();
        $ch = curl_init($pathXml->em_wsdl);
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

    public static function procesarDoc($nameDocum, $idFiles, $reporte)
    {
        $pathXml = EmpresasData::getEmpresaData();
        $ch = curl_init($pathXml->em_wsdl);
        // graba el xml sin firma en la carpeta de documentos sin deacuerdo al cliente para el respectivo proceso de envio del XML para la autorizacion
        $fxml = "xml/" . $_SESSION['ruc'] . "/sinfirma/Fact" . $nameDocum . ".xml";
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
        $msjauto = "";
        if (strlen($resultFileXml) > 100) {
            $arrayXml = simplexml_load_string($resXml, 'SimpleXMLElement', LIBXML_NOCDATA);
            /**  */
            if ($arrayXml->estado == 'AUTORIZADO') {
                $fecauto = substr($arrayXml->fechaAutorizacion, 6, 4);
                $fecauto .= "-";
                $fecauto .= substr($arrayXml->fechaAutorizacion, 3, 2);
                $fecauto .= "-";
                $fecauto .= substr($arrayXml->fechaAutorizacion, 0, 2);
                $fecauto .= " ";
                $fecauto .= substr($arrayXml->fechaAutorizacion, 10, 8);
                $claveautorizacion = $arrayXml->numeroAutorizacion;
                /** ======= FUNCION PARA REALIZAR PROCESOS REALICIONADOS CON LA AUTORIZACION DEL LOS DOCUMENTOS ELECTRONICOS  */
                $msjSuccess = "";
                $msjError = "";
                $errors = "";
                $updateState = new FilesData();
                $proccError = false;
                if ($updateState->AbrirTransaccion() == false) {
                    $error = "0-Error al iniciar la transaccion del estado del documento electrónico";
                } else {
                    $conx2 = $updateState::$Conx;
                    $updateState->fi_estauto = 1;
                    $updateState->fi_id = $idFiles;
                    $updateState->fi_fecauto = '"' . $fecauto . '"';
                    $upSt = $updateState->updateStateAuto_t($conx2);
                    if ($upSt[0] == false) {
                        $proccError = true;
                    }
                    /** EL PROCESO DE GRABACION DEL ARCHIVO EN LA TABLA RSM , Y TAMBIEN EL PROCESO QUE ACTUALIZA EL ID DE LA TABLA ID_FILE,  ES ACTUAILZADO CON EL PROCESO DE CARGAR EL XML AL WEBSERVICE - NUEVO SERVIDOR DONDE SE ALMACENARAN LOS XML GENERADOS Y TAMBIEN SE LLAMARAN LOS ARCHIVOS DE LOS XML PARA SU VISUALIZACION EN PDF O IMPRESION */
                    /** MEDIANTE CURL SE ENVIA EL ARCHIVO PARA SU GRABACIÓN */
                    $responTxt = "1-Procesado";
                    if ($proccError == true) {
                        /** SE VALIDA SI SE ACTUALIZO LA FECHA Y CLAVE DE AUTORIZACION*/
                        $updateState->CancelarTransaccion($conx2);
                        $resultAuto = "0-Error al actualizar documento autorizado";
                        $resultEnvio = "";
                    } else {
                        unlink("xml/" . $pathXml->em_ruc . "/sinfirma/Fact" . $nameDocum . ".xml");
                        $updateState->CerrarTransaccion($conx2);
                        $resultEnvio = self::loadFileRsm($claveautorizacion, $nameDocum, $idFiles, $resultFileXml, $reporte);
                        $resultAuto = "1-Documento #" . $nameDocum . " autorizado con exito";
                    }
                }
            } else {
                $xmlsClave = simplexml_load_file($fxml);
                $updateState = new FilesData();
                $updateState->fi_logauto = $arrayXml->mensaje;
                $updateState->fi_id = $idFiles;
                $updateState->updateLogAuto();
                $resultAuto = "0-" . $arrayXml->mensaje . "";
                $resultEnvio = "0-No enviado";
            }
            /**  */
            $ch = curl_init($pathXml->em_wsdlSave);
            $fields =
                array(
                    'ruc' => $_SESSION['ruc'],
                    'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre,
                    'clave' => $claveautorizacion,
                    'archivo' => $resXml
                );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                $result = curl_error($ch);
            }
            curl_close($ch);

        } else {

            $cTxt = curl_init("http://pyme.e-piramide.net/downxml/crearLog.php");
            $campos =
                array(
                    'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre,
                    'clave' => $nameDocum
                );
            curl_setopt($cTxt, CURLOPT_POST, 1);
            curl_setopt($cTxt, CURLOPT_POSTFIELDS, $campos);
            curl_setopt($cTxt, CURLOPT_RETURNTRANSFER, 1);
            $responTxt = curl_exec($cTxt);
            if (curl_errno($cTxt)) {
                $responTxt = curl_error($cTxt);
            }
            curl_close($cTxt);
            $resultAuto = "0-Fallo conexion con el servidor";
            $resultEnvio = "";
        }
        $estab = substr($nameDocum, 0, 3);
        $emision = substr($nameDocum, 3, 3);
        $idEmision = SecuenciaData::getByEmiEstab($emision, $estab);
        $array = array();
        array_push($array, array("msjAuto" => $resultAuto));
        array_push($array, array("msjMail" => $resultEnvio));
        array_push($array, array("id" => $idFiles));
        array_push($array, array("reporte" => FData::getNameReporte($idEmision->id)));
        return $array;
    }

    public static function procesarDocN($nameDocum, $idFiles)
    {
        $pathXml = EmpresasData::getEmpresaData();
        $ch = curl_init($pathXml->em_wsdl);
        $fxml = 'xml/' . $_SESSION['ruc'] . '/sinfirma/Fact' . $nameDocum . '.xml';
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
        $claveAccesoString = '';
        $totalSinImpuestosString = 0;
        $tipoIdentificacionCompradorString = '';
        $identificacionCompradorString = '';
        $importeTotalString = 0;
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
             */
            $msjSuccess = "";
            $msjError = "";
            //// WR
            $errors = "";
            /**  */
            $updateState = new FilesData();
            $proccError = false;
            if ($updateState->AbrirTransaccion() == false) {
                $error = "0-Error al iniciar la transaccion del estado del documento electrónico";
            } else {
                $conx2 = $updateState::$Conx;
                $updateState->fi_estauto = 1;
                $updateState->fi_id = $idFiles;
                $updateState->fi_claveacceso = '"' . $claveautorizacion . '"';
                $updateState->fi_fecauto = '"' . $fecauto . '"';
                $updateState->fi_logauto = '"' . null . '"';
                $upSt = $updateState->updateStateAuto_t($conx2);
                if ($upSt[0] == false) {
                    $proccError = true;
                }
                /** EL PROCESO DE GRABACION DEL ARCHIVO EN LA TABLA RSM , Y TAMBIEN EL PROCESO QUE ACTUALIZA EL ID DE LA TABLA ID_FILE,  ES ACTUAILZADO CON EL PROCESO DE CARGAR EL XML AL WEBSERVICE - NUEVO SERVIDOR DONDE SE ALMACENARAN LOS XML GENERADOS Y TAMBIEN SE LLAMARAN LOS ARCHIVOS DE LOS XML PARA SU VISUALIZACION EN PDF O IMPRESION */
                /** MEDIANTE CURL SE ENVIA EL ARCHIVO PARA SU GRABACIÓN */
                $ch = curl_init($pathXml->em_wsdlSave);
                $fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $claveautorizacion, 'archivo' => $resultFileXml);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    $result = curl_error($ch);
                }
                curl_close($ch);
                if ($proccError == true) {
                    /** SE VALIDA SI SE ACTUALIZO LA FECHA Y CLAVE DE AUTORIZACION*/
                    $updateState->CancelarTransaccion($conx2);
                    $resultAuto = "0-Error al actualizar documento autorizado";
                    $resultEnvio = "";
                } else {
                    unlink('xml/' . $_SESSION['ruc'] . '/sinfirma/Fact' . $nameDocum . '.xml');
                    $updateState->CerrarTransaccion($conx2);
                    $resultEnvio = self::loadFileRsm($claveautorizacion, $nameDocum, $idFiles, $xml);
                    $resultAuto = "1-Documento #" . $nameDocum . " autorizado con exito";
                }
            }

        } elseif ($arrayXml->mensaje == "CLAVE ACCESO REGISTRADA") {

            /** DE ACUERDO A EXTRUCTURA XML SE OBTIENE LA CLAVE DE ACCESO */
            $xmlsClave = simplexml_load_file($fxml);
            $claveAccesoString = $xmlsClave->infoTributaria->claveAcceso;
            /** clave de acceso*/
            $totalSinImpuestosString = $xmlsClave->infoFactura->totalSinImpuestos;
            /** clave de acceso*/
            $tipoIdentificacionCompradorString = $xmlsClave->infoFactura->tipoIdentificacionComprador;
            /** clave de acceso*/
            $identificacionCompradorString = $xmlsClave->infoFactura->identificacionComprador;
            /** clave de acceso*/
            $importeTotalString = $xmlsClave->infoFactura->importeTotal;
            /** clave de acceso*/
            /** =====>>>> CLAVE DE ACCESO => dato a enviar a WEBSERVICE  */
            /** RESPUESTA DEL WEBSERVICES ARCHIVO XML*/
            $resAuto = file_get_contents($pathXml->em_wsdlDescargar . $claveAccesoString);
            $Xmls = simplexml_load_string($resAuto, 'SimpleXmlElement', LIBXML_NOCDATA);
            $arrayResp = array();
            $fechaAutorizacionFile = $Xmls->fechaAutorizacion;
            foreach ($Xmls->comprobante as $comprobante) {
                $claveAccesoFile = '';
                $totalSinImpuestosFile = 0;
                $tipoIdentificacionCompradorFile = '';
                $identificacionCompradorFile = '';
                $importeTotalFile = 0;
                $xml = simplexml_load_string($comprobante, 'SimpleXmlElement', LIBXML_NOCDATA);
                foreach ($xml->infoTributaria as $valor) {
                    $claveAccesoFile = $valor->claveAcceso;
                }
                $totalSinImpuestosFile = $xml->infoFactura->totalSinImpuestos;
                $tipoIdentificacionCompradorFile = $xml->infoFactura->tipoIdentificacionComprador;
                $identificacionCompradorFile = $xml->infoFactura->identificacionComprador;
                $importeTotalFile = $xml->infoFactura->importeTotal;
            }
            $estado = 0;
            if (strcmp(trim($claveAccesoString), trim($claveAccesoFile)) === 0) {
                if (strcmp(trim($totalSinImpuestosString), trim($totalSinImpuestosFile)) === 0) {
                    if (strcmp(trim($tipoIdentificacionCompradorString), trim($tipoIdentificacionCompradorFile)) === 0) {
                        if (strcmp(trim($identificacionCompradorString), trim($identificacionCompradorFile)) === 0) {
                            if (strcmp(trim($importeTotalString), trim($importeTotalFile)) === 0) {
                                $estado = 1;
                            }
                        }
                    }
                }
            }
            if ($estado == 1) {
                $updateSt = new FilesData();
                $procsError = false;
                if ($updateSt->AbrirTransaccion() == false) {
                    $error = "0-Error al iniciar la transaccion del estado del documento electrónico";
                } else {
                    $date = new DateTime($fechaAutorizacionFile);
                    $newFecha = $date->format('Y-m-d H:i:s');
                    $conx2 = $updateSt::$Conx;
                    $updateSt->fi_estauto = 1;
                    $updateSt->fi_id = $idFiles;
                    $updateSt->fi_claveacceso = '"' . $claveAccesoFile . '"';
                    $updateSt->fi_fecauto = '"' . $newFecha . '"';
                    $updateSt->fi_logauto = "NULL";
                    $upS = $updateSt->updateStateAutoRes_t($conx2);
                    if ($upS[0] == false) {
                        $procsError = true;
                    }
                    /** EL PROCESO DE GRABACION DEL ARCHIVO EN LA TABLA RSM , Y TAMBIEN EL PROCESO QUE ACTUALIZA EL ID DE LA TABLA ID_FILE,  ES ACTUAILZADO CON EL PROCESO DE CARGAR EL XML AL WEBSERVICE - NUEVO SERVIDOR DONDE SE ALMACENARAN LOS XML GENERADOS Y TAMBIEN SE LLAMARAN LOS ARCHIVOS DE LOS XML PARA SU VISUALIZACION EN PDF O IMPRESION */
                    /** MEDIANTE CURL SE ENVIA EL ARCHIVO PARA SU GRABACIÓN */
                    $ch = curl_init("http://pyme.e-piramide.net/downxml/saveXml.php");
                    $fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $claveAccesoFile, 'archivo' => addslashes($resAuto));
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($ch);
                    if (curl_errno($ch)) {
                        $result = curl_error($ch);
                    }
                    curl_close($ch);
                    if ($procsError == true) {
                        /** SE VALIDA SI SE ACTUALIZO LA FECHA Y CLAVE DE AUTORIZACION*/
                        $updateSt->CancelarTransaccion($conx2);
                        $resultAuto = "0-Error al actualizar documento autorizado";
                        $resultEnvio = "";
                    } else {
                        unlink('xml/' . $_SESSION['ruc'] . '/sinfirma/Fact' . $nameDocum . '.xml');
                        $updateSt->CerrarTransaccion($conx2);
//            $resultEnvio = self::loadFileRsm($r[1], $nameDocum, $idFiles);
                        $resultAuto = "1-Documento #" . $nameDocum . " actualizado con exito";
                    }
                }
            }

            $resultAuto = $resultAuto;
            $resultEnvio = "0-no enviado";

        } else {

            $xmlsClave = simplexml_load_file($fxml);
            $updateClave = new FilesData();
            $updateClave->fi_claveacceso = '"' . $xmlsClave->infoTributaria->claveAcceso . '"';
            $updateClave->fi_estauto = 0;
            $updateClave->fi_fecauto = '"' . null . '"';
            $updateClave->fi_id = $idFiles;
            $updateClave->updateStateAuto();
            $updateState = new FilesData();
            $updateState->fi_logauto = $arrayXml->mensaje;
            $updateState->fi_id = $idFiles;
            $updateState->updateLogAuto();
            $resultAuto = "0-" . $arrayXml->mensaje;
            $resultEnvio = "0-No enviado";
        }
        $array = array();
        array_push($array, array("msjAuto" => $resultAuto));
        array_push($array, array("msjMail" => $resultEnvio));
        return $array;
    }

    public static function makeCurlFile($file)
    {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        return $output;
    }

    public static function loadFileRsm($claveAcceso, $numFact, $idFiles, $archivo, $reporte)
    {
        /**====== CON ESTA FUNCION SE GRABA EL ARCHIVO XML EN LA TABLA RSMFILE SI EL DOCUMENTO FUE AUTORIZADO =======*/
        $load = '';
        $error = false;
        $msjError = '';
        $msCorreo = '';
        $msjSuccess = "";
        $arrau = array();
        /** ======== FUNCION PARA EL PROCESO DE ENVIO DEL DOCUMENTO POR CORREO ELECTRONICO ========= */
//        se llama al codigo de establecimiento y punto de emision para obtener el id del formato
        if (empty($reporte) || is_null($reporte)) {
            $msjCorreos = PdfData::GeneraPdfFactura($claveAcceso, $numFact, FilesData::getByIdOne($idFiles)->ceid, $idFiles, $archivo);
        } else {
            $msjCorreos = PdfData::GeneraPdfFacturaFile($claveAcceso, $numFact, FilesData::getByIdOne($idFiles)->ceid, $idFiles, $archivo, $reporte);
        }
//        $msjCorreos = PdfData::GeneraPdfFactura($claveAcceso, $numFact, FilesData::getByIdOne($idFiles)->ceid, $idFiles, $archivo);
        if (substr($msjCorreos, 0, 1) == 1) {
            // SE VALIDA SI EL ENVIO DE CORREO FUE CORRECTO
            $upd_Send = new FilesData();
            $upd_Send->fi_stasend = 1;
            $upd_Send->fi_id = $idFiles;
            $upSend = $upd_Send->updateStateSendFiles();
            if ($upSend[0] == false) {
                $error = true;
                $msjError = "0-Documento no fue enviado por correo";
            }
            unlink('xml/' . $_SESSION['ruc'] . '/pdf/Fact' . $numFact . '.pdf');
            unlink('xml/' . $_SESSION['ruc'] . '/autorizados/Fact' . $numFact . '.xml');
        } else {
            $error = true;
            $msjError = "0-Documento no se envio por correo";
            unlink('xml/' . $_SESSION['ruc'] . '/pdf/Fact' . $numFact . '.pdf');
            unlink('xml/' . $_SESSION['ruc'] . '/autorizados/Fact' . $numFact . '.xml');
        }

        if ($error == false) {
            array_push($arrau, $msjCorreos);
            array_push($arrau, $msjError);
        } else {
            $msjXml = $msjError;
            array_push($arrau, $msjCorreos);
            array_push($arrau, $msjXml);
//      array_push($arrau, json_encode($archivo));
        }
        return $r = $arrau;
    }

    public static function file_post_contents($url, $data, $username = null, $password = null)
    {
        $postdata = http_build_query($data);
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $postdata
            )
        );
        if ($username && $password) {
            $opts['http']['header'] .= ("Authorization: Basic " . base64_encode("$username:$password")); // .= to append to the header array element
        }
        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }
}

?>
