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
require '../../core/modules/index/model/PedidosdetData.php';
require '../../core/modules/index/model/PedidosData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/FData.php';
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

$desde = $_POST['desde'];
$hasta = $_POST['hasta'];

$where = "where DATE(pefecha) >= \"$desde\" and DATE(pefecha) <= \"$hasta\" ";

if ($_POST['cliente'] != 0) {
    $where .= " and ceid = " . $_POST['cliente'];
}
if ($_POST['vendedor'] != 0) {
    $where .= " and veid = " . $_POST['vendedor'];
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

if ($_POST['tipo'] == 0) {
    $anticipos = PedidosData::getByAllFecha($where);
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
    $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
    $hoja->setTitle("Informe de Pedidos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/
    $hoja->setCellValueByColumnAndRow(1, 3, 'PEDIDO');
    $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
    $hoja->setCellValueByColumnAndRow(3, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(4, 3, 'ESTADO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'APROBADO');
    $hoja->setCellValueByColumnAndRow(6, 3, 'TOTAL');
    $i = 4;
    $o = 0;
    foreach ($anticipos as $pedido) {
        $estado = "ACTIVO";
        $aprobado = "APROBADO";
        $hoja->setCellValueByColumnAndRow(1, $i, $pedido->peid);
        $hoja->setCellValueByColumnAndRow(2, $i, $pedido->pefecha);
        $hoja->setCellValueByColumnAndRow(3, $i, strtoupper(utf8_decode(PersonData::getById($pedido->ceid)->cename)));
        if ($pedido->peestado == 0) {
            $estado = "ANULADO";
        }
        if ($pedido->peaprobado == "N") {
            $aprobado = "PENDIENTE";
        }
        $hoja->setCellValueByColumnAndRow(4, $i, $estado);
        $hoja->setCellValueByColumnAndRow(5, $i, $aprobado);
        $hoja->setCellValueByColumnAndRow(6, $i, number_format($pedido->petotal, 2, '.', ','));
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
} elseif ($_POST['tipo'] == 1) {
    $pedidos = PedidosData::getByAllDetallado($where);
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
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/

    $hoja->setCellValueByColumnAndRow(1, 3, 'PEDIDO');
    $hoja->setCellValueByColumnAndRow(2, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(3, 3, 'COD');
    $hoja->setCellValueByColumnAndRow(4, 3, 'PRODUCTO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'CANTIDAD');
    $hoja->setCellValueByColumnAndRow(6, 3, 'UNIDAD');
    $hoja->setCellValueByColumnAndRow(7, 3, 'PVP');
    $hoja->setCellValueByColumnAndRow(8, 3, 'DESC1');
    $hoja->setCellValueByColumnAndRow(9, 3, 'DESC2');
    $hoja->setCellValueByColumnAndRow(10, 3, 'SUBTOTAL');
    $hoja->setCellValueByColumnAndRow(11, 3, 'IVA');
    $hoja->setCellValueByColumnAndRow(12, 3, 'TOTAL');
    $i = 4;
    $o = 0;
    $t = 1;

    foreach ($pedidos as $pedido) {
        $hoja->setCellValueByColumnAndRow(1, $i, $pedido->peid);
        $hoja->setCellValueByColumnAndRow(2, $i, $pedido->cename);
        $hoja->setCellValueByColumnAndRow(3, $i, $pedido->itid);
        $hoja->setCellValueByColumnAndRow(4, $i, $pedido->itname);
        $hoja->setCellValueByColumnAndRow(5, $i, FData::formatoNumeroReportsInventario($pedido->pdcandig));
        $hoja->setCellValueByColumnAndRow(6, $i, $pedido->undescrip);
        $hoja->setCellValueByColumnAndRow(7, $i, FData::formatoNumeroReportsInventario($pedido->pdpvp));
        $hoja->setCellValueByColumnAndRow(8, $i, FData::formatoNumeroReportsInventario($pedido->pdpdscto1));
        $hoja->setCellValueByColumnAndRow(9, $i, FData::formatoNumeroReportsInventario($pedido->pdpdscto2));
        $hoja->setCellValueByColumnAndRow(10, $i, FData::formatoNumeroReportsInventario($pedido->pdtotal));
        $hoja->setCellValueByColumnAndRow(11, $i, FData::formatoNumeroReportsInventario($pedido->pdiva));
        $hoja->setCellValueByColumnAndRow(12, $i, FData::formatoNumeroReportsInventario($pedido->pdtotal + $pedido->pdiva));
        $i++;
        $tpvp += $pedido->pdpvp;
        $tdesc1 += $pedido->pdpdscto1;
        $tdesc2 += $pedido->pdpdscto2;
        $tiva += $pedido->pdiva;
        $total += $pedido->pdtotal;
        $tentr += $pedido->pdtotal + $pedido->pdiva;
    }
    $hoja->setCellValueByColumnAndRow(6, $i + 1, "Totales :", 'T,B,R', 0, 'R', 1, 0);
    $hoja->setCellValueByColumnAndRow(7, $i + 1, FData::formatoNumeroReportsInventario($tpvp));
    $hoja->setCellValueByColumnAndRow(8, $i + 1, FData::formatoNumeroReportsInventario($tdesc1));
    $hoja->setCellValueByColumnAndRow(9, $i + 1, FData::formatoNumeroReportsInventario($tdesc2));
    $hoja->setCellValueByColumnAndRow(10, $i + 1, FData::formatoNumeroReportsInventario($total));
    $hoja->setCellValueByColumnAndRow(11, $i + 1, FData::formatoNumeroReportsInventario($tiva));
    $hoja->setCellValueByColumnAndRow(12, $i + 1, FData::formatoNumeroReportsInventario($tentr));
    $fileName = "InformePedidos.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
}elseif($_POST['tipo'] == 2){
    $pedidos = PedidosData::getByAllDetalladoPendientes($where);
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
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
    $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
    $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
    $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
    $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
    /** ======================================*/

    $hoja->setCellValueByColumnAndRow(1, 3, 'PEDIDO');
    $hoja->setCellValueByColumnAndRow(2, 3, 'CLIENTE');
    $hoja->setCellValueByColumnAndRow(3, 3, 'COD');
    $hoja->setCellValueByColumnAndRow(4, 3, 'PRODUCTO');
    $hoja->setCellValueByColumnAndRow(5, 3, 'CANTIDAD');
    $hoja->setCellValueByColumnAndRow(6, 3, 'UNIDAD');
    $hoja->setCellValueByColumnAndRow(7, 3, 'PVP');
    $hoja->setCellValueByColumnAndRow(8, 3, 'DESC1');
    $hoja->setCellValueByColumnAndRow(9, 3, 'DESC2');
    $hoja->setCellValueByColumnAndRow(10, 3, 'SUBTOTAL');
    $hoja->setCellValueByColumnAndRow(11, 3, 'IVA');
    $hoja->setCellValueByColumnAndRow(12, 3, 'TOTAL');
    $hoja->setCellValueByColumnAndRow(13, 3, 'CANTIDAD ENTREGADA');
    $hoja->setCellValueByColumnAndRow(14, 3, 'CANTIDAD PENDIENTE');
    $i = 4;
    $o = 0;
    $t = 1;

    foreach ($pedidos as $pedido) {
        $hoja->setCellValueByColumnAndRow(1, $i, $pedido->peid);
        $hoja->setCellValueByColumnAndRow(2, $i, $pedido->cename);
        $hoja->setCellValueByColumnAndRow(3, $i, $pedido->itid);
        $hoja->setCellValueByColumnAndRow(4, $i, $pedido->itname);
        $hoja->setCellValueByColumnAndRow(5, $i, FData::formatoNumeroReportsInventario($pedido->pdcandig));
        $hoja->setCellValueByColumnAndRow(6, $i, $pedido->undescrip);
        $hoja->setCellValueByColumnAndRow(7, $i, FData::formatoNumeroReportsInventario($pedido->pdpvp));
        $hoja->setCellValueByColumnAndRow(8, $i, FData::formatoNumeroReportsInventario($pedido->pdpdscto1));
        $hoja->setCellValueByColumnAndRow(9, $i, FData::formatoNumeroReportsInventario($pedido->pdpdscto2));
        $hoja->setCellValueByColumnAndRow(10, $i, FData::formatoNumeroReportsInventario($pedido->pdtotal));
        $hoja->setCellValueByColumnAndRow(11, $i, FData::formatoNumeroReportsInventario($pedido->pdiva));
        $hoja->setCellValueByColumnAndRow(12, $i, FData::formatoNumeroReportsInventario($pedido->pdtotal + $pedido->pdiva));
        $hoja->setCellValueByColumnAndRow(13, $i, FData::formatoNumeroReportsInventario($pedido->pdcanentrega));
        $hoja->setCellValueByColumnAndRow(14, $i, FData::formatoNumeroReportsInventario($pedido->pdcandig - $pedido->pdcanentrega));
        $i++;
        $tpvp += $pedido->pdpvp;
        $tdesc1 += $pedido->pdpdscto1;
        $tdesc2 += $pedido->pdpdscto2;
        $tiva += $pedido->pdiva;
        $total += $pedido->pdtotal;
        $tentr += $pedido->pdtotal + $pedido->pdiva;
        $tentrega += $pedido->pdcanentrega;
        $tpendiente += $pedido->pdcandig - $pedido->pdcanentrega;
    }
    $hoja->setCellValueByColumnAndRow(6, $i + 1, "Totales :", 'T,B,R', 0, 'R', 1, 0);
    $hoja->setCellValueByColumnAndRow(7, $i + 1, FData::formatoNumeroReportsInventario($tpvp));
    $hoja->setCellValueByColumnAndRow(8, $i + 1, FData::formatoNumeroReportsInventario($tdesc1));
    $hoja->setCellValueByColumnAndRow(9, $i + 1, FData::formatoNumeroReportsInventario($tdesc2));
    $hoja->setCellValueByColumnAndRow(10, $i + 1, FData::formatoNumeroReportsInventario($total));
    $hoja->setCellValueByColumnAndRow(11, $i + 1, FData::formatoNumeroReportsInventario($tiva));
    $hoja->setCellValueByColumnAndRow(12, $i + 1, FData::formatoNumeroReportsInventario($tentr));
    $hoja->setCellValueByColumnAndRow(13, $i + 1, FData::formatoNumeroReportsInventario($tentrega));
    $hoja->setCellValueByColumnAndRow(14, $i + 1, FData::formatoNumeroReportsInventario($tpendiente));
    $fileName = "InformePedidos.xlsx";
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
        return PedidosData::getByAllFecha($where);
    }
}