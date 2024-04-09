<?php
//var_export($_POST);
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
require '../../core/modules/index/model/VwinfventasData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require 'CabeceraReporte.php';
$where = 'where fi_fechadoc between "' . $_POST['fechaDesde'] . '" and "' . $_POST['fechaHasta'] . '"';
if ($_POST['secuencia'] != 0) { // pto de emision
  $sec = SecuenciaData::getById($_POST['secuencia']);
  $where .= 'and fi_codestab = "' . $sec->estab . '" and fi_ptoemi = "' . $sec->emision . '"';
}
if ($_POST['estado'] == 1) { // todos exepto anulados
    $where .= ' and fi_estado <> 3';
} elseif ($_POST['estado'] == 2) { // solo anulados
  $where .= ' and fi_estado = 3';
} // todos los documentos
if (!empty($_POST['cierre'])) { // por cierre de caja
  $where .= ' and box_id = ' . $_POST['cierre'];
}
if ($_POST['cliente'] != 0) { // por cliente
  $where .= ' and ceid = ' . $_POST['cliente'];
}
if ($_POST['sucursal'] != 0) { // por cliente
  $where .= ' and sucursal_id = ' . $_POST['sucursal'];
}
if ($_POST['vendedor'] != 0) { // por vendedor
  $where .= ' and veid = ' . $_POST['vendedor'];
}
if ($_POST['ciudad'] != 0) { // por ciudad
  $where .= ' and city_id = ' . $_POST['ciudad'];
}
if ($_POST['provincia'] != 0) { // por provincia
  $where .= ' and prov_id = ' . $_POST['provincia'];
}
if ($_POST['pais'] != 0) { // por pais
  $where .= ' and pais_id = ' . $_POST['pais'];
}
$where .= " order by fi_docum asc";
$ventas = VwinfventasData::getDataDefault($where);
$secuencias = SecuenciaData::getAll();
$sucursales = SucursalData::getAll();
$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(190, 7, 'Fecha de consulta : Desde :' . $_POST['fechaDesde'] . ', Hasta : ' . $_POST['fechaHasta'], 0, 1, 'L', 0, 0);
/*=================
cabecera de reporte
=================*/
$pdf->SetFont('Arial', '', 9); // titulos
$altoTitulo = 8;
$alto = 6;
$ttIvasi = 0;
$ttivano = 0;
$ttsubt = 0;
$ttdesct = 0;
$ttiva = 0;
$tttotal = 0;
foreach ($secuencias as $secuencia) {

  if ($secuencia->coddoc == "01") {
    foreach ($sucursales as $sucursal) {

      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFont('Arial', 'B', 7); // titulos
      $pdf->SetFillColor(194,194,194);
      $pdf->Cell(189, $alto, utf8_decode("SUCURSAL " . $sucursal->suname), 'L,T,B,R', 1, 'L', 1, 0);
      $pdf->Cell(189, $alto, utf8_decode("PUNTO EMISIÓN " . $secuencia->emision), 'L,T,B,R', 1, 'C', 1, 0);
      $pdf->SetFillColor(0,0,0);
      $ttIS = 0;
      $ttIV = 0;
      $ttS = 0;
      $ttD = 0;
      $ttI = 0;
      $ttT = 0;
      $pdf->SetFillColor(227,227,227);
      $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 1, 0);
      $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 1, 0);
      $pdf->Cell(55, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Desc', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Total', 'T,B,R', 1, 'C', 1, 0);
      $pdf->SetFont('Arial', '', 7); // titulos
      foreach ($ventas as $venta) {

        $pdf->SetFont('Arial', '', 7);
        if ($sucursal->suid == $venta->sucursal_id) {

          if ($secuencia->emision == $venta->fi_ptoemi) {
            if ($venta->fi_estado != 3) {
              $pdf->SetTextColor(0, 0, 0); // color rojo
              $anulado = "";
            } else {
              $anulado = " - ANULADO";
              $pdf->SetTextColor(255, 8, 0); // color rojo
            }
            $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(55, $alto, substr(utf8_decode(ucwords(strtolower($venta->fi_er_name))) . $anulado, 0, 46), 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivasi : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivano : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_subtotal : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_desc : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_iva : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_neto : 0.00, 'T,B,R', 1, 'R', 0, 0);
            /* ======================= Totale totales ========================*/
            if ($venta->fi_estado != 3) {
              $ttIvasi = $ttIvasi + $venta->fi_ivasi;
              $ttivano = $ttivano + $venta->fi_ivano;
              $ttsubt = $ttsubt + $venta->fi_subtotal;
              $ttdesct = $ttdesct + $venta->fi_desc;
              $ttiva = $ttiva + $venta->fi_iva;
              $tttotal = $tttotal + $venta->fi_neto;
              /*===================================*/
              $ttIS = $ttIS + $venta->fi_ivasi;
              $ttIV = $ttIV + $venta->fi_ivano;
              $ttS = $ttS + $venta->fi_subtotal;
              $ttD = $ttD + $venta->fi_desc;
              $ttI = $ttI + $venta->fi_iva;
              $ttT = $ttT + $venta->fi_neto;
            }
            /*======================== Totales quiebre =======================*/
          }
        }
      } //
      $pdf->SetFillColor(180, 190, 100); // color negro
      $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
      $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
      $pdf->SetFont('Arial', 'B', 7);
      $pdf->Cell(55, $alto, 'Total sucursal ', 'T,B,R', 0, 'R', 1, 0);
      $pdf->SetFont('Arial', '', 7);
      $pdf->Cell(16, $alto, $ttIS, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttIV, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttS, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttD, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttI, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttT, 'T,B,R', 1, 'R', 1, 0);
      $pdf->SetTextColor(0, 0, 0); // color negro
    }
  }
  if ($secuencia->coddoc == "04") {
    $pdf->SetFillColor(194,194,194);
    $pdf->Cell(189, $alto, utf8_decode("NOTA DE CRÉDITO"), 'L,T,B,R', 1, 'L', 1, 0);
    foreach ($sucursales as $sucursal) {

      $pdf->SetTextColor(0, 0, 0);
      $pdf->SetFont('Arial', 'B', 7); // titulos
      $pdf->Cell(189, $alto, utf8_decode("SUCURSAL " . $sucursal->suname), 'L,T,B,R', 1, 'L', 1, 0);
      $pdf->Cell(189, $alto, utf8_decode("PUNTO EMISIÓN " . $secuencia->emision), 'L,T,B,R', 1, 'C', 1, 0);
      $pdf->SetFillColor(0,0,0);
      $ttIS = 0;
      $ttIV = 0;
      $ttS = 0;
      $ttD = 0;
      $ttI = 0;
      $ttT = 0;
      $pdf->SetFillColor(227,227,227);
      $pdf->Cell(23, $altoTitulo, 'Doc', 'L,T,B,R', 0, 'L', 1, 0);
      $pdf->Cell(15, $altoTitulo, 'Fecha', 'T,B,R', 0, 'L', 1, 0);
      $pdf->Cell(55, $altoTitulo, 'Cliente', 'T,B,R', 0, 'L', 1, 0);
//      $pdf->Cell(16, $altoTitulo, 'Exento', 'T,B,R', 0, 'C', 0, 0);
//      $pdf->Cell(16, $altoTitulo, 'Grabado', 'T,B,R', 0, 'C', 0, 0);
      $pdf->Cell(16, $altoTitulo, 'Subtotal', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Desc', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Iva', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(16, $altoTitulo, 'Total', 'T,B,R', 0, 'C', 1, 0);
      $pdf->Cell(32, $altoTitulo, 'Doc Relacionado', 'T,B,R', 1, 'C', 1, 0);
      $pdf->SetFont('Arial', '', 7); // titulos
      foreach ($ventas as $venta) {

        $pdf->SetFont('Arial', '', 7);
        if ($sucursal->suid == $venta->sucursal_id) {

          if ($secuencia->emision == $venta->fi_ptoemi && $secuencia->coddoc == "04" && $venta->fi_tipo == "04") {
            if ($venta->fi_estado != 3) {
              $pdf->SetTextColor(0, 0, 0); // color rojo
              $anulado = "";
            } else {
              $anulado = " - ANULADO";
              $pdf->SetTextColor(255, 8, 0); // color rojo
            }
            $pdf->Cell(23, $alto, $venta->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, $venta->fi_fechadoc, 'T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(55, $alto, substr(utf8_decode(ucwords(strtolower($venta->fi_er_name))) . $anulado, 0, 46), 'T,B,R', 0, 'L', 0, 0);
//            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivasi : 0.00, 'T,B,R', 0, 'R', 0, 0);
//            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_ivano : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_subtotal : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_desc : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_iva : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(16, $alto, ($venta->fi_estado != 3) ? $venta->fi_neto : 0.00, 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(32, $alto, ($venta->fi_estado != 3) ? $venta->fi_docrel : 0.00, 'T,B,R', 1, 'R', 0, 0);
            /* ======================= Totale totales ========================*/
            if ($venta->fi_estado != 3) {
              $ttIvasi = $ttIvasi + $venta->fi_ivasi;
              $ttivano = $ttivano + $venta->fi_ivano;
              $ttsubt = $ttsubt + $venta->fi_subtotal;
              $ttdesct = $ttdesct + $venta->fi_desc;
              $ttiva = $ttiva + $venta->fi_iva;
              $tttotal = $tttotal + $venta->fi_neto;
              /*===================================*/
              $ttIS = $ttIS + $venta->fi_ivasi;
              $ttIV = $ttIV + $venta->fi_ivano;
              $ttS = $ttS + $venta->fi_subtotal;
              $ttD = $ttD + $venta->fi_desc;
              $ttI = $ttI + $venta->fi_iva;
              $ttT = $ttT + $venta->fi_neto;
            }
            /*======================== Totales quiebre =======================*/
          }
        }
      } //
      $pdf->SetFillColor(180, 190, 100); // color negro
      $pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
      $pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
      $pdf->SetFont('Arial', 'B', 7);
      $pdf->Cell(55, $alto, 'Total sucursal ', 'T,B,R', 0, 'R', 1, 0);
      $pdf->SetFont('Arial', '', 7);
//      $pdf->Cell(16, $alto, $ttIS, 'T,B,R', 0, 'R', 0, 0);
//      $pdf->Cell(16, $alto, $ttIV, 'T,B,R', 0, 'R', 0, 0);
      $pdf->Cell(16, $alto, $ttS, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttD, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttI, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(16, $alto, $ttT, 'T,B,R', 0, 'R', 1, 0);
      $pdf->Cell(32, $alto, '', 'T,B,R', 1, 'R', 1, 0);
    }
  }
}
//  $pdf->Cell(16, $alto, $venta->fi_codestab.'-'.$venta->fi_ptoemi, 'T,B,R', 1, 'R', 0, 0);
$pdf->SetFillColor(180, 190, 100); // color negro
$pdf->Cell(23, $alto, '', 'L,T,B', 0, 'L', 1, 0);
$pdf->Cell(15, $alto, '', 'T,B', 0, 'L', 1, 0);
$pdf->Cell(55, $alto, 'Totales', 'T,B,R', 0, 'R', 1, 0);
//$pdf->Cell(16, $alto, $ttIvasi, 'T,B,R', 0, 'R', 0, 0);
//$pdf->Cell(16, $alto, $ttivano, 'T,B,R', 0, 'R', 0, 0);
$pdf->Cell(16, $alto, $ttsubt, 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, $ttdesct, 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, $ttiva, 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(16, $alto, $tttotal, 'T,B,R', 0, 'R', 1, 0);
$pdf->Cell(32, $alto, '', 'T,B,R', 1, 'R', 1, 0);
$pdf->Output();
