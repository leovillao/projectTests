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


$where = 'where crfecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if (isset($_POST['cliente']) && !empty($_POST['cliente'])) { // proveedores
    $where .= ' and a.ceid = ' . $_POST['cliente'];
}
if ($_POST['sucursal'] != 0) {
    $where .= " and suid = " . $_POST['sucursal'];
}

if ($_POST['etiquetac'] != 0) {
    $where .= " and setq_id  = " . $_POST['etiquetac'] . " ";
}

//if (isset($_POST)) {
//    if (empty(Validacion::validaData($where))) {
//        print_r('
//          <script>
//          alert("No hay datos con los criterios seleccionados.")
//            window.close();
//          </script>
//      ');
//    }
//
//    $error = Validacion::validaDatos();
//    if ($error != '' || !isset($_SESSION)) {
//        print_r('
//      <script>
//      alert("' . $error . '")
//      window.close();
//      </script>
//      ');
//    }
//}

//$where = "where DATE(ancreate_at) >= \"$desde\" and DATE(ancreate_at) <= \"$hasta\" ";

$compensaciones = CrucecabData::getAllFechasCab($where);
if ($compensaciones) {
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
//    $spread->getActiveSheet()->getColumnDimension('A')->setWidth(17);
    $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $spread->getActiveSheet()->getColumnDimension('C')->setWidth(30);
    $spread->getActiveSheet()->getStyle('A4');
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
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, 'COMPENSACION');
    $hoja->setCellValueByColumnAndRow(2, 3, 'ANTICIPO');
    $hoja->setCellValueByColumnAndRow(3, 3, 'FECHA');
    $hoja->setCellValueByColumnAndRow(4, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(5, 3, 'SUCURSAL');
    $hoja->setCellValueByColumnAndRow(6, 3, 'COMENTARIO');
    $hoja->setCellValueByColumnAndRow(7, 3, 'DOCUMENTO DEUDA');
    $hoja->setCellValueByColumnAndRow(8, 3, 'VALOR DEUDA');
    $hoja->setCellValueByColumnAndRow(9, 3, 'ANTICIPO DEUDA');
    $i = 4;
    $o = 0;
    $t = 1;

    foreach ($compensaciones as $compensacion) {
        foreach (CrucecabData::getAllFechasDet($compensacion->crid) as $detalle) {
            $hoja->setCellValueByColumnAndRow(1, $i, $detalle->crid);
            $hoja->setCellValueByColumnAndRow(2, $i, $detalle->anid);
            $hoja->setCellValueByColumnAndRow(3, $i, $detalle->defecha);
            $hoja->setCellValueByColumnAndRow(4, $i, strtoupper($compensacion->cename));
            $hoja->setCellValueByColumnAndRow(5, $i, strtoupper($compensacion->suname));
            $hoja->setCellValueByColumnAndRow(6, $i, strtoupper($compensacion->crcomenta));
            $hoja->setCellValueByColumnAndRow(7, $i, $detalle->derefer);
            $hoja->setCellValueByColumnAndRow(8, $i, $detalle->cdvalor);
            $hoja->setCellValueByColumnAndRow(9, $i, $detalle->cavalor);
            $i++;
            $o++;
        }
    }
    $fileName = "InformeCarteraAnticipos.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
} else {
    print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
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
