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
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/modules/index/model/SaldosbodData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
$unidad = UnitData::getById($_POST['unidad']);
$prod = ProductData::getByItcodigo($_POST['codProducto']);
//$prod = ProductData::getByItname($_POST['productTxt']);
if ($_POST['bodega'] != 0) {
    $saldodBodegas = SaldosbodData::getByItIdParam($prod->itid, $_POST['desde'], $_POST['hasta'], $_POST['bodega']);
} else {
    $saldodBodegas = SaldosbodData::getByItIdParamSN($prod->itid, $_POST['desde'], $_POST['hasta']);
}
if ($saldodBodegas) {

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
    $spread->getActiveSheet()->mergeCells('A2:H2');
    $spread->getActiveSheet()->mergeCells('A1:E1');
    $spread->getActiveSheet()->mergeCells('G1:J1');
    $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
    $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
    $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spread->getActiveSheet()->getStyle('C')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha , desde  : " . $_POST['desde'] . " , Hasta : " . $_POST['hasta'] . '.');
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, '#');
    $hoja->setCellValueByColumnAndRow(2, 3, 'Fecha');
    $hoja->setCellValueByColumnAndRow(3, 3, 'Bodega');
    $hoja->setCellValueByColumnAndRow(4, 3, 'Saldo anterior');
    $hoja->setCellValueByColumnAndRow(5, 3, 'Ingreso');
    $hoja->setCellValueByColumnAndRow(6, 3, 'Egreso');
    $hoja->setCellValueByColumnAndRow(7, 3, 'Saldo');
    $i = 4;
    $o = 0;
    foreach ($saldodBodegas as $saldodBodega) {
        $timestamp = strtotime($saldodBodega->fecha);
        $newDate = date("d-m-Y", $timestamp);
        $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
        $hoja->setCellValueByColumnAndRow(2, $i, $newDate);
//  $hoja->setCellValueByColumnAndRow(3, $i, $saldodBodega->idbodega);
        $hoja->setCellValueByColumnAndRow(3, $i, BodegasData::getById($saldodBodega->idbodega)->bodescrip);
        $hoja->setCellValueByColumnAndRow(4, $i, FData::formatoNumero($rr));
        $hoja->setCellValueByColumnAndRow(5, $i, FData::formatoNumero($saldodBodega->ingreso));
        $hoja->setCellValueByColumnAndRow(6, $i, FData::formatoNumero($saldodBodega->egreso));
        $hoja->setCellValueByColumnAndRow(7, $i, FData::formatoNumero($rr + $saldodBodega->ingreso - $saldodBodega->egreso));
        $i++;
        $o++;
        $rr = $rr + $saldodBodega->ingreso - $saldodBodega->egreso;

    }
    $fileName = "Informe_de_Saldos_SMARTTAG_BI.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');

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