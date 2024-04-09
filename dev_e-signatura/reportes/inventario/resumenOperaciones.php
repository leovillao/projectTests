<?php
//var_export($_POST);
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/DeudasData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CrucedeudasData.php';
require '../../core/modules/index/model/CobrostData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipocobroData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, 'RESUMEN DE OPERACIONES', 0, 1, 'C', 0, 0);
    }
}

$pdf = new PDF();

$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(190, 7, 'RESUMEN DE OPERACIONES DEL ' . $_POST['fechaCorte'], 0, 1, 'C', 0, 0);
$pdf->SetFont('Arial', '', 9); // titulos
$altoTitulo = 5;
$pdf->Ln(2);
$pdf->SetFillColor(192, 192, 192);

// 5  FRANQUICIADOS
$docFilesPosFile = FilesData::getDocumentosPosFiles($_POST['fechaCorte']); // se obtiene la informacion de documentos de venta
//$documentosNCRs = FilesData::getDocumentosEtiquetaFechaNcr($_POST['fechaCorte']); // se obtiene la informacion de NCR
$totalAumentoCartera = 0;
$totalDisminucionCartera = 0;

$pdf->SetFont('Arial', 'B', 9); // titulos
$pdf->Cell(140, 6, 'VENTAS POS', '', 1, 'L', 0, 0);
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(50, 6, 'Venta (+)', '', 0, 'L', 0, 0);
$pdf->Cell(15, 6, number_format($docFilesPosFile->dpvalor, 2), "", 0, 'R', 0, 0);
$pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
$pdf->Cell(50, 6, 'NCR (-)', '', 0, 'L', 0, 0);
$pdf->Cell(15, 6, number_format(0, 2), "", 0, 'R', 0, 0);
$pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
$pdf->Cell(20, 6, '', '', 0, 'R', 0, 0);
$pdf->Cell(30, 6, 'Total', '', 0, 'C', 0, 0);
$pdf->Cell(15, 6, number_format(0, 2), "", 0, 'R', 0, 0);
$pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
//$detalleFormas = CobrostData::getAllIdsDeudasForFormasPago($_POST['fechaCorte'], 5);
$totalCobrosPos = 0;
$pdf->SetFont('Arial', 'B', 9); // titulos
$pdf->Cell(50, 6, "Cobros", '', 1, 'R', 0, 0);
$pdf->SetFont('Arial', '', 8); // titulos
$detalleFormasPos = FilesData::getByCobrosDetPos($_POST['fechaCorte']);

foreach ($detalleFormasPos as $detalleCobrosPos) {
    $pdf->Cell(35, 6, '', 0, 0, 'R', 0, 0);
    $pdf->Cell(30, 6, utf8_decode($detalleCobrosPos->cfname), 0, 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($detalleCobrosPos->dpvalor, 2), 0, 1, 'R', 0, 0);
    $totalCobrosPos += $detalleCobrosPos->dpvalor;
}

$pdf->SetFont('Arial', 'B', 9); // titulos
$pdf->Cell(35, 6, '', 0, 0, 'R', 0, 0);
$pdf->Cell(30, 6, 'Totales', '', 0, 'R', 0, 0);
$pdf->Cell(16, 6, number_format($totalCobrosPos, 2), "", 1, 'R', 0, 0);


// 5  FRANQUICIADOS
$documentos = FilesData::getDocumentosEtiquetaFecha($_POST['fechaCorte'], 5); // se obtiene la informacion de documentos de venta
$documentosNCRs = FilesData::getDocumentosEtiquetaFechaNcr($_POST['fechaCorte'], 5); // se obtiene la informacion de NCR
$totalAumentoCartera = 0;
$totalDisminucionCartera = 0;
if ($documentos) {
// ser recorre todos los datos relacionados con los clientes etiquetados como mayoristas para obtener los ids y el valor total
    foreach ($documentos as $documento) {
        $arIdsFiles[] = $documento->fi_id;
        $arSuma += $documento->fi_neto;
    }
//se recorre todos los ids relacionados con las notas de credito para sumar sus valores.
    foreach ($documentosNCRs as $documentosNCR) {
        $arIdsNCR[] = $documentosNCR->fi_id;
        $arIdsNCRSum += $documentosNCR->fi_neto;
    }
    $compensaciones = DeudasData::getByDocsIds(implode(',', $arIdsFiles)); // se obtiene el valor de las compensacion
    ///// SE OBTIENE LOS COBROS DE RELACIONADOS CON LA FECHA A BUSCAR
    $cobros = DeudasData::getByDocsIdsCobros(implode(',', $arIdsFiles));


    $valorCobros = 0;
    $valorComp = 0;
    if (is_null($cobros->cdvalor)) {
        $valorCobros = $cobros->cdvalor;
    }
// se toma los valores desde la tabla de compensaciones deudas
    $valorContado = $valorCobros + $valorComp;
    $valorCredito = $arSuma - $valorContado;
    $totalGneral = 0;
    $valorNCR = $arIdsNCRSum; // se suma los valores de las notas de credito
    $totalGneral = ($valorContado + $valorCredito) - $valorNCR;
    $totalAumentoCartera += $valorContado;
    $totalAumentoCartera += $valorCredito;
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(140, 6, 'FRANQUICIADOS', '', 1, 'L', 0, 0);
    $pdf->SetFont('Arial', '', 9); // titulos
    $pdf->Cell(50, 6, 'Ventas de contado', '', 0, 'L', 0, 0);
    $pdf->Cell(15, 6, number_format($valorContado, 2), "", 0, 'R', 0, 0);
    $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
    $pdf->Cell(50, 6, 'Ventas de credito', '', 0, 'L', 0, 0);
    $pdf->Cell(15, 6, number_format($valorCredito, 2), "", 0, 'R', 0, 0);
    $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
    $pdf->Cell(50, 6, 'NCR (-)', '', 0, 'L', 0, 0);
    $pdf->Cell(15, 6, number_format($valorNCR, 2), "", 0, 'R', 0, 0);
    $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
    $pdf->Cell(20, 6, '', '', 0, 'R', 0, 0);
    $pdf->Cell(30, 6, 'Total', '', 0, 'C', 0, 0);
    $pdf->Cell(15, 6, number_format($totalGneral, 2), "", 0, 'R', 0, 0);
    $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);

    $detalleFormas = CobrostData::getAllIdsDeudasForFormasPago($_POST['fechaCorte'], 5);
    $totalCobros = 0;
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, "Cobros", '', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 9); // titulos
    foreach ($detalleFormas as $detalleForma) {
        $pdf->Cell(100, 6, $detalleForma->cfname, '', 0, 'R', 0, 0);
        $pdf->Cell(16, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, $detalleForma->total, "", 1, 'R', 0, 0);
        $totalCobros += $detalleForma->total;
    }
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
    $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($totalCobros, 2), "", 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 9); // titulos
    $totalDisminucionCartera += $totalCobros;

    ////////////// DETALLE DE ANTICIPOS
    $detalleFormas1 = DeudasData::getByDocsForAnticipo($_POST['fechaCorte'], 5);
    $totalAnticipos = 0;

    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, "Anticipos", '', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 8); // titulos
    foreach ($detalleFormas1 as $detalleForma1) {
        $pdf->Cell(50, 6, $detalleForma1->cfname, '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($detalleForma1->total, 2), "", 1, 'R', 0, 0);
        $totalAnticipos += $detalleForma1->total;
    }
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
    $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($totalAnticipos, 2), "", 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 9); // titulos

    $comp = CrucecabData::getAllSumFechaInforme($_POST['fechaCorte'], 5); // se obtiene el valor de los cobros
    $totaCompn = 0;
    if (is_null($comp->total)) {
        $totaCompn = $comp->total;
    }
    $totalCompensacion = 0;
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, "Compensaciones", '', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 8); // titulos
    if (!is_null($comp->total)) {
        $pdf->Cell(50, 6, 'Valor Compensado ', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($comp->total, 2), "", 1, 'R', 0, 0);
        $totalCompensacion += $comp->total;
    }
    $totalDisminucionCartera += $totalCompensacion;

    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
    $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($totalCompensacion, 2), "", 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 9); // titulos

    $pdf->Ln(5);
////--------------------------------------------------------------------------------------------------------------------

// 6  MAYORISTAS
    $documentosMayo = FilesData::getDocumentosEtiquetaFecha($_POST['fechaCorte'], 6); // se obtiene la informacion de documentos de venta
    $documentosNCRsMayo = FilesData::getDocumentosEtiquetaFechaNcr($_POST['fechaCorte'], 6); // se obtiene la informacion de NCR
//var_dump($documentos);
    if ($documentosMayo) {
// ser recorre todos los datos relacionados con los clientes etiquetados como mayoristas para obtener los ids y el valor total
        foreach ($documentosMayo as $documentoMayo) {
            $arIdsFilesMayo[] = $documentoMayo->fi_id;
            $arSumaMayo += $documentoMayo->fi_neto;
        }
//se recorre todos los ids relacionados con las notas de credito para sumar sus valores.
        foreach ($documentosNCRsMayo as $documentosNCRMayo) {
            $arIdsNCRMayo[] = $documentosNCR->fi_id;
            $arIdsNCRSumMayo += $documentosNCR->fi_neto;
        }
        $compensacionesMayo = DeudasData::getByDocsIds(implode(',', $arIdsFilesMayo)); // se obtiene el valor de las compensacion
        ///// SE OBTIENE LOS COBROS DE RELACIONADOS CON LA FECHA A BUSCAR
        $cobrosMayo = DeudasData::getByDocsIdsCobros(implode(',', $arIdsFilesMayo));


        $valorCobrosMayo = 0;
        $valorCompMayo = 0;
        if (is_null($cobros->cdvalor)) {
            $valorCobrosMayo = $cobros->cdvalor;
        }
// se toma los valores desde la tabla de compensaciones deudas
        $valorContadoMayo = $valorCobrosMayo + $valorCompMayo;
        $valorCreditoMayo = $arSumaMayo - $valorContadoMayo;
        $totalGneralMayo = 0;
        $valorNCRMayo = $arIdsNCRSumMayo; // se suma los valores de las notas de credito
        $totalGneralMayo = ($valorContadoMayo + $valorCreditoMayo) - $valorNCRMayo;
        $totalAumentoCartera += $valorContadoMayo;
        $totalAumentoCartera += $valorCreditoMayo;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(140, 6, 'MAYORISTAS', '', 1, 'L', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $pdf->Cell(50, 6, 'Ventas de contado', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorContadoMayo, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(50, 6, 'Ventas de credito', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorCreditoMayo, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(50, 6, 'NCR (-)', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorNCRMayo, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(20, 6, '', '', 0, 'R', 0, 0);
        $pdf->Cell(30, 6, 'Total', '', 0, 'C', 0, 0);
        $pdf->Cell(15, 6, number_format($totalGneralMayo, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);

        $detalleFormasMayo = CobrostData::getAllIdsDeudasForFormasPago($_POST['fechaCorte'], 6);
        $totalCobrosMayo = 0;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Cobros", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        foreach ($detalleFormasMayo as $detalleForma) {
            $pdf->Cell(100, 6, $detalleForma->cfname, '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, $detalleForma->total, "", 1, 'R', 0, 0);
            $totalCobrosMayo += $detalleForma->total;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalCobrosMayo, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $totalDisminucionCartera += $totalCobrosMayo;
        ////////////// DETALLE DE ANTICIPOS
        $detalleFormas1Mayo = DeudasData::getByDocsForAnticipo($_POST['fechaCorte'], 6);
        $totalAnticiposMayo = 0;

        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Anticipos", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 8); // titulos
        foreach ($detalleFormas1Mayo as $detalleForma1) {
            $pdf->Cell(50, 6, $detalleForma1->cfname, '', 0, 'R', 0, 0);
            $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, number_format($detalleForma1->total, 2), "", 1, 'R', 0, 0);
            $totalAnticiposMayo += $detalleForma1->total;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalAnticiposMayo, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos

        $compMayo = CrucecabData::getAllSumFechaInforme($_POST['fechaCorte'], 6); // se obtiene el valor de los cobros
        $totaCompnMayo = 0;
        if (is_null($compMayo->total)) {
            $totaCompnMayo = $compMayo->total;
        }
        $totalCompensacionMayo = 0;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Compensaciones", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 8); // titulos
        if (!is_null($compMayo->total)) {
            $pdf->Cell(50, 6, 'Valor Compensado ', '', 0, 'R', 0, 0);
            $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, number_format($compMayo->total, 2), "", 1, 'R', 0, 0);
            $totalCompensacionMayo += $compMayo->total;
        }
        $totalDisminucionCartera += $totalCompensacionMayo;

        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalCompensacionMayo, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos

        $pdf->Ln(5);
    }
////--------------------------------------------------------------------------------------------------------------------

// 7  INSTITUCIONALES
    $documentosInst = FilesData::getDocumentosEtiquetaFecha($_POST['fechaCorte'], 7); // se obtiene la informacion de documentos de venta
    $documentosNCRsInst = FilesData::getDocumentosEtiquetaFechaNcr($_POST['fechaCorte'], 7); // se obtiene la informacion de NCR
//var_dump($documentos);
    if ($documentosInst) {
// ser recorre todos los datos relacionados con los clientes etiquetados como mayoristas para obtener los ids y el valor total
        foreach ($documentosInst as $documentoInst) {
            $arIdsFilesInst[] = $documentoInst->fi_id;
            $arSumaInst += $documentoInst->fi_neto;
        }
//se recorre todos los ids relacionados con las notas de credito para sumar sus valores.
        foreach ($documentosNCRsInst as $documentosNCRInst) {
            $arIdsNCRInst[] = $documentosNCR->fi_id;
            $arIdsNCRSumInst += $documentosNCR->fi_neto;
        }
        $compensacionesMayo = DeudasData::getByDocsIds(implode(',', $arIdsFilesInst)); // se obtiene el valor de las compensacion
        ///// SE OBTIENE LOS COBROS DE RELACIONADOS CON LA FECHA A BUSCAR
        $cobrosInst = DeudasData::getByDocsIdsCobros(implode(',', $arIdsFilesInst));


        $valorCobrosInst = 0;
        $valorCompInst = 0;
        if (is_null($cobrosInst->cdvalor)) {
            $valorCobrosMayo = $cobrosInst->cdvalor;
        }
// se toma los valores desde la tabla de compensaciones deudas
        $valorContadoInst = $valorCobrosInst + $valorCompInst;
        $valorCreditoInst = $arSumaInst - $valorContadoInst;
        $totalGneralInst = 0;
        $valorNCRInst = $arIdsNCRSumInst; // se suma los valores de las notas de credito
        $totalGneralInst = ($valorContadoInst + $valorCreditoInst) - $valorNCRInst;
        $totalAumentoCartera += $valorContadoInst;
        $totalAumentoCartera += $valorCreditoInst;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(140, 6, 'INSTITUCIONES', '', 1, 'L', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $pdf->Cell(50, 6, 'Ventas de contado', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorContadoInst, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(50, 6, 'Ventas de credito', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorCreditoInst, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(50, 6, 'NCR (-)', '', 0, 'L', 0, 0);
        $pdf->Cell(15, 6, number_format($valorNCRInst, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);
        $pdf->Cell(20, 6, '', '', 0, 'R', 0, 0);
        $pdf->Cell(30, 6, 'Total', '', 0, 'C', 0, 0);
        $pdf->Cell(15, 6, number_format($totalGneralInst, 2), "", 0, 'R', 0, 0);
        $pdf->Cell(15, 6, '', "", 1, 'C', 0, 0);

        $detalleFormasInst = CobrostData::getAllIdsDeudasForFormasPago($_POST['fechaCorte'], 6);
        $totalCobrosInst = 0;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Cobros", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        foreach ($detalleFormasInst as $detalleForma) {
            $pdf->Cell(100, 6, $detalleForma->cfname, '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, $detalleForma->total, "", 1, 'R', 0, 0);
            $totalCobrosInst += $detalleForma->total;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalCobrosInst, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $totalDisminucionCartera += $totalCobrosInst;

        ////////////// DETALLE DE ANTICIPOS
        $detalleFormas1Inst = DeudasData::getByDocsForAnticipo($_POST['fechaCorte'], 6);
        $totalAnticiposInst = 0;

        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Anticipos", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 8); // titulos
        foreach ($detalleFormas1Inst as $detalleForma1) {
            $pdf->Cell(50, 6, $detalleForma1->cfname, '', 0, 'R', 0, 0);
            $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, number_format($detalleForma1->total, 2), "", 1, 'R', 0, 0);
            $totalAnticiposInst += $detalleForma1->total;
        }

        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalAnticiposInst, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos
        $totalCobrosMayo += $totalAnticiposInst;

        $compInst = CrucecabData::getAllSumFechaInforme($_POST['fechaCorte'], 6); // se obtiene el valor de los cobros
        $totaCompnInst = 0;
        if (is_null($compInst->total)) {
            $totaCompnInst = $compInst->total;
        }
        $totalCompensacionInst = 0;
        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, "Compensaciones", '', 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 8); // titulos
        if (!is_null($compInst->total)) {
            $pdf->Cell(50, 6, 'Valor Compensado ', '', 0, 'R', 0, 0);
            $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
            $pdf->Cell(16, 6, number_format($compInst->total, 2), "", 1, 'R', 0, 0);
            $totalCompensacionInst += $compInst->total;
        }
        $totalDisminucionCartera += $totalCompensacionInst;

        $pdf->SetFont('Arial', 'B', 9); // titulos
        $pdf->Cell(50, 6, 'Totales', '', 0, 'R', 0, 0);
        $pdf->Cell(10, 6, '', "", 0, 'R', 0, 0);
        $pdf->Cell(16, 6, number_format($totalCompensacionInst, 2), "", 1, 'R', 0, 0);
        $pdf->SetFont('Arial', '', 9); // titulos

        $pdf->Ln(5);
    }
    // CUADRO DE BALANCEO DE VOUCHERS
//se resta 1 día
    $fechaCorte = date("Y-m-d", strtotime($_POST['fechaCorte'] . "- 1 days"));
    $where = 'where a.deestado = 1 and a.defecha between "2000-01-01" and "' . $fechaCorte . '"';
    $saldoDocumentos = DeudasData::getByDataAllFechasVista($where, $fechaCorte);
    foreach ($saldoDocumentos as $saldoDocumento) {
        $totTotal[] = $saldoDocumento->detotal;
        $totAbono[] = $saldoDocumento->deabono;
        $totCompensa[] = $saldoDocumento->decopensa;
    }
    $saldoAnterior = array_sum($totCompensa);

    $pdf->SetFont('Arial', 'B', 8); // titulos
    $pdf->Cell(50, 6, 'BALANCEO DE CARTERA ', '', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 8); // titulos
    $pdf->Cell(50, 6, 'Saldo anterior', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($saldoAnterior, 2), "", 1, 'R', 0, 0);
    $pdf->Cell(50, 6, 'Incremento de cartera', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($totalAumentoCartera, 2), "", 1, 'R', 0, 0);
    $pdf->Cell(50, 6, utf8_decode('Disminución de cartera'), "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format($totalDisminucionCartera, 2), "", 1, 'R', 0, 0);
    $pdf->Cell(50, 6, 'Saldo final de cartera', "", 0, 'R', 0, 0);
    $pdf->Cell(16, 6, number_format(($saldoAnterior + $totalAumentoCartera) - $totalDisminucionCartera, 2), "", 1, 'R', 0, 0);
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Output();

} else {
    print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
}
