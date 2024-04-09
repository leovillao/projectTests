<?php
require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
include 'cabeceraCartera.php';
include 'funcionesReporte.php';
if (isset($_POST)) {
    $error = validaDatos();
    if ($error != '' || !isset($_SESSION)) {
        print_r('
      <script>
      alert("' . $error . '")
      window.close();
      </script>
      ');
    }
}
if ($_POST['tipo'] == 1) {
    /**===========================================================================
     * SI EL TIPO DE INFORME ES RESUMIDO *
     * =========================================================================== */
    if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DE LAS CABECERAS Y SU RESPECTIVO DOCUMENTO PDF */
//        ReporteCobros::getDetalleFormasPagoExcel($idsCab, $_SESSION['user_id']);
        $detalleFormasCobros = CobrosdetData::getAllCobrosFormasDet(implode(',', $idsCab));
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('E1:H1');
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
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
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
            $hoja->setCellValueByColumnAndRow(2, $i, FormasData::getById($pago->cfid)->cfname);
            $hoja->setCellValueByColumnAndRow(3, $i, "");
            $hoja->setCellValueByColumnAndRow(4, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
            $hoja->setCellValueByColumnAndRow(5, $i,"");
            $i++;
            $o++;
            $totalResumido = $totalResumido + $pago->fcvalor;

        }
        $hoja->setCellValueByColumnAndRow(3, $i, "Total : ");
        $hoja->setCellValueByColumnAndRow(4, $i, "$ ". number_format($totalResumido, 2, ',', '.'));

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

    } else {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabeceraUser($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
        /** DEVUELVE EL DETALLE DE LAS CABECERAS Y SU RESPECTIVO DOCUMENTO PDF */
//        ReporteCobros::getDetalleFormasPagoExcel($idsCab, $_SESSION['user_id']);
        $detalleFormasCobros = CobrosdetData::getAllCobrosFormasDet(implode(',', $idsCab));
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('E1:H1');
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
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
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
            $hoja->setCellValueByColumnAndRow(2, $i, FormasData::getById($pago->cfid)->cfname);
            $hoja->setCellValueByColumnAndRow(3, $i, "");
            $hoja->setCellValueByColumnAndRow(4, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
            $hoja->setCellValueByColumnAndRow(5, $i,"");
            $i++;
            $o++;
            $totalResumido = $totalResumido + $pago->fcvalor;

        }
        $hoja->setCellValueByColumnAndRow(3, $i, "Total : ");
        $hoja->setCellValueByColumnAndRow(4, $i, "$ ". number_format($totalResumido, 2, ',', '.'));

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
    }
} elseif ($_POST['tipo'] == 2) {
    /**===========================================================================
     * SI EL TIPO DE INFORME ES DETALLADO POR FORMA DE PAGO Y EL USUARIO ES ADMIN
     * =========================================================================== */
    if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoCobros($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
//        ReporteCobros::getDetalleFormasCobrosAdminExcel($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('A1:C1');
        $spread->getActiveSheet()->mergeCells('E1:H1');
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
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, 'IDCOBRO');
        $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
        $hoja->setCellValueByColumnAndRow(3, 3, 'FORMA');
        $hoja->setCellValueByColumnAndRow(4, 3, 'CODIGO');
        $hoja->setCellValueByColumnAndRow(5, 3, 'CLIENTE');
        $hoja->setCellValueByColumnAndRow(6, 3, 'BANCO');
        $hoja->setCellValueByColumnAndRow(7, 3, 'VENDEDOR');
        $hoja->setCellValueByColumnAndRow(8, 3, '# DOC');
        $hoja->setCellValueByColumnAndRow(9, 3, 'VALOR');
        $i = 4;
        $o = 0;
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        foreach ($detCobros as $detDat) {
            $hoja->setCellValueByColumnAndRow(1, $i, $detDat->idCobro);
            $hoja->setCellValueByColumnAndRow(2, $i, $detDat->fechaCCobros);
            $hoja->setCellValueByColumnAndRow(3, $i, $detDat->nombreformapago);
            $hoja->setCellValueByColumnAndRow(4, $i, $detDat->codigo);
            $hoja->setCellValueByColumnAndRow(5, $i, $detDat->cliente);
            $banco = '';
            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $hoja->setCellValueByColumnAndRow(6, $i, $banco);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }

            $hoja->setCellValueByColumnAndRow(7, $i, UserData::getById($detDat->usuario)->name);
            $hoja->setCellValueByColumnAndRow(8, $i, $numDoc);
            $hoja->setCellValueByColumnAndRow(9, $i, $detDat->valorCCFormas);
            $i++;
        }
//        $hoja->setCellValueByColumnAndRow(3, $i, "Total : ");
//        $hoja->setCellValueByColumnAndRow(4, $i, "$ ". number_format($totalResumido, 2, ',', '.'));

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

    } else {
        /** ======= SI EL USUARIO NO ES ADMINISTRADOR ======================== */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoCobrosNotAdmin($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
//        ReporteCobros::getDetalleFormasCobrosNotAdminExcel($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('A1:C1');
        $spread->getActiveSheet()->mergeCells('E1:H1');
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
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, 'IDCOBRO');
        $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
        $hoja->setCellValueByColumnAndRow(3, 3, 'FORMA');
        $hoja->setCellValueByColumnAndRow(4, 3, 'CODIGO');
        $hoja->setCellValueByColumnAndRow(5, 3, 'CLIENTE');
        $hoja->setCellValueByColumnAndRow(6, 3, 'BANCO');
        $hoja->setCellValueByColumnAndRow(7, 3, 'VENDEDOR');
        $hoja->setCellValueByColumnAndRow(8, 3, '# DOC');
        $hoja->setCellValueByColumnAndRow(9, 3, 'VALOR');
        $i = 4;
        $o = 0;
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        foreach ($detCobros as $detDat) {
            $hoja->setCellValueByColumnAndRow(1, $i, $detDat->idCobro);
            $hoja->setCellValueByColumnAndRow(2, $i, $detDat->fechaCCobros);
            $hoja->setCellValueByColumnAndRow(3, $i, $detDat->nombreformapago);
            $hoja->setCellValueByColumnAndRow(4, $i, $detDat->codigo);
            $hoja->setCellValueByColumnAndRow(5, $i, $detDat->cliente);
            $banco = '';
            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $hoja->setCellValueByColumnAndRow(6, $i, $banco);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }

            $hoja->setCellValueByColumnAndRow(7, $i, UserData::getById($detDat->usuario)->name);
            $hoja->setCellValueByColumnAndRow(8, $i, $numDoc);
            $hoja->setCellValueByColumnAndRow(9, $i, $detDat->valorCCFormas);
            $i++;
        }
//        $hoja->setCellValueByColumnAndRow(3, $i, "Total : ");
//        $hoja->setCellValueByColumnAndRow(4, $i, "$ ". number_format($totalResumido, 2, ',', '.'));

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
    }
} else { // TIPO 3
    /**===========================================================================
     * SI EL TIPO DE INFORME ES DETALLADO POR DOCUMENTO Y EL USUARIO ES ADMIN
     * =========================================================================== */
    if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoDocumentos($_POST);
//    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
//        ReporteCobros::getDetalleDocumentosAdminExcel($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);

        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('A1:C1');
        $spread->getActiveSheet()->mergeCells('E1:H1');
        $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
        $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
        $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spread->getActiveSheet()->getStyle('E')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
        $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
        $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
        $hoja->setCellValueByColumnAndRow(1, 2, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta']);
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, '');
        $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
        $hoja->setCellValueByColumnAndRow(3, 3, 'DOCUMENTO');
        $hoja->setCellValueByColumnAndRow(4, 3, 'TIPO DEUDA');
        $hoja->setCellValueByColumnAndRow(5, 3, 'DOC DEUDA');
        $hoja->setCellValueByColumnAndRow(6, 3, 'CODIGO');
        $hoja->setCellValueByColumnAndRow(7, 3, 'CLIENTE');
        $hoja->setCellValueByColumnAndRow(8, 3, 'VALOR');
//        $hoja->setCellValueByColumnAndRow(6, 3, 'VENDEDOR');
//        $hoja->setCellValueByColumnAndRow(7, 3, '# DOC');
//        $hoja->setCellValueByColumnAndRow(8, 3, 'VALOR');
        $i = 4;
        $o = 0;
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        $U = 1;
        foreach ($detCobros as $detDat) {
            $hoja->setCellValueByColumnAndRow(1, $i, $detDat->coid );
            $hoja->setCellValueByColumnAndRow(2, $i, $detDat->fecha);
            $hoja->setCellValueByColumnAndRow(3, $i, $detDat->numNcr);
            $hoja->setCellValueByColumnAndRow(4, $i, $detDat->tdnombre);
            $hoja->setCellValueByColumnAndRow(5, $i, $detDat->numFactura);
            $hoja->setCellValueByColumnAndRow(6, $i, $detDat->codigo);
            $hoja->setCellValueByColumnAndRow(7, $i, $detDat->cliente);
            $hoja->setCellValueByColumnAndRow(8, $i, $detDat->valor);
            $i++;
            $total += $detDat->valor;
        }
        $hoja->setCellValueByColumnAndRow(4, $i, "Total : ");
        $hoja->setCellValueByColumnAndRow(5, $i, $total);

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');


    } else {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoDocumentosNotAdmin($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
//    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
//        ReporteCobros::getDetalleDocumentosNotAdminExcel($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);

        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($_SESSION['user_id'])->em_ruc);
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
        $spread->getActiveSheet()->mergeCells('A1:C1');
        $spread->getActiveSheet()->mergeCells('E1:H1');
        $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
        $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
        $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spread->getActiveSheet()->getStyle('E')
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE );
        $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
        $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
        $hoja->setCellValueByColumnAndRow(1, 2, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta']);
        $hoja->setCellValueByColumnAndRow(5, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, '');
        $hoja->setCellValueByColumnAndRow(2, 3, 'FECHA');
        $hoja->setCellValueByColumnAndRow(3, 3, 'DOCUMENTO');
        $hoja->setCellValueByColumnAndRow(4, 3, 'CODIGO');
        $hoja->setCellValueByColumnAndRow(5, 3, 'CLIENTE');
        $hoja->setCellValueByColumnAndRow(6, 3, 'VALOR');
//        $hoja->setCellValueByColumnAndRow(6, 3, 'VENDEDOR');
//        $hoja->setCellValueByColumnAndRow(7, 3, '# DOC');
//        $hoja->setCellValueByColumnAndRow(8, 3, 'VALOR');
        $i = 4;
        $o = 0;
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        $U = 1;
        foreach ($detCobros as $detDat) {
            $hoja->setCellValueByColumnAndRow(1, $i, $u++ );
            $hoja->setCellValueByColumnAndRow(2, $i, $detDat->fecha);
            $hoja->setCellValueByColumnAndRow(3, $i, $detDat->documento);
            $hoja->setCellValueByColumnAndRow(4, $i, $detDat->codigo);
            $hoja->setCellValueByColumnAndRow(5, $i, $detDat->cliente);
            $hoja->setCellValueByColumnAndRow(6, $i, $detDat->valor);
            $i++;
            $total += $detDat->valor;
        }
        $hoja->setCellValueByColumnAndRow(4, $i, "Total : ");
        $hoja->setCellValueByColumnAndRow(5, $i, $total);

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');

    }
}
// INFORME DETALLADO
function validaDatos()
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

