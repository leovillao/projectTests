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


$where = 'where a.crfecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'];
}
if ($_POST['sucursal'] != 0) {
    $where .= " and a.suid = " . $_POST['sucursal'];
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

$where = 'where a.defecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'];
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= 'and a.suid = ' . $_POST['sucursal'];
}
if ($_POST['zona'] != 0) { // zona
    $where .= 'and b.zoid = ' . $_POST['zona'];
}
if ($_POST['tipoDocumento'] != 0) { // zoid
    $where .= 'and a.tdid = ' . $_POST['tipoDocumento'];
}
if ($_POST['vendedor'] != 0) { // zoid
    $where .= 'and a.veid = ' . $_POST['vendedor'];
}
//if ($_POST['etiquetac'] != 0) { // zoid
//    $where .= ' and h.id_etiq = ' . $_POST['etiquetac'];
//}
if ($_POST['etiquetac'] != 0) { // etiqueta
    $where .= ' and b.setq_id = ' . $_POST['etiquetac'];
}
$where .= " order by a.defecha,a.tdid,a.deid asc";

$cobros = DeudasData::getByDataAllFechas($where);

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
$hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
$hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
/** ======================================*/
$hoja->setCellValueByColumnAndRow(1, 3, 'Fecha');
$hoja->setCellValueByColumnAndRow(2, 3, 'Tipo');
$hoja->setCellValueByColumnAndRow(3, 3, 'Doc');
$hoja->setCellValueByColumnAndRow(4, 3, 'Cliente');
$hoja->setCellValueByColumnAndRow(5, 3, 'Documento');
$hoja->setCellValueByColumnAndRow(6, 3, 'Total');
$hoja->setCellValueByColumnAndRow(7, 3, 'Abono');
$hoja->setCellValueByColumnAndRow(8, 3, 'Saldo');
$hoja->setCellValueByColumnAndRow(9, 3, 'Fecha Venc');
$hoja->setCellValueByColumnAndRow(10, 3, 'Dias Venc');

$i = 4;
$o = 0;
$t = 1;

foreach ($cobros as $cobro) {
    $hoja->setCellValueByColumnAndRow(1, $i, $cobro->defecha);
    $hoja->setCellValueByColumnAndRow(2, $i, $cobro->tdnombre);
    $hoja->setCellValueByColumnAndRow(3, $i, $cobro->deid);
    $hoja->setCellValueByColumnAndRow(4, $i, ucwords(strtolower($cobro->cename)));
    $hoja->setCellValueByColumnAndRow(5, $i, $cobro->derefer);
    $hoja->setCellValueByColumnAndRow(6, $i, $cobro->detotal);
    $hoja->setCellValueByColumnAndRow(7, $i, $cobro->deabono);
    $hoja->setCellValueByColumnAndRow(8, $i, $cobro->desaldo);
    $hoja->setCellValueByColumnAndRow(9, $i, $cobro->devence);
    $hoja->setCellValueByColumnAndRow(10, $i, ($cobro->vencidos > 0 && $cobro->desaldo > 0)?$cobro->vencidos:'');
    $i++;
    $o++;
}
$fileName = "InformeSaldoPorDocumentof2.xlsx";
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

    public static function validaDatos()
    {
        $msj = '';
        if (empty($_POST['desde']) && !empty($_POST['hasta'])) {
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
        return CrucecabData::getAllFechas($where);
    }
}
