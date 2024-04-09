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
require '../../core/modules/index/model/VecinoData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';

//$desde = $_POST['desde'];
//$hasta = $_POST['hasta'];
//
//$where = "where DATE(ancreate_at) >= \"$desde\" and DATE(ancreate_at) <= \"$hasta\" ";
$fecha = "";
$sufijo = "c";
$wh = " and ";
if ($_POST['tipo'] != 2) {
    $fecha = " DATE(a.anfecha) >= '". $_POST["desde"] ."' and";
    $sufijo = "a";
    $wh = "where";
}
//$desde =
$hasta = $_POST['hasta'];

$where = "".$wh." ".$fecha." DATE(".$sufijo.".anfecha) <= \"$hasta\"  ";

if ($_POST['cliente'] != 0) {
    $where .= " and c.ceid = " . $_POST['cliente'];
}

if ($_POST['tipo'] != 2)  {

    if ($_POST['tipoAnticipo'] != 0) {
        $where .= " and a.taid = " . $_POST['tipoAnticipo'];
    }
    if (isset($_POST)) {
        if (empty(Validacion::validaData($where))) {
            print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
        }

        $error = Validacion::validaDatos();
        if ($error != '' || !isset($_SESSION)) {
            print_r('
      <script>
      alert("' . $error . '")
      window.close();
      </script>
      ');
        }
    }
}else{
    if (isset($_POST)) {

        if ($_POST['tipoAnticipo'] != 0) {
            $where .= " and c.taid = " . $_POST['tipoAnticipo'];
        }
        if ($_POST['etiquetac'] != 0) {
            $where .= " and t.setq_id = " . $_POST['etiquetac'];
        }
        if (empty(Validacion::validaDataCorte($_POST['hasta'],$where))) {
            print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
        }
    }
}

//$where = "where DATE(ancreate_at) >= \"$desde\" and DATE(ancreate_at) <= \"$hasta\" ";

//var_dump($_POST);
if ($_POST['tipo'] == 0) {
    $where = "";
    $fecha = "";
    $sufijo = "c";
    $wh = " and ";
    if ($_POST['tipo'] != 2) {
        $fecha = " DATE(a.anfecha) >= '". $_POST["desde"] ."' and";
        $sufijo = "a";
        $wh = "where";
    }
//$desde =
    $hasta = $_POST['hasta'];

    $where = "".$wh." ".$fecha." DATE(".$sufijo.".anfecha) <= \"$hasta\"  ";

    if ($_POST['cliente'] != 0) {
        $where .= " and a.ceid = " . $_POST['cliente'];
    }
    if ($_POST['etiquetac'] != 0) {
        $where .= " and b.setq_id = " . $_POST['etiquetac'];
    }
    $anticipos = AnticipocabData::getByAllAnticiposResumidoExcel($where);
//    var_dump($anticipos);
    $spread = new Spreadsheet();
    $spread
        ->getProperties()
        ->setCreator("SmartTag-Bi")
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
    $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, '#');
    $hoja->setCellValueByColumnAndRow(2, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(3, 3, 'TOTAL');
    $i = 4;
    $o = 0;
    foreach ($anticipos as $anticipo) {
        $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
        $hoja->setCellValueByColumnAndRow(2, $i, strtoupper(PersonData::getById($anticipo->ceid)->cename));
        $hoja->setCellValueByColumnAndRow(3, $i, $anticipo->total);
        $i++;
        $o++;
    }
    $fileName = "InformeCarteraAnticipos.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
} elseif($_POST['tipo'] == 1) {
    $where = "";
    $fecha = "";
    $sufijo = "c";
    $wh = " and ";
    if ($_POST['tipo'] != 2) {
        $fecha = " DATE(a.anfecha) >= '". $_POST["desde"] ."' and";
        $sufijo = "a";
        $wh = "where";
    }
//$desde =
    $hasta = $_POST['hasta'];

    $where = "".$wh." ".$fecha." DATE(".$sufijo.".anfecha) <= \"$hasta\"  ";

    if ($_POST['cliente'] != 0) {
        $where .= " and a.ceid = " . $_POST['cliente'];
    }
    if ($_POST['etiquetac'] != 0) {
        $where .= " and c.setq_id = " . $_POST['etiquetac'];
    }
    $anticipos = AnticipocabData::getByAllFechaReporte($where);
//    var_dump($anticipos);
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
    $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(2, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(3, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(4, 3, 'ABONO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'SALDO');
    $i = 4;
    $o = 0;
    $t = 1;

    foreach ($anticipos as $anticipo) {
        $detalles = AnticipodetData::getByAnId($anticipo->anid);
        foreach ($detalles as $detalle) {
            $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
            $hoja->setCellValueByColumnAndRow(2, $i, strtoupper(PersonData::getById($anticipo->ceid)->cename));
            $hoja->setCellValueByColumnAndRow(3, $i, $anticipo->anvalor);
            $hoja->setCellValueByColumnAndRow(4, $i, $anticipo->anaplica);
            $hoja->setCellValueByColumnAndRow(5, $i, $anticipo->ansaldo);
        }
        $i++;
        $o++;
    }
    $fileName = "InformeCarteraAnticipos.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
}elseif ($_POST['tipo'] == 2) {

    $fechaCorte = $_POST['hasta'];
    $anticipos = AnticipocabData::getByForCorteReporte($fechaCorte,$where);
//    var_dump($anticipos);
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
//    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
//    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
//    $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $spread->getActiveSheet()->getStyle('E')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $spread->getActiveSheet()->getStyle('D')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $spread->getActiveSheet()->getStyle('F')
        ->getNumberFormat()
        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['hasta']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, '#');
    $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
    $hoja->setCellValueByColumnAndRow(3, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(4, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'ABONO');
    $hoja->setCellValueByColumnAndRow(6, 3, 'SALDO');
    $i = 4;
    $o = 0;
    foreach ($anticipos as $anticipo) {
        if (($anticipo->anvalor - $anticipo->aplicado) != 0) {

            $hoja->setCellValueByColumnAndRow(1, $i, $anticipo->anid);
            $hoja->setCellValueByColumnAndRow(2, $i, $anticipo->anfecha);
            $hoja->setCellValueByColumnAndRow(3, $i, strtoupper(utf8_decode(PersonData::getById($anticipo->ceid)->cename)));
            $hoja->setCellValueByColumnAndRow(4, $i, number_format($anticipo->anvalor, 2, '.', ','));
            $hoja->setCellValueByColumnAndRow(5, $i, number_format($anticipo->aplicado, 2, '.', ','));
            $hoja->setCellValueByColumnAndRow(6, $i, number_format($anticipo->anvalor - $anticipo->aplicado, 2, '.', ','));
            $i++;
            $totalvalor += $anticipo->anvalor;
            $totalAplicado += $anticipo->aplicado;
            $totalSaldo += $anticipo->anvalor - $anticipo->aplicado;
        }
    }
//    $pdf->Cell(38, 5, "", 'T,B,L,R', 0, 'R', 0, 1);
//    $pdf->Cell(85, 5, "TOTALES : ", 'T,B,L,R', 0, 'R', 0, 1);
    $hoja->setCellValueByColumnAndRow(4, $i, number_format($totalvalor, 2, '.', ','));
    $hoja->setCellValueByColumnAndRow(5, $i, number_format($totalAplicado, 2, '.', ','));
    $hoja->setCellValueByColumnAndRow(6, $i, number_format($totalSaldo, 2, '.', ','));
    $fileName = "InformeCarteraAnticipos.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
}else{
    if ($_POST['etiquetac'] != 0) {
        $where .= " and c.setq_id = " . $_POST['etiquetac'];
    }
    $anticipos = AnticipocabData::getByAllFechaReporte($where);
//    var_dump($anticipos);
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
    $spread->getActiveSheet()->mergeCells('A1:E1');
    $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
    $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
    $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(2, 3, 'TIPO ANTICIPO');
    $hoja->setCellValueByColumnAndRow(3, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(4, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'ABONO');
    $hoja->setCellValueByColumnAndRow(6, 3, 'SALDO');
    $i = 4;
    $o = 0;
    $t = 1;

    foreach ($anticipos as $anticipo) {
        $detalles = AnticipodetData::getByAnId($anticipo->anid);
        foreach ($detalles as $detalle) {
            $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
            $hoja->setCellValueByColumnAndRow(2, $i, strtoupper(AnticipoData::getById($anticipo->taid)->tanombre));
            $hoja->setCellValueByColumnAndRow(3, $i, strtoupper(PersonData::getById($anticipo->ceid)->cename));
            $hoja->setCellValueByColumnAndRow(4, $i, $anticipo->anvalor);
            $hoja->setCellValueByColumnAndRow(5, $i, $anticipo->anaplica);
            $hoja->setCellValueByColumnAndRow(6, $i, $anticipo->ansaldo);
        }
        $i++;
        $o++;
    }
    $fileName = "InformeCarteraAnticipos.xlsx";
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
        if (empty($fechaini) && empty($fechahasta) && $_POST['tipo'] != 2) {
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

    public static function validaDatos()
    {
        $msj = '';
        if (empty($_POST['desde']) && !empty($_POST['hasta']) && $_POST['tipo'] != 2) {
            $msj = "Debe ingresar fecha de inicio";
        }
        if (!empty($_POST['desde']) && empty($_POST['hasta'])) {
            $msj = "Debe ingresar fecha de fin";
        }
        if (empty($_POST['desde']) && empty($_POST['hasta'])) {
            $msj = "Debe ingresar rango de fecha ";
        }
        return $msj;
    }

    public static function validaData($where)
    {
        return AnticipocabData::getByAllFecha($where);
    }

    public static function validaDataCorte($fecha,$where)
    {
        return AnticipocabData::getByForCorteReporte($fecha,$where);
    }
}
