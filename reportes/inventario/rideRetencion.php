<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';

require '../../core/modules/index/model/RetFEData.php';
require '../../core/modules/index/model/RetFEdetData.php';
require '../../core/controller/RET_MakeXML.php';



$dataRetencion = FilesData::getByIdOne($_GET['id']);

$claveAcc = $dataRetencion->fi_claveacceso;

$ch = curl_init("http://pyme.e-piramide.net/downxml/getXml.php");
$fields = array('ruc' => $_SESSION['ruc'], 'razon' => EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre, 'clave' => $claveAcc);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$claveAcceso = $_GET['id'];
$xml = curl_exec($ch);
if (curl_errno($ch)) {
    $xml = curl_error($ch);
    exit();
}
curl_close($ch);

if (!empty($xml)) {
    /** SE LEE EL ARCHIVO RECIBIDO DESDE EL WEBSERVICES CON LA FUNCION ICONV*/
    $xmlFile = iconv('UTF-8', 'UTF-8//IGNORE', $xml);
    /** SE DEVUELVE EL ARCHIVO A LA FUNCION SIMPLEXML_LOAD_STRING PARA PARSEAR LAS ETIQUETAS Y VISULIZAR EL CONTENIDO */
    $Xmls = simplexml_load_string($xmlFile, 'SimpleXmlElement', LIBXML_NOCDATA);
    RetAutorizado($Xmls);
}else{
    RetNAutorizado($_GET['id']);
}


function RetAutorizado($Xmls)
{
    class PDF extends PDF_Code128
    {
        function Footer()
        {
            // Go to 1.5 cm from bottom
            $this->SetY(-35);
            // Select arial italic 8
            $this->SetFont('arial', 'I', 8);
            // Print centered page number
            if (isset($_SESSION['logoFooter']) && !empty($_SESSION['logoFooter'])) {
                $this->Image("../../" . $_SESSION['logoFooter'], 20, null, 180);
            }
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
        }
    }

    $empresa = EmpresasData::getEmpresaData();
    $pdf = new PDF();
    $pdf->AddPage();
    foreach ($Xmls->comprobante as $comprobante) {
        $xmls = simplexml_load_string($comprobante, 'SimpleXmlElement', LIBXML_NOCDATA);
        $ambiente = "PRUEBA";
        foreach ($xmls->infoTributaria as $valores) {
            $fuente = 'Arial';
            $pdf->SetFont($fuente, 'B', 16);
            $pdf->Image("../../" . $empresa->logo, 10, 5, 45, "jpg");
            $pdf->Ln();
            $pdf->SetFont($fuente, '', 16);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(90, 5, 'RUC : ' . $empresa->em_ruc, 0, 1, 'C', 0, 1);
            $pdf->SetTextColor(255, 0, 0);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(95, 7, 'RETENCION ', 0, 1, 'C', 0, 1);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($fuente, '', 19);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(90, 7, $valores->estab . $valores->ptoEmi . $valores->secuencial, 0, 1, 'C', 0, 1);
            $pdf->SetFont($fuente, '', 8);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->Cell(98, 5, 'NUMERO AUTORIZACION :', 0, 1, 'L', 0, 1);
            $pdf->SetFont($fuente, '', 13);
            $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
            $pdf->SetFont($fuente, '', 8);
            $pdf->Cell(98, 5, $valores->claveAcceso, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, $valores->nombreComercial, 0, 1, 'C', 0, 1);
            $pdf->SetFont($fuente, '', 9);
            $pdf->Cell(100, 4, $empresa->em_slogan, 0, 1, 'C', 0, 1);
            $pdf->SetFont($fuente, 'B', 13);
            $pdf->Cell(98, 5, $valores->razonSocial, 0, 0, 'L', 0, 1);
            $pdf->SetFont($fuente, '', 8);
            $pdf->Cell(98, 5, 'FECHA AUTORIZACION : ' . $Xmls->fechaAutorizacion, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'Direccion Matriz :', 0, 0, 'L', 0, 1);

            if ($valores->ambiente == 2) {
                $ambiente = "PRODUCCION";
            }
            $pdf->Cell(98, 5, 'AMBIENTE : ' . $ambiente, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, utf8_decode($valores->dirMatriz), 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, 'EMISION : ' . $valores->tipoEmision, 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'CONTRIBUYENTE ESPECIAL :' . $empresa->em_ceresolucion, 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, 'CLAVE DE ACCESO :', 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 5, 'OBLIGADO A LLEVAR CONTABILIDAD :' . $valores->em_obligado, 0, 0, 'L', 0, 1);
            $pdf->Cell(98, 5, $valores->claveAcceso, 0, 1, 'L', 0, 1);
            $code = $valores->claveAcceso;
        }
        foreach ($xmls->infoCompRetencion as $comprobante) {
            $pdf->Ln(12);
            $pdf->Cell(64, 5, 'IDENTIFICACION COMPRADOR : ' . $comprobante->identificacionSujetoRetenido, 'T,L', 0, 'L', 0, 1);
            $pdf->Cell(128, 5, 'FECHA DE EMISION : ' . $comprobante->fechaEmision, 'T,R', 1, 'C', 0, 1);
            $pdf->Cell(192, 5, 'RAZON SOCIAL : ' . $comprobante->razonSocialSujetoRetenido, 'R,L', 1, 'L', 0, 1);
            $pdf->Cell(192, 5, 'DIRECCION COMPRADOR : ' . $comprobante->dirEstablecimiento, 'R,L', 1, 'L', 0, 1);
            $pdf->Cell(64, 5, 'PERIODO FISCAL : ' . $comprobante->periodoFiscal, 'B,L', 0, 'L', 0, 1);
            $pdf->Cell(64, 5, "Comprobante : Factura", "B", 0, 'L', 0, 1);
            $pdf->Cell(64, 5, "Numero : " . $xmls->impuestos->impuesto[0]->numDocSustento, "B,R", 1, 'L', 0, 1);
            $pdf->Ln();
        }

        $pdf->Cell(32, 5, 'Ejercicio Fiscal', 'T,L', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, 'Base Imponible', 'T,L', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, 'Impuesto', 'T,L', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, 'Codigo Impuesto', 'T,L', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, 'Porcentaje', 'T,L', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, 'Valor Retenido', 'L,T,R', 1, 'C', 0, 1);
        for ($i = 0; $i < COUNT($xmls->impuestos->impuesto); $i++) {
            $impuesto = "IVA";
            if ($xmls->impuestos->impuesto[$i]->codigo == 1) {
                $impuesto = "FUENTE";
            }
            $pdf->Cell(32, 5, $xmls->infoCompRetencion->periodoFiscal, 'T,L', 0, 'C', 0, 1);
            $pdf->Cell(32, 5, $xmls->impuestos->impuesto[$i]->baseImponible, 'T,L', 0, 'C', 0, 1);
            $pdf->Cell(32, 5, $impuesto, 'T,L', 0, 'C', 0, 1);
            $pdf->Cell(32, 5, $xmls->impuestos->impuesto[$i]->codigoRetencion, 'T,L', 0, 'C', 0, 1);
            $pdf->Cell(32, 5, $xmls->impuestos->impuesto[$i]->porcentajeRetener, 'T,L', 0, 'C', 0, 1);
            $pdf->Cell(32, 5, $xmls->impuestos->impuesto[$i]->valorRetenido, 'L,T,R', 1, 'C', 0, 1);
            $total += $xmls->impuestos->impuesto[$i]->valorRetenido;
        }
        $pdf->Cell(32, 5, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(32, 5, $total, 'L,T,R,B', 1, 'C', 0, 1);

        $pdf->Cell(100, 5, "INFORMACION ADICIONAL", "L,R,B,T", 1, 'L', 0, 1);
        $campoAdi = $xmls->infoAdicional->campoAdicional;
        $pdf->Cell(100, 0, "" ,"B", 1, 1, '', 0, 0);
        for ($i = 0; $i < count($xmls->infoAdicional->campoAdicional); ++$i) {
            $pdf->Cell(40, 5, $xmls->infoAdicional->campoAdicional[$i]['nombre'] ,"L", 0, 0, '', 0, 0);
            $pdf->Cell(60, 5, $xmls->infoAdicional->campoAdicional[$i] ,"R", 1, 1, '', 0, 0);
        }
        $pdf->Cell(100, 1, "" ,"T", 1, 1, '', 0, 0);


    }
    $archivo = $pdf->Output('I');
}

function RetNAutorizado($idRetencion)
{
    /*class PDF extends PDF_Code128
    {
        function Footer()
        {
            // Go to 1.5 cm from bottom
            $this->SetY(-35);
            // Select arial italic 8
            $this->SetFont('arial', 'I', 8);
            // Print centered page number
            if (isset($_SESSION['logoFooter']) && !empty($_SESSION['logoFooter'])) {
                $this->Image("../../" . $_SESSION['logoFooter'], 20, null, 180);
            }
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'R');
        }
    }*/

    $rowRetencion = RetFEData::getById($idRetencion);
    $DetRetencion = RetFEdetData::getById($idRetencion);
    $Objret = FabricaRet::CreaRetencion($rowRetencion, $DetRetencion);
    $exito = FabricaRet::GetXML($Objret);

    var_dump($exito);

}


