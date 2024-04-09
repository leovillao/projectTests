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
//require '../../core/controller/Fpdf/fpdf.php';

if ($_POST['tipo'] == 1) {
    reporteVentas::reportForFechas($_POST['desde'], $_POST['hasta']);
} else {
    reporteVentas::getByFechaVentaProductos($_POST['desde'], $_POST['hasta']);
}

class reporteVentas
{

    public static function reportForFechas($desde, $hasta)
    {
        $ventas = FilesData::getByFechaVenta($desde, $hasta);
        require 'CabeceraReporte.php';

        $pdf = new PDF();

        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 9); // titulos
        $pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['desde'] . ', Hasta : ' . $_POST['hasta'], 0, 1, 'L', 0, 0);
        /*=================
        cabecera de reporte
        =================*/
        $pdf->SetFont('Arial', '', 9); // titulos
        $altoTitulo = 8;
        $pdf->Ln(4);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(55, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Desc', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(16, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 0, 0);
        $pdf->SetFont('Arial', '', 7); // titulos
        $alto = 6;
        $ttIvasi = 0;
        $ttivano = 0;
        $ttsubt = 0;
        $ttiva = 0;
        $tttotal = 0;
        foreach ($ventas as $venta) {
            if ($venta->fi_estado != 3) {
                $pdf->SetTextColor(0, 0, 0); // color rojo
                $anulado = "";
            } else {
                $anulado = " - ANULADO";
                $pdf->SetTextColor(255, 8, 0); // color rojo
            }
//        $pdf->setFillColor(230,230,230);
            $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(55, $alto, substr(utf8_decode($venta->fi_er_name) . $anulado,0,46), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivasi : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivano : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_subtotal : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_desc : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_iva : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_neto : 0.00, 'T,B,R', 1, 'R', 0, 0);
            if ($venta->fi_estado != 3) {
                $ttIvasi = $ttIvasi + $venta->fi_ivasi;
                $ttivano = $ttivano + $venta->fi_ivano;
                $ttsubt = $ttsubt + $venta->fi_subtotal;
                $ttiva = $ttiva + $venta->fi_iva;
                $tttotal = $tttotal + $venta->fi_neto;
            }
        }
        $pdf->SetTextColor(0, 0, 0); // color negro

        $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 0, 0);
        $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 0, 0);
        $pdf->Cell(50, $alto, 'Totales', 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(21, $alto, $ttIvasi, 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(21, $alto, $ttivano, 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(21, $alto, $ttsubt, 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(18, $alto, $ttiva, 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(21, $alto, $tttotal, 'T,B,R', 1, 'R', 0, 0);
//$pdf->MultiCell(190, 5, json_encode($ventas) , 0, 'C', 0, 1);
        $pdf->Output();

    }

    public static function getByFechaVentaProductos($desde, $hasta)
    {

        $ventas = FilesData::getDataProductFechas($desde, $hasta);

        require 'CabeceraReporte.php';

        $pdf = new PDF();

        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', '', 9); // titulos
        $pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['desde'] . ', Hasta : ' . $_POST['hasta'], 0, 1, 'L', 0, 0);
        /*=================
        cabecera de reporte
        =================*/
        $pdf->SetFont('Arial', '', 9); // titulos
        $altoTitulo = 8;
        $pdf->Ln(4);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(75, $altoTitulo, 'Producto', 'T,B,R', 0, 'L', 0, 0);
        $pdf->Cell(21, $altoTitulo, 'Cantidad', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(21, $altoTitulo, 'Precio', 'T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(21, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 0, 0);
        $alto = 6;
        $tttotal = 0;
        $ttpvp = 0;

        $pdf->SetFont('Arial', '', 7); // titulos
        foreach ($ventas as $venta) {
            $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(75, $alto, $venta->itname, 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(21, $alto, $venta->odcandig, 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $alto, $venta->odpvp, 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(21, $alto, $venta->odbruta, 'T,B,R', 1, 'C', 0, 0);

            $tttotal = $tttotal + $venta->odbruta;
            $ttpvp = $ttpvp + $venta->odpvp;

        }

        $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 0, 0);
        $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 0, 0);
        $pdf->SetFont('Arial', 'B', 10); // titulos
        $pdf->Cell(96, $alto, 'TOTALES : ', 'T,B', 0, 'R', 0, 0);
//    $pdf->Cell(21, $alto, '', 'T,B,R', 0, 'C', 0, 0);
        $pdf->SetFont('Arial', '', 7); // titulos
        $pdf->Cell(21, $alto, $ttpvp, 'L,T,B,R', 0, 'C', 0, 0);
        $pdf->Cell(21, $alto, $tttotal, 'T,B,R', 1, 'C', 0, 0);

        $pdf->Output();

    }

}

