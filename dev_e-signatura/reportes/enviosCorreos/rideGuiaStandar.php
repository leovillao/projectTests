<?php

$pdf = new PDF_Code128();
$pdfile = $pdf->AddPage();
$pdfile = $pdf->SetFont('arial', 'B', 16);
$claveacceso = $Xmls->numeroAutorizacion;
$fechauto = $Xmls->fechaAutorizacion;

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
        $razon = $valores->razonSocial;
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
/** ====================================================================================*/
$archivo = $pdf->Output('xml/' . $_SESSION['ruc'] . '/pdf/Guia' . $nombre . '.pdf', 'S');
file_put_contents('xml/' . $_SESSION['ruc'] . '/pdf/Guia' . $nombre . '.pdf', $archivo);
file_put_contents('xml/' . $_SESSION['ruc'] . '/autorizados/Guia' . $nombre . '.xml', $xmlFile);
