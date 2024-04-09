<?php

class GuiaSendFileXml
{
    /** ENVIO DEL DOCUMENTO ELECTRONICO*/
    /** DEFINO LOS PARAMETROS DEL ENVIO*/
    public static function procesarDoc($nameDocum, $idFiles, $reporte)
    {
        $pathXml = EmpresasData::getEmpresaData();
        $ch = curl_init($pathXml->em_wsdl);
        // graba el xml sin firma en la carpeta de documentos sin deacuerdo al cliente para el respectivo proceso de envio del XML para la autorizacion
        $fxml = "xml/" . $_SESSION['ruc'] . "/sinfirma/Guia" . $nameDocum . ".xml";
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
                $updateState = new GuiaRemisionData();
                $proccError = false;
                if ($updateState->AbrirTransaccion() == false) {
                    $error = "0-Error al iniciar la transaccion del estado del documento electrónico";
                } else {
                    $conx2 = $updateState::$Conx;
                    $updateState->loid = $idFiles;
                    $updateState->lo_stateauto = 1;
                    $updateState->lo_fecauto = '"' . $fecauto . '"';
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
                        unlink("xml/" . $pathXml->em_ruc . "/sinfirma/Guia" . $nameDocum . ".xml");
                        $updateState->CerrarTransaccion($conx2);
                        // FUNCION QUE ENVIO LOS DOCUMENTOS XML Y PDF POR CORREO
                        $resultEnvio = self::loadFileRsm($claveautorizacion, $nameDocum, $idFiles, $resultFileXml, $reporte);
//                        $resultEnvio = "";
                        $resultAuto = "1-Documento #" . $nameDocum . " autorizado con exito";
                    }
                }
            } else {
                $xmlsClave = simplexml_load_file($fxml);
                $updateState = new GuiaRemisionData();
                $updateState->lo_logauto = $arrayXml->mensaje;
                $updateState->loid = $idFiles;
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

            $cTxt = curl_init("http://srv01.e-piramide.net/downxml/crearLog.php");
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

        $array = array();

        array_push($array, array("msjAuto" => $resultAuto));
        array_push($array, array("msjMail" => $resultEnvio));
        array_push($array, array("id" => $idFiles));
        array_push($array, array("reporte" => $reporte));

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

    public static function loadFileRsm($claveAcceso, $numGuia, $idFiles, $archivo, $reporte)
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
            $msjCorreos = PdfData::GeneraPdfGuia($claveAcceso, $numGuia, GuiaRemisionData::getById($idFiles)->ceid, $idFiles, $archivo);
        } else {
            $msjCorreos = PdfData::GeneraPdfGuiaFile($claveAcceso, $numGuia, GuiaRemisionData::getById($idFiles)->ceid, $idFiles, $archivo, $reporte);
        }

        if (substr($msjCorreos, 0, 1) == 1) {
            // SE VALIDA SI EL ENVIO DE CORREO FUE CORRECTO
            $upd_Send = new GuiaRemisionData();
            $upd_Send->lo_statesend = 1;
            $upd_Send->loid = $idFiles;
            $upSend = $upd_Send->updateStateSendFiles();
            if ($upSend[0] == false) {
                $error = true;
                $msjError = "0-Documento no fue enviado por correo";
            }
            unlink('xml/' . $_SESSION['ruc'] . '/pdf/Guia' . $numGuia . '.pdf');
            unlink('xml/' . $_SESSION['ruc'] . '/autorizados/Guia' . $numGuia . '.xml');
        } else {
            $error = true;
            $msjError = "0-Documento no se envio por correo";
            unlink('xml/' . $_SESSION['ruc'] . '/pdf/Guia' . $numGuia . '.pdf');
            unlink('xml/' . $_SESSION['ruc'] . '/autorizados/Guia' . $numGuia . '.xml');
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
