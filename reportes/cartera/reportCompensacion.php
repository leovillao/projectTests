<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/ProveeData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/TipoOperationData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];
$GLOBALS['hasta'] = $_POST['hasta'];
$GLOBALS['desde'] = $_POST['desde'];

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 5, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->SetFont('Arial', '', 11); // titulos
        $this->Cell(95, 5, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(190, 5, 'Fecha Desde :' . $GLOBALS['desde'] . " , Hasta : " . $GLOBALS['hasta'], '', 0, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
    }
}

$where = 'where a.crfecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if (isset($_POST['cliente']) && !empty($_POST['cliente'])) { // proveedores
    $where .= ' and a.ceid = ' . $_POST['cliente'];
}

if (isset($_POST['sucursal']) && !empty($_POST['sucursal'])) { // proveedores
    $where .= ' and a.suid = ' . $_POST['sucursal'];
}

if ($_POST['etiquetac'] != 0) {
    $where .= " and setq_id  = " . $_POST['etiquetac'] . " ";
}


$compensaciones = CrucecabData::getAllFechasCab($where);

if ($compensaciones) {
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->Ln(1);
//    $pdf->SetFont('Arial', '', 10); // titulos

    $pdf->SetFont('Arial', '', 12); // titulos
    $pdf->Cell(190, 5, 'Detallado', 0, 1, 'C', 0, 0);

    $pdf->SetFont('Arial', '', 10); // titulos
    $documento = 0;
    $document = 0;
    $documentoe = 0;
    $idAnticipo = 0;

    $alto = 5;
    $altoTitulo = 5;
    $total = 0;
    $totalDocumento = 0;
    $pdf->SetFont('Arial', '', 7); // titulos
    foreach ($compensaciones as $venta) {
        if ($venta->crestado == 1) {
            if ($documento != $venta->crid) {
                if ($total > 0) {
                    $pdf->Cell(86, $alto, '', 'L,T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, 'TOTAL : ', 'T,B,R', 0, 'R', 0, 0);
                    $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($total), 'T,B,R', 1, 'R', 0, 0);
                    $total = 0;
                    $pdf->Ln(2);
                    $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($venta->user_id)->name . ' ' . UserData::getById($venta->user_id)->lastname, '', 0, 'L', 0, 0);
                    $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $venta->opcreate_at, '', 1, 'R', 0, 0);
                }

                if ($documento != 0) {
                    $pdf->Ln(15);
                }
                $pdf->SetFont('Arial', 'B', 8); // titulos
                $pdf->Cell(20, $altoTitulo, 'CLIENTE : ', 0, 0, 'L', 0, 0);
                $pdf->SetFont('Arial', '', 8); // titulos
                $pdf->Cell(90, $altoTitulo, strtoupper(utf8_decode($venta->cename)), 0, 1, 'L', 0, 0);
                $pdf->SetFont('Arial', 'B', 8); // titulos

                $pdf->Cell(30, $altoTitulo, utf8_decode('COMPENSACIÓN : '), 0, 0, 'L', 0, 0);
                $pdf->SetFont('Arial', '', 8); // titulos
                $pdf->Cell(90, $altoTitulo, $venta->crid, 0, 1, 'L', 0, 0);
                $pdf->SetFont('Arial', 'B', 8); // titulos
                $pdf->Cell(20, $altoTitulo, 'COMENTARIO : ', 0, 0, 'L', 0, 0);
                $pdf->SetFont('Arial', '', 8); // titulos
                $pdf->Cell(90, $altoTitulo, $venta->crcomenta, 0, 1, 'L', 0, 0);
                $pdf->SetFont('Arial', 'B', 8); // titulos
                $pdf->Cell(20, $altoTitulo, 'FECHA : ', 0, 0, 'L', 0, 0);
                $pdf->SetFont('Arial', '', 8); // titulos
                $pdf->Cell(90, $altoTitulo, $venta->crfecha, 0, 1, 'L', 0, 0);

                $pdf->SetFont('Arial', '', 7); // titulos

                $documento = $venta->crid;
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(25, 5, "No.Ant", 'L,T,B,R', 0, 'L', 1, 0);
                $pdf->Cell(30, 5, "No.Deuda", 'L,T,B,R', 0, 'L', 1, 0);
                $pdf->Cell(52, 5, "Fecha", 'L,T,B,R', 0, 'C', 1, 0);
                $pdf->Cell(26, 5, "Documento", 'L,T,B,R', 0, 'C', 1, 0);
                $pdf->Cell(26, 5, "Valor Anticipo", 'L,T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(26, 5, "Valor Deuda", 'L,T,B,R', 1, 'R', 1, 0);
                $detalles = CrucecabData::getAllFechasDet($venta->crid);
                $detallesAnticipos = CrucecabData::getAllFechasDetAnticipos($venta->crid);

                $totalCa = 0;
                $totalCaAnid = 0;
                $ttotalAnt = 0;
                $ttotalDeuda = 0;
                foreach ($detallesAnticipos as $detallesAnt) {
                        $pdf->Cell(25, $alto, $detallesAnt->anid, 'L,T,B,R', 0, 'L', 0, 0);
                        $pdf->Cell(30, $alto, '', 'L,T,B,R', 0, 'L', 0, 0);
                        $pdf->Cell(52, $alto, '', 'L,T,B,R', 0, 'C', 0, 0);
                        $pdf->Cell(26, $alto, '', 'L,T,B,R', 0, 'R', 0, 0);
                        $pdf->Cell(26, $alto, FData::formatoNumeroReportes($detallesAnt->cavalor), 'L,T,B,R', 0, 'R', 0, 0);
                        $pdf->Cell(26, $alto, '', 'L,T,B,R', 1, 'R', 0, 0);
                        $totalCa = $detallesAnt->anid;
                        $ttotalAnt += $detallesAnt->cavalor;
                }
                foreach ($detalles as $detalle) {
                        $pdf->Cell(25, $alto, '', 'L,T,B,R', 0, 'L', 0, 0);
                        $pdf->Cell(30, $alto, $detalle->deid, 'L,T,B,R', 0, 'L', 0, 0);
                        $pdf->Cell(52, $alto, $detalle->defecha, 'L,T,B,R', 0, 'C', 0, 0);
                        $pdf->Cell(26, $alto, $detalle->derefer, 'L,T,B,R', 0, 'C', 0, 0);
                        $pdf->Cell(26, $alto, ($totalCa == 0) ? FData::formatoNumeroReportes($detalle->cavalor) : '', 'L,T,B,R', 0, 'R', 0, 0);
                        $pdf->Cell(26, $alto, FData::formatoNumeroReportes($detalle->cdvalor), 'L,T,B,R', 1, 'R', 0, 0);
                        $totalCaAnid = $detalle->cavalor;
                        $ttotalDeuda += $detalle->cdvalor;
                }
                $pdf->Cell(25, $alto, '', 'L,T,B', 0, 'L', 0, 0);
                $pdf->Cell(30, $alto, '', 'T,B', 0, 'L', 0, 0);
                $pdf->Cell(52, $alto, '', 'T,B', 0, 'C', 0, 0);
                $pdf->SetFont('Arial', 'B', 7); // titulos
                $pdf->Cell(26, $alto, 'Totales : ', 'T,B,R', 0, 'C', 0, 0);
                $pdf->SetFont('Arial', '', 7); // titulos
                $pdf->Cell(26, $alto, FData::formatoNumeroReportes($ttotalAnt), 'L,T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(26, $alto, FData::formatoNumeroReportes($ttotalDeuda), 'L,T,B,R', 1, 'R', 0, 0);
                $totalesGeneralesDeuda += $ttotalDeuda;
                $totalesGeneralesAnt += $ttotalAnt;
            }
        }
    }
    $pdf->Cell(25, $alto, '', 'L,T,B', 0, 'L', 0, 0);
    $pdf->Cell(30, $alto, '', 'T,B', 0, 'L', 0, 0);
    $pdf->Cell(52, $alto, '', 'T,B', 0, 'C', 0, 0);
    $pdf->SetFont('Arial', 'B', 7); // titulos
    $pdf->Cell(26, $alto, 'Totales Generales : ', 'T,B,R', 0, 'C', 0, 0);
    $pdf->SetFont('Arial', '', 7); // titulos
    $pdf->Cell(26, $alto, FData::formatoNumeroReportes($totalesGeneralesDeuda), 'L,T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(26, $alto, FData::formatoNumeroReportes($totalesGeneralesAnt), 'L,T,B,R', 1, 'R', 0, 0);
    $pdf->Output();
} else {
    echo '<script>
     var opcion = confirm("No existe información para los criterios seleccionados");
        if (opcion == true) {
            window.close();
        } else {
            window.close();
        }
        </script>';
}
