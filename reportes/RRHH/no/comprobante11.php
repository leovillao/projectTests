<?php

require 'cabeceraReporte.php';
        $pdf = new PDF();
        $pdf->AddPage(); 
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'B', 16); // titulos
        $pdf->Cell(190, 7, strtoupper(TipoOperationData::getById($ventas->toid)->todescrip), 0, 1, 'C', 0, 0);

        /*=================
        cabecera de reporte
        =================*/
        $pdf->SetFont('Arial', '', 9); // titulos
        $altoTitulo = 7;
        $pdf->Ln(4);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(23, $altoTitulo, 'Documento :', 0, 0, 'L', 0, 0);
        $pdf->Cell(23, $altoTitulo, '# ' . $ventas->opnumdoc, 0, 0, 'L', 0, 0);
        $pdf->Cell(23, $altoTitulo, 'BODEGA : ', 0, 0, 'L', 0, 0);
        $pdf->Cell(23, $altoTitulo, "dasd", 0, 0, 'L', 0, 0);
        $pdf->Cell(98, $altoTitulo, 'Num CONTROL # : ' . $id, 0, 1, 'R', 0, 0);
        $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $ventas->opfecha, 0, 0, 'L', 0, 0);
        $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $ventas->opcomenta, 0, 1, 'L', 0, 0);

        $pdf->Cell(70, $altoTitulo, 'PRODUCTO', 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'UNIDAD', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'CANTIDAD', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'COSTO UNITARIO', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'TOTAL', 'T,B,R', 1, 'C', 0, 0);
        $alto = 6;
        foreach ($ventasDet as $venta) {
            $pdf->Cell(70, $alto, utf8_decode(ProductData::getById($venta->itid)->itname), 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(30, $alto, utf8_decode(UnitData::getById($venta->unid_dig)->undescrip), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(30, $alto, utf8_decode($venta->odcandig), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(30, $alto, FData::formatoNumero($venta->odcostoudig), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(30, $alto, FData::formatoNumero($venta->odcostotot), 'T,B,R', 1, 'R', 0, 0);
            $tttotal = $tttotal + $venta->odcostotot;
        }
        $pdf->Ln(5);
        $pdf->Cell(130, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(30, $alto, 'Totales', 'L,T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumero($tttotal), 'T,B,R', 1, 'R', 0, 0);

        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, '', 'B', 1, 'C', 0, 0);
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, strtoupper(UserData::getById($ventas->user_id)->name), 0, 0, 'C', 0, 0);

//$pdf->MultiCell(190, 5, json_encode($ventas) , 0, 'C', 0, 1);
        $pdf->Output();

?>