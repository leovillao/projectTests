<?php
//var_dump($_POST);
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
//require 'core/modules/index/model/BoxData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/FormasData.php';
//require 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
//require '../../core/controller/Fpdf/fpdf.php';
include 'cabeceraCobros.php';
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
    ReporteCobros::getDetalleFormasPago($idsCab, $_SESSION['user_id']);

  } else {
    /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , SE VALIDA EL PERFIL */
    $idsCab = ReporteCobros::getCabeceraUser($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
    /** DEVUELVE EL DETALLE DE LAS CABECERAS Y SU RESPECTIVO DOCUMENTO PDF */
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
    $detCobros = ReporteCobros::getDetInfoCobros($_POST['desde'], $_POST['hasta']);
    /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
    ReporteCobros::getDetalleFormasCobrosAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
  } else {
    /** ======= SI EL USUARIO NO ES ADMINISTRADOR ======================== */
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
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
    /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
    $detCobros = ReporteCobros::getDetInfoDocumentos($_POST['desde'], $_POST['hasta']);
//    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
    /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
    ReporteCobros::getDetalleDocumentosAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
  } else {
    /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
    /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
    $detCobros = ReporteCobros::getDetInfoDocumentosNotAdmin($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
//    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
    /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
    ReporteCobros::getDetalleDocumentosNotAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
  }
}
// INFORME DETALLADO
function validaDatos() {
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

