<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('America/Guayaquil');
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/DeudasData.php';
require '../../core/modules/index/model/FData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/vwEstadoCuentaData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';

$where = 'where fecha between "' . $_POST['fechaDesde'] . '" and "' . $_POST['fechaHasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= ' and suid = ' . $_POST['sucursal'] . " ";
}

if ($_POST['orden'] == 2) {
    $where .= ' order by iddeuda,derefer asc ';
}else{
    $where .= ' order by fecha asc ';
}

$saldos = vwEstadoCuentaData::getAllFecha($where);

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
//$spread->getActiveSheet()->mergeCells('A2:B2');
//$spread->getActiveSheet()->mergeCells('C2:H2');
$spread->getActiveSheet()->mergeCells('A1:D1');
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
$spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spread->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spread->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
$hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
$hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
//$hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['ccdate']);
$hoja->setCellValueByColumnAndRow(4, 2, "Fechas , Desde : " . $_POST['fechaDesde'] . " Hasta : " . $_POST["fechaHasta"]);
$hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
/** ======================================*/
$hoja->setCellValueByColumnAndRow(1, 3, 'Fecha');
$hoja->setCellValueByColumnAndRow(2, 3, 'Tipo');
//$hoja->setCellValueByColumnAndRow(3, 3, 'Doc');
$hoja->setCellValueByColumnAndRow(3, 3, 'Tipo Doc');
$hoja->setCellValueByColumnAndRow(4, 3, 'Referencia');
$hoja->setCellValueByColumnAndRow(5, 3, 'Cuota');
$hoja->setCellValueByColumnAndRow(6, 3, 'Deudor');
$hoja->setCellValueByColumnAndRow(7, 3, 'Acreedor');
$hoja->setCellValueByColumnAndRow(8, 3, 'Saldo Acumulado');
$hoja->setCellValueByColumnAndRow(9, 3, 'Comentario');

$i = 4;
$o = 0;
$t = 1;
$saldoAcumulado = 0;
foreach ($saldos as $saldo) {
    $val = 0;
    $saldo1 = $saldo->factor * $saldo->valor;
    $saldoAcumulado += $saldo1;
    $deudor = 0;
    $acreedor = 0;
    if ($saldo->factor == "-1") {
        $acreedor = $saldo->valor;
    }else{
        $deudor = $saldo->valor;
    }
//    $pdf->Row(array($saldo->fecha,$saldo->tipocobro,$saldo->tipodeuda,$saldo->derefer,$saldo->decuota,$deudor,$acreedor,number_format($saldoAcumulado,2),$saldo->observa));

    $hoja->setCellValueByColumnAndRow(1, $i, $saldo->fecha);
    $hoja->setCellValueByColumnAndRow(2, $i, $saldo->tipocobro);
    $hoja->setCellValueByColumnAndRow(3, $i, $saldo->tipodeuda);
    $hoja->setCellValueByColumnAndRow(4, $i, $saldo->derefer);
    $hoja->setCellValueByColumnAndRow(5, $i, FData::formatoNumeroReportes($saldo->decuota));
    $hoja->setCellValueByColumnAndRow(6, $i, FData::formatoNumeroReportes($deudor));
    $hoja->setCellValueByColumnAndRow(7, $i, FData::formatoNumeroReportes($acreedor));
    $hoja->setCellValueByColumnAndRow(8, $i, FData::formatoNumeroReportes($saldoAcumulado));
    $hoja->setCellValueByColumnAndRow(9, $i, $saldo->observa);
    $i++;
    $o++;
}


$fileName = "InformeSaldoPorDocumentof1.xlsx";
# Crear un "escritor"
$writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
