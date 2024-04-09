<?php
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
//include 'cabeceraCartera.php';
include 'funcionesReporte.php';

require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . "/{nb}", 0, 1, 'R', 0, 0);
//    $this->Cell(193,4,'Pagina ' .$this->PageNo(),0,1,'R',0,0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
    }
}

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
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }

        ReporteCobros::getDetalleFormasPago($idsCab, $_SESSION['user_id']);
    } else {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabeceraUser($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
        /** DEVUELVE EL DETALLE DE LAS CABECERAS Y SU RESPECTIVO DOCUMENTO PDF */
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }
        ReporteCobros::getDetalleFormasPago($idsCab, $_SESSION['user_id']);
    }
} elseif ($_POST['tipo'] == 2) {
    /**===========================================================================
     * SI EL TIPO DE INFORME ES DETALLADO POR FORMA DE PAGO Y EL USUARIO ES ADMIN
     * =========================================================================== */
    if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }
        $detCobros = ReporteCobros::getDetInfoCobros($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
        ReporteCobros::getDetalleFormasCobrosAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
    } else {
        /** ======= SI EL USUARIO NO ES ADMINISTRADOR ======================== */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoCobrosNotAdmin($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
        ReporteCobros::getDetalleFormasCobrosNotAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
    }
} else {
    /**===========================================================================
     * SI EL TIPO DE INFORME ES DETALLADO POR DOCUMENTO Y EL USUARIO ES ADMIN
     * =========================================================================== */
    if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoDocumentos($_POST['desde'], $_POST['hasta']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
        ReporteCobros::getDetalleDocumentosAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
    } else {
        /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
        $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
        if (!$idsCab) {
            $error = "No hay informacion en rangos seleccionados";
            if ($error != '' || !isset($_SESSION)) {
                print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
            }
        }
        /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
        $detCobros = ReporteCobros::getDetInfoDocumentosNotAdmin($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
        /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
        ReporteCobros::getDetalleDocumentosNotAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
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

