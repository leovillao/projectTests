<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VwinfProductosData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';

if (Validacion::ValidaFecha($_POST['fechaDesde'], $_POST['fechaHasta']) != "") {
    echo Validacion::ValidaFecha($_POST['fechaDesde'], $_POST['fechaHasta']);
} else {

    $where = 'where fi_fechadoc between "' . $_POST['fechaDesde'] . '" and "' . $_POST['fechaHasta'] . '"';
    if ($_POST['secuencia'] != 0) { // pto de emision
        $sec = SecuenciaData::getById($_POST['secuencia']);
        $where .= 'and fi_codestab = "' . $sec->estab . '" and fi_ptoemi = "' . $sec->emision . '"';
    }
    $where .= " and fi_tipo = '01 '";
    if ($_POST['estado'] == 1) { // todos exepto anulados
        $where .= ' and fi_estado <> 3';
    } elseif ($_POST['estado'] == 2) { // solo anulados
        $where .= ' and fi_estado = 3';
    } // todos los documentos
    if (!empty($_POST['cierre'])) { // por cierre de caja
        $where .= ' and box_id = ' . $_POST['cierre'];
    }
    if ($_POST['cliente'] != 0) { // por cliente
        $where .= ' and ceid = ' . $_POST['cliente'];
    }
    if ($_POST['sucursal'] != 0) { // por sucursal
        $where .= ' and sucursal_id = ' . $_POST['sucursal'];
    }
    if ($_POST['vendedor'] != 0) { // por vendedor
        $where .= ' and veid = ' . $_POST['vendedor'];
    }
    if ($_POST['ciudad'] != 0) { // por ciudad
        $where .= ' and city_id = ' . $_POST['ciudad'];
    }
    if ($_POST['provincia'] != 0) { // por provincia
        $where .= ' and prov_id = ' . $_POST['provincia'];
    }
    if ($_POST['pais'] != 0) { // por pais
        $where .= ' and pais_id = ' . $_POST['pais'];
    }
    $ventas = VwinfProductosData::getDataDefault($where);
//    echo json_encode($ventas);
    $spread = new Spreadsheet();
    $spread
        ->getProperties()
        ->setCreator("SmartTag-Bi")
        //    ->setLastModifiedBy('BaulPHP')
        ->setTitle('SmartTag-Bi')
        ->setSubject('Reporte de venta')
        ->setDescription('Reporte de Venta')
        ->setKeywords('Informe de Ventas')
        ->setCategory('Excel');
    $hoja = $spread->getActiveSheet();
    $spread->getDefaultStyle()->getFont()->setName('Arial');
    $spread->getDefaultStyle()->getFont()->setSize(8);
    $spread->getActiveSheet()->getColumnDimension('A')->setWidth(17);
    $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $spread->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $spread->getActiveSheet()->getStyle('A4');
    $spread->getActiveSheet()->mergeCells('A2:E2');
    $spread->getActiveSheet()->mergeCells('G1:J1');
    $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
    $spread->getActiveSheet()->getStyle('A3:M3')->getFont()->setBold(true)->setSize(10);
    $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);

    $spread->getActiveSheet()->getStyle('C')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);

    $hoja->setTitle("Informe de Ventas"); // Titulo de la pagina
//    $hoja->setCellValueByColumnAndRow(1, 5, "Un valor en 1, 1");
    // TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Consulta , Desde : " . $_POST['fechaDesde'] . ", Hasta :" . $_POST['fechaHasta']);
    $hoja->setCellValueByColumnAndRow(6, 2, "Cierre # " . $_POST['cierre']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);

    /** ======================================*/
//  Fecha Doc Producto Unidad Cantidad Precio Subtotal Iva Total
    $hoja->setCellValueByColumnAndRow(1, 3, 'Categoria');
    $hoja->setCellValueByColumnAndRow(2, 3, 'Subcategoria');
    $hoja->setCellValueByColumnAndRow(3, 3, 'Fecha');
    $hoja->setCellValueByColumnAndRow(4, 3, 'Tipo');
    $hoja->setCellValueByColumnAndRow(5, 3, 'Documento');
    $hoja->setCellValueByColumnAndRow(6, 3, 'Código');
    $hoja->setCellValueByColumnAndRow(7, 3, 'Producto');
    $hoja->setCellValueByColumnAndRow(8, 3, 'Unidad');
    $hoja->setCellValueByColumnAndRow(9, 3, 'Cantidad');
    $hoja->setCellValueByColumnAndRow(10, 3, 'Precio');
    $hoja->setCellValueByColumnAndRow(11, 3, 'Subtotal');
    $hoja->setCellValueByColumnAndRow(12, 3, 'Iva');
    $hoja->setCellValueByColumnAndRow(13, 3, 'Total');
    $i = 4;
    foreach ($ventas as $venta) {

        $hoja->setCellValueByColumnAndRow(1, $i, $venta->ctdescription);
        $hoja->setCellValueByColumnAndRow(2, $i, $venta->ct2description);
        $hoja->setCellValueByColumnAndRow(3, $i, $venta->fi_fechadoc);
        $hoja->setCellValueByColumnAndRow(4, $i, Validacion::tipoDocumento($venta->fi_tipo));
        $hoja->setCellValueByColumnAndRow(5, $i, $venta->fi_docum);
        $hoja->setCellValueByColumnAndRow(6, $i, ProductData::getById($venta->itid)->itcodigo);
        $hoja->setCellValueByColumnAndRow(7, $i, ucwords(strtolower($venta->itname)));
        $hoja->setCellValueByColumnAndRow(8, $i, $venta->undescrip);
        $hoja->setCellValueByColumnAndRow(9, $i, $venta->odcandig);
        $hoja->setCellValueByColumnAndRow(10, $i, $venta->odpvp);
        $hoja->setCellValueByColumnAndRow(11, $i, $venta->odsubtotal);
        $hoja->setCellValueByColumnAndRow(12, $i, $venta->odiva);
        $hoja->setCellValueByColumnAndRow(13, $i, $venta->odtotal);

        $subt = $subt + $venta->odsubtotal;
        $iva = $iva + $venta->odiva;
        $neto = $neto + $venta->odtotal;
        $i++;

    }
    $hoja->setCellValueByColumnAndRow(1, $i, '');
    $hoja->setCellValueByColumnAndRow(2, $i, '');
    $hoja->setCellValueByColumnAndRow(3, $i, '');
    $hoja->setCellValueByColumnAndRow(4, $i, '');
    $hoja->setCellValueByColumnAndRow(5, $i, '');
    $hoja->setCellValueByColumnAndRow(6, $i, '');
    $hoja->setCellValueByColumnAndRow(7, $i, 'TOTALES : ');
    $hoja->setCellValueByColumnAndRow(8, $i, "");
    $hoja->setCellValueByColumnAndRow(9, $i, "");
    $hoja->setCellValueByColumnAndRow(10, $i, "");
    $hoja->setCellValueByColumnAndRow(11, $i, $subt);
    $hoja->setCellValueByColumnAndRow(12, $i, $iva);
    $hoja->setCellValueByColumnAndRow(13, $i, $neto);
    $fileName = "Informe_de_Ventas_Productos_Clasificacion_SMARTTAG_BI.xlsx";
    # Crear un "escritor"
    $writer = new Xlsx($spread);
    # Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');

}


class Validacion
{
    public static function ValidaFecha($fechaini, $fechahasta)
    {
        $t = '';
        if (empty($fechaini) && empty($fechahasta)) {
            $t = "Debe ingresar Rango de fecha valido";
        } elseif (empty($fechaini)) {
            $t = "Debe ingresar Fecha de inicio valido";
        } elseif (empty($fechahasta)) {
            $t = "Debe ingresar Fecha de Hasta valido";
        }
        return $t;
    }

    public static function tipoDocumento($tipo)
    {
        $array = array(
            "FACTURA" => "01",
            "NOTA DE CRÉDITO" => "04",
            "RETENCION" => "07"
        );
        $indice = array_search($tipo, $array, false);
        return $indice;
    }
}
