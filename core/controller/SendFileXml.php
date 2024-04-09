<?php
//require_once 'Esquemas.php';
//require_once 'core/modules/index/model/RsmData.php';
//require_once 'core/modules/index/model/EmpresasData.php';
class EnviaFile
{

// ENVIO DEL DOCUMENTO ELECTRONICO
// DEFINO LOS PARAMETROS DEL ENVIO
    public static function procesar($nameDocum)
    {
        $ch = curl_init("http://pyme.e-piramide.net:8080/FE/Procesar?wsdl");
        $pathXml = EmpresasData::getByRuc($_SESSION['ruc']);
        $fxml = $pathXml->path_xml.'/sinfirma/Ret' . $nameDocum . '.xml';  // ARCHIVO CONSTRUIDO

        $xml = self::makeCurlFile($fxml);

        $ambiente = $pathXml->em_ambiente;  // Ambiente de trabajo
//$filep12='C:\\home\\demo\\0912925203-ljr729.p12';  // ruta del file en sel servidor de FE

//        $filep12 = '/home/cbarcia/0912925203-ljr729.p12';  // ruta del file en sel servidor de FE
        $filep12 = $pathXml->firma;  // ruta del file en sel servidor de FE

        $pswp12 = $pathXml->em_clave_p_12;  // Clave de la firma.

// PREPARO EL ARREGLO DE PARAMETROS

        $data = array('file' => $xml, 'ambiente' => $ambiente, 'fp12' => $filep12, 'pp12' => $pswp12);
// CONFIGURO LA CONEXION
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


    public static function makeCurlFile($file)
    {
        $mime = mime_content_type($file);
        $info = pathinfo($file);
        $name = $info['basename'];
        $output = new CURLFile($file, $mime, $name);
        return $output;
    }

}

?>