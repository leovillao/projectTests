<?php
//var_export($_POST);
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/UnitData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/SaldosbodData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

$unidad = UnitData::getById($_POST['unidad']);
$prod = ProductData::getByItcodigo($_POST['codProducto']);
if ($_POST['bodega'] != 0) {
    $saldodBodegas = SaldosbodData::getByItIdParam($prod->itid, $_POST['desde'], $_POST['hasta'], $_POST['bodega']);
} else {
    $saldodBodegas = SaldosbodData::getByItIdParamSN($prod->itid, $_POST['desde'], $_POST['hasta']);
}

if ($saldodBodegas) {

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
            $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
            $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
            $this->SetFont('Arial', 'B', 17); // titulos
            $this->Cell(190, 7, 'INFORME KARDEX / UNIDADES', 0, 1, 'C', 0, 0);
        }
    }


    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial', '', 10); // titulos
//$pdf->Cell(190, 7, 'Fecha de Corte : ' . $_POST['fecha'], 0, 1, 'R', 0, 0);
    $pdf->SetFont('Arial', '', 12); // titulos
    $pdf->Cell(190, 7, "BODEGA : " . BodegasData::getById($_POST['bodega'])->bodescrip, 0, 1, 'L', 0, 0);
    $pdf->Cell(190, 7, "Fecha , Desde  : " . $_POST['desde'] . ' , Hasta : ' . $_POST['hasta'], 0, 1, 'L', 0, 0);
//$pdf->Cell(190, 7, "valor cero : " . $_POST['valorCero'], 0, 1, 'L', 0, 0);
    /*=================
    cabecera de reporte
    =================*/
    $pdf->SetFont('Arial', '', 9); // titulos
    $altoTitulo = 8;
    $pdf->Ln(5);
    $pdf->SetFillColor(192, 192, 192);
    $pdf->Cell(10, $altoTitulo, '#', 'L,T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, 'Fecha', 'T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, 'Bodega', 'T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, "Saldo Anterior", 'T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, 'Ingreso', 'T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, 'Egreso', 'T,B,R', 0, 'C', 0, 0);
    $pdf->Cell(30, $altoTitulo, 'Saldo', 'T,B,R', 1, 'C', 0, 0);
    $pdf->SetFont('Arial', '', 7); // titulos
    $alto = 6;
    $ri = 0;
/// ciclo del cuerpo del report
    foreach ($saldodBodegas as $saldodBodega) {
        $timestamp = strtotime($saldodBodega->fecha);
        $newDate = date("d-m-Y", $timestamp);

        $pdf->Cell(10, $alto, $ri + 1, 'L,T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(30, $alto, $newDate, 'T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(30, $alto, BodegasData::getById($saldodBodega->idbodega)->bodescrip, 'T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumero($rr), 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumero($saldodBodega->ingreso), 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumero($saldodBodega->egreso), 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(30, $alto, FData::formatoNumero($rr + $saldodBodega->ingreso - $saldodBodega->egreso), 'T,B,R', 1, 'R', 0, 0);
        //          "fecha" => $newDate,
//          "idbodega" => $saldodBodega->idbodega,
//          "bodega" => BodegasData::getById($saldodBodega->idbodega)->bodescrip,
//          "saldoanterior" => FData::formatoNumero($rr),
//          "ingreso" => FData::formatoNumero($saldodBodega->ingreso),
//          "egreso" => FData::formatoNumero($saldodBodega->egreso),
//          "saldo" => FData::formatoNumero($rr + $saldodBodega->ingreso - $saldodBodega->egreso),
        $ri++;
        $rr = $rr + $saldodBodega->ingreso - $saldodBodega->egreso;

    }
    $pdf->Output();

    class pdfReport
    {
        public static function reportByFechas($desde, $hasta)
        {
            $ventas = FilesData::getByFechaVenta($desde, $hasta);
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 9); // titulos
            $pdf->Cell(190, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 0, 1, 'R', 0, 0);
            $pdf->SetFont('Arial', 'B', 17); // titulos
            $pdf->Cell(190, 7, 'REPORTE DE VENTAS', 0, 1, 'C', 0, 0);
            $pdf->SetFont('Arial', '', 14); // titulos
            $pdf->Cell(190, 7, ($_POST['producto'] == 0) ? "PRODUCTO : TODOS" : 'PRODUCTO :' . ProductData::getById($_POST['producto']), 0, 1, 'L', 0, 0);
            $pdf->SetFont('Arial', '', 9); // titulos
            $pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['desde'] . ', Hasta : ' . $_POST['hasta'], 0, 1, 'L', 0, 0);
            /*=================
            cabecera de reporte
            =================*/
            $pdf->SetFont('Arial', '', 9); // titulos
            $altoTitulo = 8;
            $pdf->Ln(5);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(50, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(18, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 0, 0);
            $pdf->SetFont('Arial', '', 7); // titulos
            $alto = 6;
            $ttIvasi = 0;
            $ttivano = 0;
            $ttsubt = 0;
            $ttiva = 0;
            $tttotal = 0;
            foreach ($ventas as $venta) {
                $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(50, $alto, $venta->fi_er_name, 'T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_ivasi, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_ivano, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_subtotal, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(18, $alto, $venta->fi_iva, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_neto, 'T,B,R', 1, 'R', 0, 0);
                $ttIvasi = $ttIvasi + $venta->fi_ivasi;
                $ttivano = $ttivano + $venta->fi_ivano;
                $ttsubt = $ttsubt + $venta->fi_subtotal;
                $ttiva = $ttiva + $venta->fi_iva;
                $tttotal = $tttotal + $venta->fi_neto;
            }
            $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 0, 0);
            $pdf->Cell(50, $alto, 'Totales', 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttIvasi, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttivano, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttsubt, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(18, $alto, $ttiva, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $tttotal, 'T,B,R', 1, 'R', 0, 0);
            $pdf->Output();
        }

        public static function reportByFechasProduct($desde, $hasta)
        {

            $ventas = FilesData::getByFechaVentaProducts($desde, $hasta);
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 9); // titulos
            $pdf->Cell(190, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 0, 1, 'R', 0, 0);
            $pdf->SetFont('Arial', 'B', 17); // titulos
            $pdf->Cell(190, 7, 'REPORTE DE VENTAS', 0, 1, 'C', 0, 0);
            $pdf->SetFont('Arial', '', 14); // titulos
            $pdf->Cell(190, 7, ($_POST['producto'] == 0) ? "PRODUCTO : TODOS" : 'PRODUCTO :' . ProductData::getById($_POST['producto']), 0, 1, 'L', 0, 0);
            $pdf->SetFont('Arial', '', 9); // titulos
            $pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['desde'] . ', Hasta : ' . $_POST['hasta'], 0, 1, 'L', 0, 0);
            /*=================
            cabecera de reporte
            =================*/
            $pdf->SetFont('Arial', '', 9); // titulos
            $altoTitulo = 8;
            $pdf->Ln(5);
            $pdf->SetFillColor(192, 192, 192);
            $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(50, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(18, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 0, 0);
            $pdf->SetFont('Arial', '', 7); // titulos
            $alto = 6;
            $ttIvasi = 0;
            $ttivano = 0;
            $ttsubt = 0;
            $ttiva = 0;
            $tttotal = 0;
            foreach ($ventas as $venta) {
                $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(50, $alto, $venta->fi_er_name, 'T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_ivasi, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_ivano, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_subtotal, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(18, $alto, $venta->fi_iva, 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(21, $alto, $venta->fi_totaldoc, 'T,B,R', 1, 'R', 0, 0);
                $ttIvasi = $ttIvasi + $venta->fi_ivasi;
                $ttivano = $ttivano + $venta->fi_ivano;
                $ttsubt = $ttsubt + $venta->fi_subtotal;
                $ttiva = $ttiva + $venta->fi_iva;
                $tttotal = $tttotal + $venta->fi_totaldoc;
            }
            $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 0, 0);
            $pdf->Cell(50, $alto, 'Totales', 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttIvasi, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttivano, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $ttsubt, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(18, $alto, $ttiva, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(21, $alto, $tttotal, 'T,B,R', 1, 'R', 0, 0);
            $pdf->Output();
        }
    }
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