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

$where = 'where a.deestado = 1 and a.defecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= 'and a.suid = ' . $_POST['sucursal'] . " ";
}
if ($_POST['zona'] != 0) { // zona
    $where .= 'and b.zoid = ' . $_POST['zona'] . " ";
}
if ($_POST['tipoDocumento'] != 0) { // zoid
    $where .= 'and a.tdid = ' . $_POST['tipoDocumento'] . " ";
}
if ($_POST['vendedor'] != 0) { // zoid
    $where .= 'and a.veid = ' . $_POST['vendedor'];
}
if ($_POST['etiquetac'] != 0) { // zoid
    $where .= ' and b.setq_id = ' . $_POST['etiquetac'];
}
//vencidosdesde
//vencidoshasta

if ($_POST['vencidosdesde'] >= 1 && $_POST['vencidoshasta'] >= 1) {
    if ($_POST['estadodocumentos'] === 'negativo') {
        $where .= ' and DATEDIFF(NOW(), a.devence) <= 0 ';
    } else if ($_POST['estadodocumentos'] === 'positivo') {
        $where .= ' and DATEDIFF(NOW(), a.devence) >= ' . $_POST['vencidosdesde'] . ' and DATEDIFF(NOW(), a.devence) <= ' . $_POST['vencidoshasta'] . ' ';
    }
}

$where .= " order by h.name ,a.defecha , a.deid desc ";

$cobros = DeudasData::getByDataAllFechasVista($where, $_POST['ccdate']);
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
$hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
$hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
$hoja->setCellValueByColumnAndRow(1, 2, "Fecha de Corte : " . $_POST['fecha']);
$hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
/** ======================================*/
$hoja->setCellValueByColumnAndRow(1, 3, 'Fecha');
$hoja->setCellValueByColumnAndRow(2, 3, 'Tipo Doc');
$hoja->setCellValueByColumnAndRow(3, 3, 'Cliente');
$hoja->setCellValueByColumnAndRow(4, 3, 'Documento');
$hoja->setCellValueByColumnAndRow(5, 3, 'Total');
$hoja->setCellValueByColumnAndRow(6, 3, 'Abono');
$hoja->setCellValueByColumnAndRow(7, 3, 'Saldo');
$hoja->setCellValueByColumnAndRow(8, 3, 'Fecha Venc');
$hoja->setCellValueByColumnAndRow(9, 3, 'Dias Venc');
$i = 4;
$o = 0;
$t = 1;
if (count($cobros) > 0) {
    if ($_POST['alcance'] == 1) {
        foreach ($cobros as $cobro) {
            if ($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0) {
                $hoja->setCellValueByColumnAndRow(1, $i, $cobro->defecha);
                $hoja->setCellValueByColumnAndRow(2, $i, $cobro->tdnombre);
                $hoja->setCellValueByColumnAndRow(3, $i, ucwords(strtolower($cobro->cename)));
                $hoja->setCellValueByColumnAndRow(4, $i, $cobro->derefer);
                $hoja->setCellValueByColumnAndRow(5, $i, $cobro->detotal);
                $hoja->setCellValueByColumnAndRow(6, $i, $cobro->deabono + $cobro->decopensa);
                $hoja->setCellValueByColumnAndRow(7, $i, $cobro->detotal - ($cobro->deabono + $cobro->decopensa));
                $hoja->setCellValueByColumnAndRow(8, $i, $cobro->devence);
                $hoja->setCellValueByColumnAndRow(9, $i, $cobro->vencidos);
                $i++;
                $o++;
            }
        }
    } elseif ($_POST['alcance'] == 2) {
        foreach ($cobros as $cobro) {
            if (($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0) && $cobro->vencidos > 0) {
                $hoja->setCellValueByColumnAndRow(1, $i, $cobro->defecha);
                $hoja->setCellValueByColumnAndRow(2, $i, $cobro->tdnombre);
                $hoja->setCellValueByColumnAndRow(3, $i, ucwords(strtolower($cobro->cename)));
                $hoja->setCellValueByColumnAndRow(4, $i, $cobro->derefer);
                $hoja->setCellValueByColumnAndRow(5, $i, $cobro->detotal);
                $hoja->setCellValueByColumnAndRow(6, $i, $cobro->deabono + $cobro->decopensa);
                $hoja->setCellValueByColumnAndRow(7, $i, $cobro->detotal - ($cobro->deabono + $cobro->decopensa));
                $hoja->setCellValueByColumnAndRow(8, $i, $cobro->devence);
                $hoja->setCellValueByColumnAndRow(9, $i, $cobro->vencidos);
                $i++;
                $o++;
            }
        }
    } elseif ($_POST['alcance'] == 3) {
        foreach ($cobros as $cobro) {
            if ($cobro->vencidos <= 0 && ($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0)) {
                $hoja->setCellValueByColumnAndRow(1, $i, $cobro->defecha);
                $hoja->setCellValueByColumnAndRow(2, $i, $cobro->tdnombre);
                $hoja->setCellValueByColumnAndRow(3, $i, ucwords(strtolower($cobro->cename)));
                $hoja->setCellValueByColumnAndRow(4, $i, $cobro->derefer);
                $hoja->setCellValueByColumnAndRow(5, $i, $cobro->detotal);
                $hoja->setCellValueByColumnAndRow(6, $i, $cobro->deabono + $cobro->decopensa);
                $hoja->setCellValueByColumnAndRow(7, $i, $cobro->detotal - ($cobro->deabono + $cobro->decopensa));
                $hoja->setCellValueByColumnAndRow(8, $i, $cobro->devence);
                $hoja->setCellValueByColumnAndRow(9, $i, $cobro->vencidos);
                $i++;
                $o++;
            }
        }
    } else {
        foreach ($cobros as $cobro) {
            $hoja->setCellValueByColumnAndRow(1, $i, $cobro->defecha);
            $hoja->setCellValueByColumnAndRow(2, $i, $cobro->tdnombre);
            $hoja->setCellValueByColumnAndRow(3, $i, ucwords(strtolower($cobro->cename)));
            $hoja->setCellValueByColumnAndRow(4, $i, $cobro->derefer);
            $hoja->setCellValueByColumnAndRow(5, $i, $cobro->detotal);
            $hoja->setCellValueByColumnAndRow(6, $i, $cobro->deabono + $cobro->decopensa);
            $hoja->setCellValueByColumnAndRow(7, $i, $cobro->detotal - ($cobro->deabono + $cobro->decopensa));
            $hoja->setCellValueByColumnAndRow(8, $i, $cobro->devence);
            $hoja->setCellValueByColumnAndRow(9, $i, $cobro->vencidos);
            $i++;
            $o++;
        }
    }
    $fileName = "InformeSaldoPorDocumentof1.xlsx";
# Crear un "escritor"
    $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
    $writer->save('php://output');
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

}
