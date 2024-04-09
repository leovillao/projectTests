<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/GuiaRemisionData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/GuiaFeData.php';

require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';


$pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$emails = '';
$guiaCab = GuiaRemisionData::getById($_GET['id']); // se obtiene el identificador en este caso la clave de acceso para enviar la llamada al servidor de documentos XML
$numDecimales = ConfigurationData::getByShortName("conf_num_decimal_precio")->cgdatoi;

if (isset($_GET['clave'])) {
    $claveAcc = $_GET['clave'];
} else {
    $claveAcc = $guiaCab->lo_claveacceso;
}

// se envia la clave de acceso para obtener el xml del documento a visualizar
$ch = curl_init("http://pyme.e-piramide.net/downxml/getXml.php");
$fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $claveAcc);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xml = curl_exec($ch);
if (strlen($xml) < 300) {
//    var_dump("no esta");

    $guiaCabecera = GuiaFeData::getByIdPDF($guiaCab->loid);
    $guiaDetalles = GuiaFeData::getDetalleGuiaRemisionPDF($guiaCab->loid);

    class PDF extends PDF_Code128
    {
        function Footer()
        {
            // Go to 1.5 cm from bottom
            $this->SetY(-35);
            // Select arial italic 8
            $this->SetFont('arial', 'I', 8);
            // Print centered page number
            if ($_SESSION['logoFooter'] != '') {
                $this->Image("../../" . $_SESSION['logoFooter'], 20, null, 180);
            }
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
        }
    }

    $pdf = new PDF();
    $pdfile = $pdf->AddPage();
    $pdfile = $pdf->SetFont('arial', 'B', 16);
    $claveacceso = $guiaCabecera->claveAcceso;
    $fechauto = "";

    if (!empty($pathXml->logo)) {
        $logo = "../../" . $pathXml->logo;
    } else {
        $logo = '';
    }

//    foreach ($Xmls->comprobante as $comprobante) {
//        $xmls = simplexml_load_string($comprobante, 'SimpleXmlElement', LIBXML_NOCDATA);
    $ambiente = "PRUEBA";
//        foreach ($xmls->infoTributaria as $valores) {
    if ($guiaCabecera->ambiente == 2) {
        $ambiente = "PRODUCCION";
    }
    if ($guiaCabecera->tipoEmision == 1) {
        $emision = "NORMAL";
    }
    $pdf->Image($logo, 35, 5, 45, "JPG");
    $pdf->Ln();
    $pdf->SetFont('arial', '', 16);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(90, 5, 'RUC : ' . $pathXml->em_ruc, 0, 1, 'C', 0, 1);
    $pdf->SetTextColor(255, 0, 0);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(95, 7, utf8_decode('GUIA DE REMISIóN'), 0, 1, 'C', 0, 1);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('arial', '', 19);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(90, 7, $guiaCabecera->estab . '-' . $guiaCabecera->ptoEmision . '-' . $guiaCabecera->secuencia, 0, 1, 'C', 0, 1);
    $pdf->SetFont('arial', '', 8);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
    $pdf->SetFont('arial', '', 13);
    $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
    $pdf->SetFont('arial', '', 8);
    $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
    $pdf->SetFont('arial', 'B', 14);
    $pdf->Cell(100, 5, $pathXml->em_comercial, 0, 1, 'C', 0, 1);
    $pdf->SetFont('arial', '', 9);
    $pdf->Cell(100, 4, $pathXml->em_slogan, 0, 1, 'C', 0, 1);
    $pdf->SetFont('arial', 'B', 13);
    $pdf->Cell(98, 5, $guiaCabecera->razonSocial, 0, 0, 'L', 0, 1);
    $pdf->SetFont('arial', '', 8);
    $pathXml = $guiaCabecera->razonSocial;
    $pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, $guiaCabecera->dirMatriz, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $guiaCabecera->contribuyenteEspecial, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
    $pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $guiaCabecera->obligadoContabilidad, 0, 0, 'L', 0, 1);
    $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
    $code = $claveacceso;
    $pdf->Code128(105, 73, $code, 100, 9);
    $pdf->Ln(12);
    if (isset($guiaCabecera->regimenMicroempresa) && !empty($guiaCabecera->regimenMicroempresas)) {
        $pdf->Cell(98, 5, utf8_decode("CONTRIBUYENTE RÉGIMEN MICROEMPRESAS"), 0, 1, 'L', 0, 1);
    }
    if (isset($guiaCabecera->agenteRetencion) && !empty($guiaCabecera->agenteRetencion)) {
        $pdf->Cell(98, 5, utf8_decode("AGENTE DE RETENCION RESOLUCION : NAC-DNCRASC20-00000001"), 0, 1, 'L', 0, 1);
    }
    $nombre = $guiaCabecera->estab . $guiaCabecera->ptoEmision . $guiaCabecera->secuencial;
//        }

    $pdf->Cell(190, 0, "", "B", 1, 'L', 0, 1);
//        foreach ($xmls->infoGuiaRemision as $guiaRemision) {
    $pdf->Cell(190, 5, utf8_decode('Identificación(Transportista) : ') . $guiaCabecera->rucTransportista, 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 5, utf8_decode('Razón Social / Nombres y Apellidos : ') . $guiaCabecera->razonSocialTransportista, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode('Placa : ') . $guiaCabecera->placa, 0, 0, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode('Punto de partida : ') . $guiaCabecera->dirPartida, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode('Fecha Inicio Transporte : ') . $guiaCabecera->fechaIniTransporte, 0, 0, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode('Fecha Inicio Transporte : ') . $guiaCabecera->fechaFinTransporte, 0, 1, 'L', 0, 1);
//        }
//        $pdf->Cell(190, 0, "", "B", 1, 'L', 0, 1);
//        $pdf->Ln(1);
//        foreach ($xmls->destinatarios as $destinatario) {
//            foreach ($destinatario as $des) {
    $docSustento = ($guiaCabecera->codDocSustento == "01") ? "FACTURA" : "EGRESO";
    $pdf->Cell(90, 5, "Comprobante de Venta  " . utf8_decode($docSustento) . " " . $guiaCabecera->numDocSustento, 0, 0, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Fecha de Emisión : ") . " " . $guiaCabecera->fechaEmisionDocSustento, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Número de Autorización : ") . " " . $guiaCabecera->numAutDocSustento, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
    $pdf->Cell(190, 1, "", "B", 1, 'L', 0, 1);

    $pdf->Cell(90, 5, utf8_decode("Motivo Traslado : ") . " " . $guiaCabecera->motivoTraslado, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Destino(Punto de Llegada) : ") . " " . $guiaCabecera->dirDestinatario, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Identificación(Destinatario) : ") . " " . $guiaCabecera->identificacionDestinatario, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Razón Social / Nombre Apellidos : ") . " " . $guiaCabecera->razonSocialDestinatario, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Documento Aduanero : ") . " " . $guiaCabecera->docAduaneroUnico, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Código Establecimiento Destino : ") . " " . $guiaCabecera->codEstabDestino, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 5, utf8_decode("Ruta : ") . " " . $guiaCabecera->ruta, 0, 1, 'L', 0, 1);
    $pdf->Ln(3);
    $pdfile = $pdf->SetFont('arial', 'B', 9);


    $pdf->Cell(30, 5, utf8_decode("Cantidad"), 1, 0, 'C', 0, 1);
    $pdf->Cell(100, 5, utf8_decode("Descripción"), 1, 0, 'C', 0, 1);
    $pdf->Cell(30, 5, utf8_decode("Código Principal"), 1, 0, 'C', 0, 1);
    $pdf->Cell(30, 5, utf8_decode("Código Auxiliar"), 1, 1, 'C', 0, 1);
    $pdfile = $pdf->SetFont('arial', '', 9);
//
    /**
     * ["codigo"]=> string(6) "000369" ["descripcion"]=> string(32) "AZITROMICINA 500 MG TAB PORTUGAL" ["cantidad"]=> string(7) "5.00000"
     */
    foreach ($guiaDetalles as $guiaDetalle) {
        $pdf->Cell(30, 5, $guiaDetalle->cantidad, 1, 0, 'C', 0, 0);
        $pdf->Cell(100, 5, $guiaDetalle->descripcion, 1, 0, 'C', 0, 0);
        $pdf->Cell(30, 5, $guiaDetalle->codigo, 1, 0, 'C', 0, 0);
        $pdf->Cell(30, 5, $guiaDetalle->codigo, 1, 1, 'C', 0, 0);
    }

} else {

    /** SE LEE EL ARCHIVO RECIBIDO DESDE EL WEBSERVICES CON LA FUNCION ICONV*/
    $xmlFile = iconv('UTF-8', 'UTF-8//IGNORE', $xml);
    /** SE DEVUELVE EL ARCHIVO A LA FUNCION SIMPLEXML_LOAD_STRING PARA PARSEAR LAS ETIQUETAS Y VISULIZAR EL CONTENIDO */
    $Xmls = simplexml_load_string($xmlFile, 'SimpleXmlElement', LIBXML_NOCDATA);

    /** CLASE QUE MUESTRA EL PIE DE PAGINA CON EL RESPECTIVO LOGO SETEADO EN CONFIGURACIONES */
    class PDF extends PDF_Code128
    {
        function Footer()
        {
            // Go to 1.5 cm from bottom
            $this->SetY(-35);
            // Select arial italic 8
            $this->SetFont('arial', 'I', 8);
            // Print centered page number
            if ($_SESSION['logoFooter'] != '') {
                $this->Image("../../" . $_SESSION['logoFooter'], 20, null, 180);
            }
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
        }
    }

    $pdf = new PDF();
    $pdfile = $pdf->AddPage();
    $pdfile = $pdf->SetFont('arial', 'B', 16);
    $claveacceso = $Xmls->numeroAutorizacion;
    $fechauto = $Xmls->fechaAutorizacion;
    if (!empty($pathXml->logo)) {
        $logo = "../../" . $pathXml->logo;
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
            $pdf->Image($logo, 35, 5, 45, "JPG");
            $pdf->Ln();
            $pdf->SetFont('arial', '', 16);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(90, 5, 'RUC : ' . $pathXml->em_ruc, 0, 1, 'C', 0, 1);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(95, 7, utf8_decode('GUIA DE REMISIóN'), 0, 1, 'C', 0, 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('arial', '', 19);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(90, 7, $valores->estab . '-' . $valores->ptoEmi . '-' . $valores->secuencial, 0, 1, 'C', 0, 1);
            $pdf->SetFont('arial', '', 8);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
            $pdf->SetFont('arial', '', 13);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->SetFont('arial', '', 8);
            $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
            $pdf->SetFont('arial', 'B', 14);
            $pdf->Cell(100, 5, $pathXml->em_comercial, 0, 1, 'C', 0, 1);
            $pdf->SetFont('arial', '', 9);
            $pdf->Cell(100, 4, $pathXml->em_slogan, 0, 1, 'C', 0, 1);
            $pdf->SetFont('arial', 'B', 13);
            $pdf->Cell(98, 5, $valores->razonSocial, 0, 0, 'L', 0, 1);
            $pdf->SetFont('arial', '', 8);
            $pathXml = $valores->razonSocial;
            $pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $fechauto, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, $valores->dirMatriz, 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, 'EMISION : ' . $emision, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $xmls->infoFactura->contribuyenteEspecial, 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $xmls->infoFactura->obligadoContabilidad, 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, $claveacceso, 0, 1, 'L', 0, 1);
            $code = $claveacceso;
            $pdf->Code128(105, 73, $code, 100, 9);
            $pdf->Ln(12);
            if (isset($valores->regimenMicroempresa) && !empty($valores->regimenMicroempresas)) {
                $pdf->Cell(98, 5, utf8_decode("CONTRIBUYENTE RÉGIMEN MICROEMPRESAS"), 0, 1, 'L', 0, 1);
            }
            if (isset($valores->agenteRetencion) && !empty($valores->agenteRetencion)) {
                $pdf->Cell(98, 5, utf8_decode("AGENTE DE RETENCION RESOLUCION : NAC-DNCRASC20-00000001"), 0, 1, 'L', 0, 1);
            }
            $nombre = $valores->estab . $valores->ptoEmi . $valores->secuencial;
        }
        $pdf->Cell(190, 0, "", "B", 1, 'L', 0, 1);
        foreach ($xmls->infoGuiaRemision as $guiaRemision) {
            $pdf->Cell(190, 5, utf8_decode('Identificación(Transportista) : ') . $guiaRemision->rucTransportista, 0, 1, 'L', 0, 1);
            $pdf->Cell(190, 5, utf8_decode('Razón Social / Nombres y Apellidos : ') . $guiaRemision->razonSocialTransportista, 0, 1, 'L', 0, 1);
            $pdf->Cell(90, 5, utf8_decode('Placa : ') . $guiaRemision->placa, 0, 0, 'L', 0, 1);
            $pdf->Cell(90, 5, utf8_decode('Punto de partida : ') . $guiaRemision->dirPartida, 0, 1, 'L', 0, 1);
            $pdf->Cell(90, 5, utf8_decode('Fecha Inicio Transporte : ') . $guiaRemision->fechaIniTransporte, 0, 0, 'L', 0, 1);
            $pdf->Cell(90, 5, utf8_decode('Fecha Inicio Transporte : ') . $guiaRemision->fechaFinTransporte, 0, 1, 'L', 0, 1);
        }
        $pdf->Cell(190, 0, "", "B", 1, 'L', 0, 1);
        $pdf->Ln(1);
        foreach ($xmls->destinatarios as $destinatario) {
            foreach ($destinatario as $des) {
                $docSustento = ($des->codDocSustento == "01") ? "FACTURA" : "EGRESO";
                $pdf->Cell(90, 5, "Comprobante de Venta  " . utf8_decode($docSustento) . " " . $des->numDocSustento, 0, 0, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Fecha de Emisión") . " " . $des->fechaEmisionDocSustento, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Número de Autorización") . " " . $des->numAutDocSustento, 0, 1, 'L', 0, 1);
                $pdf->Ln(1);
                $pdf->Cell(190, 1, "", "B", 1, 'L', 0, 1);

                $pdf->Cell(90, 5, utf8_decode("Motivo Traslado") . " " . $des->motivoTraslado, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Destino(Punto de Llegada) ") . " " . $des->dirDestinatario, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Identificación(Destinatario) ") . " " . $des->identificacionDestinatario, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Razón Social / Nombre Apellidos ") . " " . $des->razonSocialDestinatario, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Documento Aduanero") . " " . $des->docAduaneroUnico, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Código Establecimiento Destino") . " " . $des->codEstabDestino, 0, 1, 'L', 0, 1);
                $pdf->Cell(90, 5, utf8_decode("Ruta") . " " . $des->ruta, 0, 1, 'L', 0, 1);
                $pdf->Ln(3);
                $pdfile = $pdf->SetFont('arial', 'B', 9);


                $pdf->Cell(30, 5, utf8_decode("Cantidad"), 1, 0, 'C', 0, 1);
                $pdf->Cell(100, 5, utf8_decode("Descripción"), 1, 0, 'C', 0, 1);
                $pdf->Cell(30, 5, utf8_decode("Código Principal"), 1, 0, 'C', 0, 1);
                $pdf->Cell(30, 5, utf8_decode("Código Auxiliar"), 1, 1, 'C', 0, 1);
                $pdfile = $pdf->SetFont('arial', '', 9);

                $totDet = COUNT($des->detalles->detalle);
                for ($td = 0; $td < $totDet; ++$td) {
                    $pdf->Cell(30, 5, $des->detalles->detalle[$td]->cantidad, 1, 0, 'C', 0, 0);
                    $pdf->Cell(100, 5, $des->detalles->detalle[$td]->descripcion, 1, 0, 'C', 0, 0);
                    $pdf->Cell(30, 5, $des->detalles->detalle[$td]->codigoInterno, 1, 0, 'C', 0, 0);
                    $pdf->Cell(30, 5, $des->detalles->detalle[$td]->codigoInterno, 1, 1, 'C', 0, 0);
                }
            }
        }
    }
} // fin de validacion si exite el documento xml o carga la informacion desde las tablas

$archivo = $pdf->Output();
