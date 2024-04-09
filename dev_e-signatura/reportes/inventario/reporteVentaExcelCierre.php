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
require '../../core/modules/index/model/VwinfventasData.php';
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
  $ventas = VwinfventasData::getDataDefault($where);
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
  $spread->getActiveSheet()->mergeCells('A2:H2');
  $spread->getActiveSheet()->mergeCells('G1:J1');
  $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
  $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
  $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
  $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
  $spread->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);

  $spread->getActiveSheet()->getStyle('C')
          ->getNumberFormat()
          ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);

  $hoja->setTitle("Informe de Ventas"); // Titulo de la pagina
//    $hoja->setCellValueByColumnAndRow(1, 5, "Un valor en 1, 1");
  // TITULO DE LA PAGINA
  $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
  $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Consulta , Desde : " . $_POST['fechaDesde'] . ", Hasta :" . $_POST['fechaHasta']);
  $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
  /** ======================================*/
  $hoja->setCellValueByColumnAndRow(1, 3, '# Cierre');
  $hoja->setCellValueByColumnAndRow(2, 3, 'Tipo Doc');
  $hoja->setCellValueByColumnAndRow(3, 3, 'Doc');
  $hoja->setCellValueByColumnAndRow(4, 3, 'Fecha');
  $hoja->setCellValueByColumnAndRow(5, 3, 'Cliente');
  $hoja->setCellValueByColumnAndRow(6, 3, 'Exento');
  $hoja->setCellValueByColumnAndRow(7, 3, 'Grabado');
  $hoja->setCellValueByColumnAndRow(8, 3, 'Subtotal');
  $hoja->setCellValueByColumnAndRow(9, 3, 'Desc');
  $hoja->setCellValueByColumnAndRow(10, 3, 'Iva');
  $hoja->setCellValueByColumnAndRow(11, 3, 'Total');
  $i = 4;
  foreach ($ventas as $venta) {
    $hoja->setCellValueByColumnAndRow(1, $i, $venta->box_id);
    $hoja->setCellValueByColumnAndRow(2, $i, Validacion::tipoDocumento($venta->fi_tipo));
    $hoja->setCellValueByColumnAndRow(3, $i, $venta->fi_docum);
    $hoja->setCellValueByColumnAndRow(4, $i, $venta->fi_fechadoc);
    $hoja->setCellValueByColumnAndRow(5, $i, $venta->fi_er_name);
    $hoja->setCellValueByColumnAndRow(6, $i, ($venta->fi_estado != 3) ? $venta->fi_ivasi : 0.00);
    $hoja->setCellValueByColumnAndRow(7, $i, ($venta->fi_estado != 3) ? $venta->fi_ivano : 0.00);
    $hoja->setCellValueByColumnAndRow(8, $i, ($venta->fi_estado != 3) ? $venta->fi_subtotal : 0.00);
    $hoja->setCellValueByColumnAndRow(9, $i, ($venta->fi_estado != 3) ? $venta->fi_desc : 0.00);
    $hoja->setCellValueByColumnAndRow(10, $i, ($venta->fi_estado != 3) ? $venta->fi_iva : 0.00);
    $hoja->setCellValueByColumnAndRow(11, $i, ($venta->fi_estado != 3) ? $venta->fi_neto : 0.00);
    $totalST = $totalST + $venta->fi_subtotal;
    $totalNeto = $totalNeto + $venta->fi_neto;
    $totalIva = $totalIva + $venta->fi_iva;
    $totalDesc = $totalDesc + $venta->fi_desc;
    $totalSubt = $totalSubt + $venta->fi_subtotal;
    $totalivan = $totalivan + $venta->fi_ivano;
    $totalivas = $totalivas + $venta->fi_ivasi;
    $tiva = $tiva + $venta->fi_ivasi;
    $tivan = $tivan + $venta->fi_ivano;
    $subt = $subt + $venta->fi_subtotal;
    $desc = $desc + $venta->fi_desc;
    $iva = $iva + $venta->fi_iva;
    $neto = $neto + $venta->fi_neto;
    $i++;

  }
  $hoja->setCellValueByColumnAndRow(1, $i, '');
  $hoja->setCellValueByColumnAndRow(2, $i, '');
  $hoja->setCellValueByColumnAndRow(3, $i, '');
  $hoja->setCellValueByColumnAndRow(4, $i, '');
  $hoja->setCellValueByColumnAndRow(5, $i, 'TOTALES : ');
  $hoja->setCellValueByColumnAndRow(6, $i, $tiva);
  $hoja->setCellValueByColumnAndRow(7, $i, $tivan);
  $hoja->setCellValueByColumnAndRow(8, $i, $subt);
  $hoja->setCellValueByColumnAndRow(9, $i, $desc);
  $hoja->setCellValueByColumnAndRow(10, $i, $iva);
  $hoja->setCellValueByColumnAndRow(11, $i, $neto);
  $fileName = "Informe_de_Ventas_Cierre_SMARTTAG_BI.xlsx";
  # Crear un "escritor"
  $writer = new Xlsx($spread);
  # Le pasamos la ruta de guardado
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
  $writer->save('php://output');

}


class Validacion {
  public static function ValidaFecha($fechaini, $fechahasta) {
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

  public static function tipoDocumento($tipo) {
    $array = array(
            "FACTURA" => "01",
            "NOTA DE CRÉDITO" => "04",
            "RETENCION" => "07"
    );
    $indice = array_search($tipo, $array, false);
    return $indice;
  }
}