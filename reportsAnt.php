<?php
//var_dump($_POST);
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require 'core/modules/index/model/SecuenciaData.php';
require 'core/modules/index/model/EmpresasData.php';
require 'core/modules/index/model/EntidadesData.php';
require 'core/modules/index/model/FilesData.php';
require 'core/modules/index/model/AnticipocabData.php';
require 'core/modules/index/model/AnticipodetData.php';
require 'core/modules/index/model/AnticipoData.php';
//require 'core/modules/index/model/BoxData.php';
require 'core/modules/index/model/PersonData.php';
require 'core/modules/index/model/RsmData.php';
require 'core/modules/index/model/TipocobroData.php';
require 'core/modules/index/model/CobroscabData.php';
require 'core/modules/index/model/CobrosdetData.php';
require 'core/modules/index/model/FormasData.php';
//require 'core/modules/index/model/FpagosData.php';
require 'core/modules/index/model/UserData.php';
require 'core/modules/index/model/ConfigurationData.php';
require 'core/controller/Executor.php';
require 'core/controller/Database.php';
require 'core/controller/Core.php';
require 'core/controller/Model.php';
require 'core/controller/Fpdf/fpdf.php';
include 'funcionesReporte.php';
include 'ReporteAnticiposFunciones.php';
if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
  $idsCab = ReporteAnticipos::getCabecera($_POST['desde'], $_POST['hasta'], $_POST['cliente']);
  if ($_POST['tipo'] == 0) { // RESUMIDO
    if ($_POST['cliente'] == 0) {
      ReporteAnticipos::getDetallesAnticipoAllDocs($idsCab, $_SESSION['user_id']);/* == REPORTE POR CLIENTE NO DETALLADO ==== */
    } else {
      ReporteAnticipos::getDetallesAnticipo($idsCab, $_SESSION['user_id'], $_POST['cliente']);/* == REPORTE POR CLIENTE NO DETALLADO ==== */
    }
  } else { // DETALLADO
    if ($_POST['cliente'] == 0){
      ReporteAnticipos::getDetallesAntDet($idsCab, $_SESSION['user_id']);
    }else{
      ReporteAnticipos::getDetallesAntDetCliente($idsCab, $_SESSION['user_id'],$_POST['cliente']);
    }
  }
}
/*else {
  $idsCab = ReporteCobros::getCabeceraUser($_POST['desde'], $_POST['hasta'], $_SESSION['user_id']);
  ReporteCobros::getDetalleFormasPago($idsCab, $_SESSION['user_id']);
}*/
/*if(){}else {
  if (UserData::getById($_SESSION['user_id'])->pfid == 1) {
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
    ReporteCobros::getDetalleFormasCobrosAdmin($detCobros, $_SESSION['user_id']);
  } else {
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
    $detCobros = ReporteCobros::getDetInfoCobros($idsCab);
    ReporteCobros::getDetalleFormasCobros($detCobros, $_SESSION['user_id']);
  }
}*/
// INFORME DETALLADO
/*function validaDatos() {
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
}*/