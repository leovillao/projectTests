<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/DeudasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/VendedorData.php';
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
        $this->SetFont('Arial', '', 11); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
    }
}


$where = 'where a.defecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'];
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= 'and a.suid = ' . $_POST['sucursal'];
}
if ($_POST['zona'] != 0) { // zona
    $where .= 'and a.zoid = ' . $_POST['zona'];
}
if ($_POST['tipoDocumento'] != 0) { // zoid
    $where .= 'and a.tdid = ' . $_POST['tipoDocumento'];
}
if ($_POST['vendedor'] != 0) { // zoid
    $where .= 'and a.veid = ' . $_POST['vendedor'];
}
//if ($_POST['etiquetac'] != 0) { // etiqueta
//    $where .= ' and b.setq_id = ' . $_POST['etiquetac'];
//}
if ($_POST['etiquetac'] != 0) { // zoid
    $where .= ' and b.setq_id = ' . $_POST['etiquetac'];
}
$where .= " order by a.defecha,a.tdid,a.deid asc";
$cobros = DeudasData::getByDataAllFechas($where);
//echo json_encode($cobros)

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$altoTitulo = 8;
$alto = 6;
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8); // titulos
$pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
$pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
/**/
$nameSucursal = "";
$cobroTipo = 0;
$sucName = "";
$totalST = "";
/**/

$tiva = 0;
$tivan = 0;
$subt = 0;
$desc = 0;
$iva = 0;
$neto = 0;
/***/
$totalNeto = 0;
$totalIva = 0;
$totalDesc = 0;
$totalSubt = 0;
$totalivan = 0;
$totalivas = 0;
$idCobro = 0;
/***/
//c.tdid as tdid, // id del tipo de documento
//d.tdnombre as documento, // nombre del tipo de documento
foreach ($cobros as $cobro) {
    if (empty($nameSucursal)) {
        $nameSucursal = $cobro->tdnombre;
    }
    if ($cobro->defecha != $idCobro) {
        if ($totalFP > 0) {
            $pdf->Cell(190, 6, "Subtotal " . utf8_decode("dd") . " : $ " . $totalFP, 'T,B,R,L', 1, 'R', 1, 1);
            $totalFP = 0;
            $nmfp = "";
        }
        if ($totalNeto > 0) {
            $pdf->SetFillColor(231, 229, 229);
            $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
            $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
            $pdf->Cell(55, $alto, 'Subtotal ' . utf8_decode($nameSucursal), 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(20, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalNeto), 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(12, $alto, '', 'T,B,R', 1, 'R', 1, 0);

            $totalNeto = 0;
            $totalIva = 0;
            $totalDesc = 0;
            $totalSubt = 0;
            $totalivan = 0;
            $totalivas = 0;
        }

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 7); // titulos
        $pdf->Cell(189, $alto, utf8_decode("Tipo " . $nameSucursal), 'L,T,B,R', 1, 'L', 0, 0);
        $pdf->Cell(189, $alto, utf8_decode("TIPO DOCUMENTO :") . utf8_decode($nameSucursal), 'L,T,B,R', 1, 'L', 0, 0);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(15, 6, 'Fecha', 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(23, 6, 'Doc', 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(55, 6, 'Cliente', 'T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(20, 6, 'Documento', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, 6, 'Total', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, 6, 'Abono', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, 6, 'Saldo', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(16, 6, 'Fecha Venc', 'T,B,R', 0, 'C', 1, 0);
        $pdf->Cell(12, 6, 'Dias Venc', 'T,B,R', 1, 'C', 1, 0);
        //            $cobroTipo = $cobro->fecha;
        $nameSucursal = $cobro->tdnombre;
        $idCobro = $cobro->defecha;
    }

    if ($cobro->deestado != 3) {
        $pdf->SetTextColor(0, 0, 0); // color rojo
        $anulado = "";
    } else {
        $anulado = " - ANULADO";
        $pdf->SetTextColor(255, 8, 0); // color rojo
    }

    $pdf->SetFont('Arial', '', 7); // titulos
    $pdf->Cell(15, $alto, $cobro->defecha, 'L,T,B,R', 0, 'L', 0, 0);
    $pdf->Cell(23, $alto, $cobro->deid, 'L,T,B,R', 0, 'L', 0, 0);
    $pdf->Cell(55, $alto, substr(utf8_decode(ucwords(strtolower($cobro->cename))) . $anulado, 0, 46), 'T,B,R', 0, 'L', 0, 0);
    $pdf->SetFont('Arial', '', 6); // titulos
    $pdf->Cell(20, $alto, ($cobro->deestado != 0) ? $cobro->derefer : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 6); // titulos
    $pdf->Cell(16, $alto, ($cobro->deestado != 0) ? FData::formatoNumeroReportes($cobro->detotal) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($cobro->deestado != 0) ? FData::formatoNumeroReportes($cobro->deabono) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($cobro->deestado != 0) ? FData::formatoNumeroReportes($cobro->desaldo) : 0.00, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(16, $alto, ($cobro->deestado != 0) ? $cobro->devence : 0, 'T,B,R', 0, 'R', 0, 0);
    $pdf->Cell(12, $alto, ($cobro->desaldo != 0) ? $cobro->vencidos : 0, 'T,B,R', 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 7); // titulos

//    $totalST = $totalST + $cobro->fi_subtotal;

    $totalNeto = $totalNeto + $cobro->desaldo;
//    $totalIva = $totalIva + $cobro->fi_iva;
//    $totalDesc = $totalDesc + $cobro->fi_desc;
//    $totalSubt = $totalSubt + $cobro->fi_subtotal;
//    $totalivan = $totalivan + $cobro->fi_ivano;
    $totalivas = $totalivas + $cobro->desaldo;

//    $tiva = $tiva + $cobro->fi_ivasi;
//    $tivan = $tivan + $cobro->fi_ivano;
//    $subt = $subt + $cobro->fi_subtotal;
//    $desc = $desc + $cobro->fi_desc;
//    $iva = $iva + $cobro->fi_iva;
    $neto = $neto + $cobro->desaldo;
}

if ($totalNeto >= 0) {
    $pdf->SetFillColor(231, 229, 229);
    $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
    $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
    $pdf->Cell(55, $alto, 'SUBTOTAL TIPO : ' . utf8_decode($nameSucursal), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(20, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, FData::formatoNumeroReportes($totalNeto), 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
    $pdf->Cell(12, $alto, '', 'T,B,R', 1, 'R', 1, 0);
}
//160, 160, 160
$pdf->SetFillColor(160, 160, 160);
$pdf->SetTextColor(0, 0, 0); // color negro
$pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
$pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
$pdf->Cell(55, $alto, 'TOTAL TIPO ', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(20, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, '', 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, FData::formatoNumeroReportes($neto), 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(12, $alto, '', 'T,B,R', 1, 'R', 1, 0);
$pdf->Ln();


$pdf->Output();

//var_dump($_POST);
