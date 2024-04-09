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
require '../../core/modules/index/model/SucursalData.php';
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
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
reporteVentas::reportAnticipo($_GET['id']);

$anticipo = AnticipocabData::getById($_GET['id']);
$detalles = AnticipodetData::getByDetAnticipo($_GET['id']);
//var_dump($anticipo);
class reporteVentas
{

    public static function reportAnticipo($id)
    {
        /* CABECERA  DE INGRESO */
        $anticipo = AnticipocabData::getById($id);
        $detalles = AnticipodetData::getByDetAnticipo($id);

        /* DETALLE DE INGRESO */
        require 'cabeceraReport.php';
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 10); // titulos
        $pdf->Cell(190, 5, "Tipo de Anticipo : ", 0, 1, 'L', 0, 0);
        $pdf->SetFont('Arial', 'B', 16); // titulos
        $pdf->Cell(190, 6, strtoupper(TipocobroData::getById($anticipo->tcid)->tcdescrip), 0, 1, 'C', 0, 0);

        /*=================
        cabecera de reporte
        =================*/
        $pdf->SetFont('Arial', '', 9); // titulos
        $altoTitulo = 7;
        $pdf->Ln(4);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(23, $altoTitulo, 'Cliente :', 0, 0, 'L', 0, 0);
        $pdf->Cell(110, $altoTitulo, PersonData::getById($anticipo->ceid)->cename, 0, 0, 'L', 0, 0);
        $pdf->Cell(23, $altoTitulo, 'Documento :', 0, 0, 'L', 0, 0);
        $pdf->Cell(23, $altoTitulo, '# ' . $anticipo->anid, 0, 1, 'L', 0, 0);
        if (!is_null($anticipo->suid)) {
            $pdf->Cell(23, $altoTitulo, 'SUCURSAL : ', 0, 0, 'L', 0, 0);
            $pdf->Cell(23, $altoTitulo, SucursalData::getById($anticipo->suid)->suname, 0, 1, 'L', 0, 0);
        }
//        $pdf->Cell(98, $altoTitulo, 'Tipo Cobro # : ' . TipoCobro::getById($anticipo->tcid)->tcdescrip, 0, 1, 'R', 0, 0);
        $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $anticipo->anfecha, 0, 0, 'L', 0, 0);
        $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $anticipo->anconcepto, 0, 1, 'L', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $pdf->SetFillColor(192,192,192);
//        $pdf->Cell(40, 5, 'ANTICIPO', 'L,T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(40, 5, 'FECHA', 'L,T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(110, 5, 'FORMA', 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(40, 5, 'VALOR', 'T,B,R', 1, 'C', 1, 0);
        $alto = 6;
        foreach ($detalles as $detalle) {
//            $pdf->Cell(40, 5, $detalle->anid, 'L,T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(40, 5, $detalle->anfecha, 'L,T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(110, 5, $detalle->cfname, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(40, 5, FData::formatoNumeroReportes($detalle->afvalor), 'T,B,R', 1, 'R', 0, 0);
            $tttotal = $tttotal + $detalle->afvalor;
        }
        $pdf->Ln(5);
//        $pdf->Cell(130, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(150, $alto, 'Totales', 'L,T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(40, $alto, FData::formatoNumeroReportes($tttotal), 'T,B,R', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 7); // titulos
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, '', 'B', 1, 'C', 0, 0);
        $pdf->Cell(15, $alto, '', 0, 0, 'L', 0, 0);
        $pdf->Cell(80, $alto, strtoupper(UserData::getById($anticipo->user_id)->name), 0, 0, 'C', 0, 0);

        $pdf->Output();

    }

}

