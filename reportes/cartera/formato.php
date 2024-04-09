<?php
$spread = new Spreadsheet();
$spread
    ->getProperties()
    ->setCreator("SmartTag-Bi")
    ->setTitle('SmartTag-Bi')
    ->setSubject('Reporte de Cartera')
    ->setDescription('Reporte de Cartera')
    ->setKeywords('Informe de Cartera')
    ->setCategory('Excel');
$hoja = $spread->getActiveSheet();
$spread->getDefaultStyle()->getFont()->setName('Arial');
$spread->getDefaultStyle()->getFont()->setSize(8);
$spread->getActiveSheet()->getColumnDimension('A')->setWidth(17);
$spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$spread->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$spread->getActiveSheet()->getStyle('A4');
$spread->getActiveSheet()->mergeCells('A2:H2');
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
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
$hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
$hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
$hoja->setCellValueByColumnAndRow(1, 2, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta']);
$hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
/** ======================================*/
$hoja->setCellValueByColumnAndRow(1, 3, 'FORMA DE PAGO');
$hoja->setCellValueByColumnAndRow(2, 3, '');
$hoja->setCellValueByColumnAndRow(3, 3, 'TOTAL');
$i = 4;
$o = 0;
$totalDocumento = 0;
$contadorDocs = 0;
$totalResumido = 0;
foreach ($detalleFormasCobros as $pago) {
    $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
    $hoja->setCellValueByColumnAndRow(2, $i, utf8_decode(FormasData::getById($pago->cfid)->cfname));
    $hoja->setCellValueByColumnAndRow(3, $i, '');
    $hoja->setCellValueByColumnAndRow(4, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
    $hoja->setCellValueByColumnAndRow(5, $i, );
    $hoja->setCellValueByColumnAndRow(6, $i, 'Total ' . utf8_decode(FormasData::getById($pago->cfid)->cfname));
    $hoja->setCellValueByColumnAndRow(7, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
    $i++;
    $o++;
    $totalResumido = $totalResumido + $pago->fcvalor;

}
$hoja->setCellValueByColumnAndRow(2, $i, "Total de Documentos :$ " . number_format($totalResumido, 2, ',', '.'));

$fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
$writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
$writer->save('php://output');