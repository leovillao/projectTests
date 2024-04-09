<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/vWFormasCobrosDetalleData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];
$GLOBALS['desde'] = $_POST['desde'];
$GLOBALS['hasta'] = $_POST['hasta'];
$GLOBALS['sucursal'] = $_POST['sucursal'];

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . "/{nb}", 0, 1, 'R', 0, 0);
//    $this->Cell(193,4,'Pagina ' .$this->PageNo(),0,1,'R',0,0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
        $this->SetFont('Arial', '', 8); // titulos
        if ($GLOBALS['sucursal'] != 0) {
            $sucursal = "Sucursal : " . SucursalData::getById($GLOBALS['sucursal'])->suname;
        }
        $this->Cell(100, 4,  $sucursal , 0, 0, 'L', 0, 0);
        $this->Cell(90, 4, 'RANGO DE FECHA : ' . $GLOBALS['desde'] . ' HASTA ' . $GLOBALS['hasta'], 0, 1, 'C', 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 7); // titulos
        $this->SetFillColor(160, 160, 160);
        $this->Cell(10, 6, 'Num ', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(20, 6, 'Tipo Anticipo', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(20, 6, 'Fecha', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(119, 6, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(21, 6, 'Valor', 'T,B,L,R', 1, 'R', 0, 1);

    }
}

$fuentes = FData::fuentesPdf();

$where = " where DATE(anfecha) >= '" . $_POST['desde'] . "' and DATE(anfecha) <= '" . $_POST['hasta'] . "'  ";

if ($_POST['cliente'] != 0) {
    $where .= " and ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= 'and suid = ' . $_POST['sucursal'] . " ";
}
if ($_POST['etiquetac'] != 0) { // zoid
    $where .= ' and idetiqueta = ' . $_POST['etiquetac'];
}
if($_POST['tipoAnticipo'] != 0){
    $where .= " and taid = ". $_POST['tipoAnticipo'];
}
$cfid = 0;
$where .= " and anestado = 1 order by cfname,etiqueta,anid";


$anticipos = vWFormasCobrosDetalleData::getAllFechas($where);

$pdf = new PDF();
$pdf->AliasNbPages();
$netoEtiqueta = 0;
$totalNetoEtiqueta = 0;
$totalivasEtiqueta = 0;
if (count($anticipos) > 0) {
    foreach ($anticipos as $cobro) {
        // saldo es mayor a 0
        if ($controw == 0) {
            $pdf->AddPage();
        }

        if ($cfid !== $cobro->cfid) {
            $pdf->SetFont('Arial', 'B', 10); // titulos
            if ($netoD != 0) {
                $pdf->Cell(169, 6, utf8_decode("Total " . $formaPago . " :   "), '', 0, 'R', 0, 0);
                $pdf->Cell(21, 6, "$ ".FData::formatoNumeroReportes($netoD), '', 1, 'R', 0, 0);
                $netoD = 0;
                $totalNetoD = 0;
                $totalivasD = 0;
                $controw++;
            }
            $cfid = $cobro->cfid;
            $pdf->Cell(30, 6, utf8_decode("Forma pago : " . $cobro->cfname), '', 1, 'L', 0, 0);
            $tdid = 0;
            $controw++;
        }

        if ($cobro->idetiqueta != $idetiqueta) {
            $pdf->SetFont('Arial', 'B', 9); // titulos
            if ($netoEtiqueta != 0) {
                $pdf->Cell(143, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($netoEtiqueta)), '', 0, 'R', 0, 0);
                $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalNetoEtiqueta), '', 0, 'R', 0, 0);
                $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalivasEtiqueta), '', 1, 'R', 0, 0);
                $netoEtiqueta = 0;
                $totalNetoEtiqueta = 0;
                $totalivasEtiqueta = 0;
                $controw++;
            }
            $idetiqueta = $cobro->idetiqueta;
            $pdf->SetFont('Arial', 'B', 9); // titulos
            $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
            $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $cobro->etiqueta), '', 1, 'L', 0, 0);
            $tdid = 0;
            $controw++;
        }

        $etiqueta = $cobro->etiqueta;
        $formaPago = $cobro->cfname;

        $pdf->SetFont('Arial', '', 7); // titulos
        $pdf->Cell(10, 6, $cobro->anid, '', 0, 'C', 0, 1);
        $pdf->Cell(20, 6, $cobro->tcdescrip, '', 0, 'C', 0, 1);
        $pdf->Cell(20, 6, $cobro->anfecha, '', 0, 'C', 0, 1);
        $pdf->Cell(119, 6, $cobro->cename, '', 0, 'L', 0, 1);
        $pdf->Cell(21, 6, '$ ' . FData::formatoNumeroReportes($cobro->afvalor), '', 1, 'R', 0, 1);
        $pdf->SetFont('Arial', '', 7); // titulos

        $netoD += $cobro->afvalor;
        $netoG += $cobro->afvalor;

        $controw++;
        if ($controw >= 37) {
            $controw = 0;
        }
    }
    $pdf->SetFont('Arial', 'B', 10); // titulos
    if ($netoD != 0) {
        $pdf->Cell(169, 6, utf8_decode("Total " . $formaPago . " :   "), '', 0, 'R', 0, 0);
        $pdf->Cell(21, 6, "$ ".FData::formatoNumeroReportes($netoD), '', 1, 'R', 0, 0);
        $netoD = 0;
        $totalNetoD = 0;
        $totalivasD = 0;
        $controw++;
    }
    if ($netoEtiqueta != 0) {
        $pdf->Cell(169, 6, utf8_decode("Total " . $etiqueta . " :   "), '', 0, 'R', 0, 0);
        $pdf->Cell(21, 6, "$ ".FData::formatoNumeroReportes($netoEtiqueta), '', 1, 'R', 0, 0);
        $netoEtiqueta = 0;
        $totalNetoEtiqueta = 0;
        $totalivasEtiqueta = 0;
        $controw++;
    }

    $pdf->Cell(169, 6, utf8_decode("Total General : "), '', 0, 'R', 0, 0);
    $pdf->Cell(21, 6, "$ ".FData::formatoNumeroReportes($netoG), '', 1, 'R', 0, 0);
    $controw++;
}

$pdf->Output();
