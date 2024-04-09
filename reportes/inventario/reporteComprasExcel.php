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
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
//require../../ 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/ProveeData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/TipoOperationData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';


$where = 'where opfecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if (isset($_POST['proveedor']) && !empty($_POST['proveedor'])) { // proveedores
    $where .= 'and prid = ' . ProveeData::getByRucProvee($_POST['proveedor'])->id;
}

if ($_POST['opcionReporte'] == 1) {
    $where .= " and fi_id is NULL and prid IS NOT NULL";
}

if ($_POST['opcionReporte'] == 2) {
    $where .= " and fi_id is NOT NULL and prid IS NOT NULL";
}

// tipoReporte = 1 => resumido
// tipoReporte = 2 => detallado
if ($_POST['tipoReporte'] == 1) {
    $compras = OperationData::getInformacionComprasResumidos($where);
//    echo json_encode($compras);
} else {
    $compras = OperationData::getInformacionCompras($where);
}
if ($compras) {
    if ($_POST['tipoReporte'] == 1) {
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
        $spread->getActiveSheet()->getStyle('C')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
        $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
        $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
        $hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
        $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, 'Documento');
        $hoja->setCellValueByColumnAndRow(2, 3, 'Fecha');
        $hoja->setCellValueByColumnAndRow(3, 3, 'Factura');
        $hoja->setCellValueByColumnAndRow(4, 3, 'Tipo');
        $hoja->setCellValueByColumnAndRow(5, 3, 'Proveedor');
        $hoja->setCellValueByColumnAndRow(6, 3, 'Total');
//    $hoja->setCellValueByColumnAndRow(7, 3, 'Saldo');
        $i = 4;
        $o = 0;
        foreach ($compras as $venta) {

            $hoja->setCellValueByColumnAndRow(1, $i, $venta->opnumdoc);
            $hoja->setCellValueByColumnAndRow(2, $i, $venta->opfecha);
            $hoja->setCellValueByColumnAndRow(3, $i, (!is_null($venta->fi_id)) ? FilesData::getByIdOne($venta->fi_id)->fi_docum : 'PENDIENTE');
            $hoja->setCellValueByColumnAndRow(4, $i, TipoOperationData::getById($venta->toid)->todescrip);
            if ($venta->prid != null) {
                $hoja->setCellValueByColumnAndRow(5, $i, ProveeData::getById($venta->prid)->ruc . ' - ' . ProveeData::getById($venta->prid)->razon);
            } else {
                $hoja->setCellValueByColumnAndRow(5, $i, '');
            }
            $hoja->setCellValueByColumnAndRow(6, $i, FData::formatoNumeroReportsInventario($venta->total));
//        $hoja->setCellValueByColumnAndRow(7, $i, );
            $i++;
            $o++;
        }
        $fileName = "Informe_de_Saldos_SMARTTAG_BI.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
    } else {
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
        $spread->getActiveSheet()->getStyle('C')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
        $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
        $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
        $hoja->setCellValueByColumnAndRow(1, 2, "Fecha , desde : " . $_POST['desde'] . "hasta :" . $_POST['hasta']);
        $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, 'Codigo');
        $hoja->setCellValueByColumnAndRow(2, 3, 'Producto');
        $hoja->setCellValueByColumnAndRow(3, 3, 'Cantidad');
        $hoja->setCellValueByColumnAndRow(4, 3, 'Factura');
        $hoja->setCellValueByColumnAndRow(5, 3, 'Unidad');
        $hoja->setCellValueByColumnAndRow(6, 3, 'Costo');
        $hoja->setCellValueByColumnAndRow(7, 3, 'Total');
//    $hoja->setCellValueByColumnAndRow(7, 3, 'Saldo');
        $i = 4;
        $o = 0;
        foreach ($compras as $venta) {

            $hoja->setCellValueByColumnAndRow(1, $i, ProductData::getById($venta->itid)->itcodigo);
            $hoja->setCellValueByColumnAndRow(2, $i, ProductData::getById($venta->itid)->itname);
            $hoja->setCellValueByColumnAndRow(3, $i, FData::formatoNumeroReportsInventario($venta->odcandig));
            $hoja->setCellValueByColumnAndRow(4, $i, (!is_null($venta->fi_id)) ? FilesData::getByIdOne($venta->fi_id)->fi_docum : 'PENDIENTE');
            $hoja->setCellValueByColumnAndRow(5, $i, UnitData::getById($venta->unid_dig)->undescrip);
            $hoja->setCellValueByColumnAndRow(6, $i, FData::formatoNumeroReportsInventario($venta->odcostoudig));
            $hoja->setCellValueByColumnAndRow(7, $i, FData::formatoNumeroReportsInventario($venta->odcostotot));

            /*if ($venta->prid != null) {
                $hoja->setCellValueByColumnAndRow(5, $i, ProveeData::getById($venta->prid)->razon);
            } else {
                $hoja->setCellValueByColumnAndRow(5, $i, '');
            }*/
//        $hoja->setCellValueByColumnAndRow(7, $i, );
            $i++;
            $o++;
        }
        $fileName = "Informe_de_Saldos_SMARTTAG_BI.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
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