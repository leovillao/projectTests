<?php
switch ($_POST['reporte']) {
  case 1:
    if ($_POST['tipoFact'] == 1) {
      require_once 'reporteEstandarDocumento.php';
    } else {
      require_once 'reporteEstandarProducto.php';
    }
    break;
  case 2:
    if ($_POST['tipoFact'] == 1) {
      require_once 'reporteCierreDocumento.php';
    } else {
      require_once 'reporteCierreProducto.php';
    }
    break;
  case 3:
    require_once 'reporteAuto.php';
    break;
  case 4:
    require_once 'reporteClasificacionProductos.php';
    break;
  case 5:
    if ($_POST['tipoFact'] == 1) {
      require_once 'reporteVendedorDocumento.php';
    } else {
      require_once 'reporteVendedor.php';
    }
    break;
  case 6:
    if ($_POST['tipoFact'] == 1) {
      require_once 'reporteNcrDocumento.php';
    } else {
      require_once 'reporteNcrProducto.php';
    }
    break;
}
