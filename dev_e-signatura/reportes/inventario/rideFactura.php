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

$empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$pathXml = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
$emails = '';
$dpdf = FilesData::getByIdOne($_GET['id']);
$numDecimales = ConfigurationData::getByShortName("conf_num_decimal_precio")->cgdatoi;

if (isset($_GET['clave'])) {
    $claveAcc = $_GET['clave'];
} else {
    $claveAcc = $dpdf->fi_claveacceso;
}
$clientMails = PersonData::getByCeRut($dpdf->fi_er_ruc);
if ($clientMails->ceemail1 != '') {
    $emails .= $clientMails->ceemail1 . ',';
}
if ($clientMails->ceemail2 != '') {
    $emails .= $clientMails->ceemail2 . ',';
}
if ($clientMails->ceemail3 != '' || $clientMails->ceemail3 != null) {
    $emails .= $clientMails->ceemail3 . ',';
}
//var_export($emails);
/** EL ARCHIVO DEL WEBSERVICES */
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
/** SE RECORRE LAS ETIQUETAS DEL ARCHIVO XML PARA VISUALIZAR EL CONTENIDO */
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
        $pdf->Cell(90, 5, 'RUC : ' . $empresa->em_ruc, 0, 1, 'C', 0, 1);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(100, 5, '', 0, 0, 'C', 0, 1);
        $pdf->Cell(95, 7, 'FACTURA ', 0, 1, 'C', 0, 1);
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
        $empresa = $valores->razonSocial;
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
    $totAdi = count($xmls->infoAdicional->campoAdicional);
    $campoAdi = $xmls->infoAdicional->campoAdicional;

    $pdf->SetFont('arial', '', 8);
    $pdf->Cell(64, 5, 'FECHA DE EMISION : ' . $xmls->infoFactura->fechaEmision, 0, 0, 'L', 0, 1);
    $pdf->Cell(64, 5, 'IDENTIFICACION COMPRADOR : ' . $xmls->infoFactura->identificacionComprador, 0, 0, 'L', 0, 1);
    /* NUMERO DE TELEFONO DE LA CABECERA */
    for ($i = 0; $i < $totAdi; ++$i) {
        $telefono = $campoAdi[$i]['nombre'] == "telefonoCliente";
        if (isset($telefono)) {
            if ($campoAdi[$i]['nombre'] == "telefonoCliente") {
                $pdf->Cell(64, 5, 'Telefono :' . ' ' . $campoAdi[$i], 0, 1, 'C', 0, 0);
            }
        } else {
            $pdf->Cell(80, 5, ' ', 'R', 1, 'L', 0, 0);
        }
    }

    $pdf->Cell(100, 5, 'RAZON SOCIAL : ' . $xmls->infoFactura->razonSocialComprador, 0, 1, 'L', 0, 1);
    $pdf->Cell(98, 5, 'DIRECCION COMPRADOR : ' . $xmls->infoFactura->direccionComprador, 0, 1, 'L', 0, 1);
    /* COMENTARIO EN LA CABECERA DE LA FACTURA */
    for ($i = 0; $i < $totAdi; ++$i) {
        $comentario = $campoAdi[$i]['nombre'] == "comentario";
        if (isset($comentario)) {
            if ($campoAdi[$i]['nombre'] == "comentario") {
                $pdf->Cell(95, 5, 'COMENTARIO :' . ' ' . $campoAdi[$i], 0, 1, 'L', 0, 0);
            }
        } else {
            $pdf->Cell(80, 5, ' ', 'R', 1, 'L', 0, 0);
        }

    }
    $pdf->Ln();
    $pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
    $pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
    $totDet = COUNT($xmls->detalles->detalle);
    $td = 0;
    $totDesc = 0;
    for ($td = 0; $td < $totDet; ++$td) {

        $toDet = COUNT($xmls->detalles->detalle[$td]->detallesAdicionales->detAdicional);
        $adiDetalle = $xmls->detalles->detalle[$td]->detallesAdicionales->detAdicional;

        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->codigoPrincipal, '', 0, 'C', 0, 1);
        $pdf->Cell(70, 5, utf8_decode($xmls->detalles->detalle[$td]->descripcion), '', 0, 'L', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->cantidad, '', 0, 'C', 0, 1);
        $pdf->Cell(20, 5, 'Und', '', 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioUnitario, '', 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->descuento, '', 0, 'C', 0, 1);
        $pdf->Cell(20, 5, $xmls->detalles->detalle[$td]->precioTotalSinImpuesto, '', 1, 'C', 0, 1);

        $tdd = 0;
        if (!empty($adiDetalle)) {
            for ($j = 0; $j < $toDet; $j++) {
                $pdf->Cell(20, 4, '', '', 0, 'L', 0, 1);
//              $pdf->MultiCell(70,4,'This is MultiCell - Welcome to plus2net.com','LRTB','L',false);
                $pdf->MultiCell(70, 4, utf8_decode($adiDetalle[$j]['valor']), 0, 1);
                $pdf->Cell(20, 4, '', '', 0, 'L', 0, 1);
                $pdf->Cell(20, 4, '', '', 0, 'L', 0, 1);
                $pdf->Cell(20, 4, '', '', 0, 'L', 0, 1);
                $pdf->Cell(20, 4, '', '', 0, 'L', 0, 1);
                $pdf->Cell(20, 4, '', '', 1, 'L', 0, 1);
            }
        }
        $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(70, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(20, 0, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(20, 0, '', 'T,R', 1, 'C', 0, 1);

        $totDesc = $totDesc + $xmls->detalles->detalle[$td]->descuento;
    }
    $pdf->Cell(130, 5, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(40, 5, 'Venta Bruta ', 1, 0, 'R', 0, 1);
    $pdf->Cell(20, 5, $xmls->infoFactura->totalSinImpuestos, 1, 1, 'C', 0, 1);
    $pdf->SetFont('arial', '', 10);
    $pdf->Cell(100, 5, utf8_decode('FORMAS DE PAGO'), 'T,R,L', 0, 'L', 0, 1);
    $pdf->SetFont('arial', '', 9);
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
    $pdf->Cell(40, 5, 'Base 12% ', 1, 0, 'R', 0, 1);
    $pdf->Cell(20, 5, $xmls->infoFactura->totalConImpuestos->totalImpuesto[0]->baseImponible, 1, 1, 'C', 0, 1);
    $pdf->Cell(100, 5, 'Moneda : ' . $xmls->infoFactura->moneda, 'R,L', 0, 'L', 0, 1);
    $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
    $pdf->Cell(40, 5, 'Base 0% ', 1, 0, 'R', 0, 1);
    $pdf->Cell(20, 5, $xmls->infoFactura->totalConImpuestos->totalImpuesto[1]->baseImponible, 1, 1, 'C', 0, 1);
    $pdf->Cell(100, 5, 'Total ' . $pago->total . ' Plazo ' . $pago->plazo . ' ' . $pago->unidadTiempo, 'R,B,L', 0, 'L', 0, 1);
    $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
    $pdf->Cell(40, 5, 'Subtotal S/I ', 1, 0, 'R', 0, 1);
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
    $pdf->SetFont('arial', '', 9);
    $totCampos = count($xmls->infoAdicional->campoAdicional);
    $campoAdi = $xmls->infoAdicional->campoAdicional;
    for ($j = 0; $j < $totCampos; $j++) {
        if ($campoAdi[$j]['nombre'] != 'IdTransac' && $campoAdi[$j]['nombre'] != 'comentario') {
            $pdf->Cell(100, 5, utf8_decode($campoAdi[$j]['nombre']) . ' :' . ' ' . utf8_decode($campoAdi[$j]), 'L,R', 1, 'L', 0, 1);
        }
    }
    $pdf->Cell(100, 5, '', 'B,L,R', 1, 'R', 0, 1);

}
$archivo = $pdf->Output();

