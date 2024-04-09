<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
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
require '../../core/modules/index/model/FData.php';
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

reporteVentas::reportIngreso($_GET['id']);

class reporteVentas
{

    public static function reportIngreso($id)
    {
        /* CABECERA  DE INGRESO */
        $ventas = OperationData::getById($id);
        $ventasDet = OperationdetData::getByIdsOpid($id);
        /* DETALLE DE INGRESO */
        require 'cabeceraReport.php';
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
        $pdf->Cell(23, $altoTitulo, BodegasData::getById($ventas->boid)->bodescrip, 0, 0, 'L', 0, 0);
        $pdf->Cell(98, $altoTitulo, 'Num CONTROL # : ' . $id, 0, 1, 'R', 0, 0);
        $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $ventas->opfecha, 0, 0, 'L', 0, 0);
        $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $ventas->opcomenta, 0, 1, 'L', 0, 0);

        $pdf->Cell(80, $altoTitulo, 'PRODUCTO', 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(20, $altoTitulo, 'UNIDAD', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'CANTIDAD', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'COSTO UNITARIO', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(30, $altoTitulo, 'TOTAL', 'T,B,R', 1, 'C', 0, 0);
        $alto = 6;
      $pdf->SetFont('Arial', '', 7); // titulos
      foreach ($ventasDet as $venta) {
            $pdf->Cell(80, $alto, utf8_decode(ProductData::getById($venta->itid)->itname), 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(20, $alto, utf8_decode(UnitData::getById($venta->unid_dig)->undescrip), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(30, $alto, FData::formatoNumeroReportsInventario($venta->odcandig), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(30, $alto, FData::formatoNumeroReportsInventario($venta->odcostoudig), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(30, $alto, FData::formatoNumeroReportsInventario($venta->odcostotot), 'T,B,R', 1, 'R', 0, 0);
            $tttotal = $tttotal + $venta->odcostotot;
        }
//      $pdf->SetFont('Arial', '', 7); // titulos

      $pdf->Ln(5);
        $pdf->Cell(130, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(30, $alto, 'Totales', 'L,T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumeroReportsInventario($tttotal), 'T,B,R', 1, 'R', 0, 0);

        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, '', 'B', 1, 'C', 0, 0);
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, strtoupper(UserData::getById($ventas->user_id)->name), 0, 0, 'C', 0, 0);

        $pdf->Output();

    }

}

