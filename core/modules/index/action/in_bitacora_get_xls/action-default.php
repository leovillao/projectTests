<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$spreadsheet->getActiveSheet()->mergeCells('B1:E1');
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setCellValue('B1', 'CONTROL DE ARCHIVOS EN REPOSITORIOS');
$activeWorksheet->setCellValue('B2', 'Emision');
$activeWorksheet->setCellValue('C2', date('d/m/Y'));
$activeWorksheet->setCellValue('B3', 'Proyecto');
$activeWorksheet->setCellValue('C3', 'Smarttag');

$activeWorksheet->setCellValue('A6', 'No.');
$activeWorksheet->setCellValue('B6', 'Usuario');
$activeWorksheet->setCellValue('C6', 'Fecha');
$activeWorksheet->setCellValue('D6', 'Accion');
$activeWorksheet->setCellValue('E6', 'Pagina');

$where = ' where date(bicreate_at) between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '" ';

if (!empty($_POST['usuario'])) {
    $where .= ' and user_id = ' . $_POST['usuario'];
}
$bicatoras = BitacoraData::getAllForWhere($where);
$rowCount = 7;
$count = 0;
foreach ($bicatoras as $bitacora) {
    $activeWorksheet->setCellValueByColumnAndRow(1, $rowCount, $count++);
    $activeWorksheet->setCellValueByColumnAndRow(2, $rowCount, UserData::getById($bitacora->user_id)->usr_nombre);
    $activeWorksheet->setCellValueByColumnAndRow(3, $rowCount, $bitacora->bicreate_at);
    $activeWorksheet->setCellValueByColumnAndRow(4, $rowCount, $bitacora->biaccion);
    $activeWorksheet->setCellValueByColumnAndRow(5, $rowCount, $bitacora->bipage);
    $rowCount++;
}
$fileName = "Descarga_excel.xlsx";
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');
