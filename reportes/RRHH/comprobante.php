<?php
require 'cabeceraReport.php';
        $cabecera = ro_haberdesccabeceraData::getByIdhd($_GET['id']);
        $detalle = ro_haberdescdetalleData::getByIdhd($cabecera->hdid);
        $pdf = new PDF();
        $pdf->AddPage(); 
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 9);
        $altoTitulo = 7;
        $pdf->Ln(7);
        $pdf->Cell(40, $altoTitulo, utf8_decode('Nº de Cuota'), 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(40, $altoTitulo, utf8_decode('Periodo'), 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(40, $altoTitulo, utf8_decode('Año'), 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(40, $altoTitulo, utf8_decode('Valor'), 'T,B,R', 0, 'C', 0, 0);
        $alto = 6;
        $pdf->Ln();
        foreach ($detalle as $detalledata) {
            $pdf->Cell(40, $alto, utf8_decode($detalledata->ddcuota), 'L,T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(40, $alto, utf8_decode(ro_periodosData::getById($detalledata->peid)->pedescrip), 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(40, $alto, utf8_decode($detalledata->ddanio), 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(40, $alto, $detalledata->ddvalor, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Ln();
            //$tttotal = $tttotal + $detalledata->ddvalor;
        }
        $pdf->Ln(10);
        $pdf->Cell(40, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(40, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(40, $alto, 'Totales', 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(40, $alto, $cabecera->hdtotal, 'T,B,R', 1, 'R', 0, 0);
        $pdf->Ln();
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, '', 'B', 1, 'C', 0, 0);
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, utf8_decode('Recibí conforme'), 0, 0, 'C', 0, 0);
        
        $pdf->Output();

?>