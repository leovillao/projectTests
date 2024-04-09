<?php

if (strlen(session_id()) < 1) {
    session_cache_limiter('none');
    session_start();
}
include_once 'PDF.php';
include_once 'emisorData.php';
$pdf = new PDF();
$pdf->AddPage();
$pdf->Cell(80);
$content = iconv('UTF-8', 'UTF-8//IGNORE', $value);
$xmlDecode = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
$xml = simplexml_load_string($xmlDecode, 'SimpleXmlElement', LIBXML_NOCDATA);
$nombre = 0;
$pdf->Image('imagenes/'.emisorData::getLogo(), 5, 5, 100);
$pdf->Ln();
foreach ($xmls->infoFactura as $valor) {
    foreach ($xmls->infoTributaria as $valores) {
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(100, 6, '', 0, 0, 'C', 0, 1);
        $pdf->Cell(95, 6, 'Factura '.$valores->estab.'-'.$valores->ptoEmi.'-'.$valores->secuencial, 0, 1, 'C', 0, 1);
        $nombre = $valores->estab.$valores->ptoEmi.$valores->secuencial;
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(100, 6, '', 0, 0, 'C', 0, 1);
        $pdf->Cell(95, 6, $valor->fechaEmision, 0, 1, 'C', 0, 1);
        $pdf->Ln(12);
    }
}
$pdf->Ln();
$pdf->SetFont('Arial', '', 9);
foreach ($xml->comprobante as $comp) {
    $xmls = simplexml_load_string($comp, 'SimpleXmlElement', LIBXML_NOCDATA);
    foreach ($xmls->infoFactura as $valor) {
        foreach ($xmls->infoTributaria as $valores) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(95, 4, $valores->razonSocial, 0, 0, 'L', 0, 1);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(95, 4, 'Autorizacion : ', 0, 1, 'L', 0, 1);
            $pdf->Cell(95, 4, 'RUC : '.$valores->ruc, 0, 0, 'L', 0, 1);
            $pdf->Cell(95, 4, $xml->numeroAutorizacion, 0, 1, 'L', 0, 1);
            $pdf->Cell(95, 4, 'Matriz : '.$valores->dirMatriz, 0, 0, 'L', 0, 1);
            $pdf->Cell(95, 4, 'Fecha : '.$xml->fechaAutorizacion, 0, 1, 'L', 0, 1);
            $pdf->Cell(95, 4, 'Establecimiento : '.$valor->dirEstablecimiento, 0, 0, 'L', 0, 1);
            switch ($valores->ambiente) {
                case 1:
                    $pdf->Cell(95, 4, 'Ambiente Pruebas ', 0, 1, 'L', 0, 1);
                    break;
                default:
                    $pdf->Cell(95, 4, 'Ambiente Produccion ', 0, 1, 'L', 0, 1);
                    break;
            }
            $pdf->Cell(95, 4, 'Contribuyente Especial : '.$valor->contribuyenteEspecial, 0, 0, 'L', 0, 1);
            switch ($valores->tipoEmision) {
                case 1:
                    $pdf->Cell(95, 4, 'Emision : Normal', 0, 1, 'L', 0, 1);
                    break;
                default:
                    $pdf->Cell(95, 4, 'Emision : Normal', 0, 1, 'L', 0, 1);
                    break;
            }
            $pdf->Cell(95, 4, 'Obligado a llevar Contabilidad : '.$valor->obligadoContabilidad, 0, 0, 'L', 0, 1);
            $pdf->Cell(95, 4, 'Clave : ', 0, 1, 'L', 0, 1);
            $pdf->Cell(100, 4, '', 0, 0, 'C', 0, 1);
            $code = $valores->claveAcceso;
            $pdf->Code128(100, 66, $code, 105, 8);
            $pdf->SetXY(109, 75);
            $pdf->Write(5, $code);
            $pdf->Ln();
            $pdf->Line(10, 83, 200, 83);
            $pdf->Ln();
        }
    }
    $pdf->Cell(95, 5, $valor->razonSocialComprador, 0, 1, 'L', 0, 1);
    switch ($valor->tipoIdentificacionComprador) {
       case 05:
            $pdf->Cell(95, 5, 'Cedula '.$valor->identificacionComprador, 0, 1, 'L', 0, 1);
            break;
    }
    $pdf->Cell(95, 5, utf8_decode('Fecha Emisión :').$valor->fechaEmision, 0, 1, 'L', 0, 1);
    $pdf->Cell(95, 5, utf8_decode('Dirección :').$valor->direccionComprador, 0, 1, 'L', 0, 1);
    $pdf->Line(10, 108, 200, 108);
    $pdf->Ln();
    $pdf->Cell(20, 5, utf8_decode('Código'), 1, 0, 'C', 0, 1);
    $pdf->Cell(70, 5, utf8_decode('Descripción'), 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, utf8_decode('Cantidad'), 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Unidad', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Pre.unit', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Desc', 1, 0, 'C', 0, 1);
    $pdf->Cell(20, 5, 'Total', 1, 1, 'C', 0, 1);
    $totDet = count($xmls->detalles->detalle);
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
    $pdf->Cell(20, 5, $valor->totalSinImpuestos, 1, 1, 'C', 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(130, 5, 'Formas de Pago', 0, 0, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
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
                    $pdf->Cell(100, 5, 'OTROS CON UTILIZACION DEL SISTEMA FINANCIERO', 'T,R,L', 0, 'L', 0, 1);
                    break;
                default:
                    $pdf->Cell(100, 5, 'ENDOSO DE TITULOS', 'T,R,L', 0, 'L', 0, 1);
                    break;
           }
        $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, 'Sub 12% ', 1, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, $valor->totalSinImpuestos, 1, 1, 'C', 0, 1);
        $pdf->Cell(100, 5, 'Total Sin Impuestos : '.$valor->totalSinImpuestos, 'R,L', 0, 'L', 0, 1);
        $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, 'Sub 0% ', 1, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, $totDesc, 1, 1, 'C', 0, 1);
        $pdf->Cell(100, 5, 'Total Descuentos : '.$valor->totalDescuento, 'R,L', 0, 'L', 0, 1);
        $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, 'SubTotalSin Impuestos ', 1, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, $totDesc, 1, 1, 'C', 0, 1);
        $pdf->Cell(100, 5, 'Total Propina : '.$valor->propina, 'R,L', 0, 'L', 0, 1);
        $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, 'Iva ', 1, 0, 'R', 0, 1);
//            $iva = money_format($valor->totalSinImpuestos);
        foreach ($xmls->infoFactura->totalConImpuestos->totalImpuesto as $impuesto) {
            $pdf->Cell(20, 5, $impuesto->valor, 1, 1, 'C', 0, 1);
        }
        $pdf->Cell(100, 5, 'Total : '.$pago->total, 'R,L', 0, 'L');
        $pdf->Cell(30, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, 'Total ', 1, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, $pago->total, 1, 1, 'C', 0, 1);
        $pdf->Cell(100, 5, 'Moneda : '.$valor->moneda, 'R,L', 0, 'L', 0, 1);
        $pdf->Cell(40, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, '', 0, 1, 'C', 0, 1);
        $pdf->MultiCell(100, 5, 'Plazo '.$pago->plazo.' '.$pago->unidadTiempo, 'R,B,L', 'L', 0);
        $pdf->Cell(40, 5, '', 0, 0, 'R', 0, 1);
        $pdf->Cell(20, 5, '', 0, 1, 'C', 0, 1);
    }
    if (!is_null($xmls->infoAdicional->campoAdicional)) {
        $totAdi = count($xmls->infoAdicional->campoAdicional);
        $campoAdi = $xmls->infoAdicional->campoAdicional;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(130, 5, utf8_decode('Información Adicional'), 0, 1, 'L', 0, 1);
        $pdf->SetFont('Arial', '', 9);
        for ($i = 0; $i < $totAdi; ++$i) {
            $pdf->Cell(130, 5, $campoAdi[$i]['nombre'].' '.$campoAdi[$i], 1, 1, 'L', 0, 1);
        }
    }
}
$pdf->Output($nombre.'.pdf', 'D');
//$pdf->Output();
