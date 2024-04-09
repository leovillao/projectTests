<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require 'core/modules/index/model/SecuenciaData.php';
require 'core/modules/index/model/CobroscabData.php';
require 'core/modules/index/model/AnticipocabData.php';
require 'core/modules/index/model/FormasData.php';
require 'core/modules/index/model/DocumData.php';
require 'core/modules/index/model/CobrosdetData.php';
require 'core/modules/index/model/CobrostData.php';
require 'core/modules/index/model/DeudasData.php';
require 'core/modules/index/model/EmpresasData.php';
require 'core/modules/index/model/EntidadesData.php';
require 'core/modules/index/model/FilesData.php';
//require 'core/modules/index/model/BoxData.php';
require 'core/modules/index/model/PersonData.php';
require 'core/modules/index/model/RsmData.php';
//require 'core/modules/index/model/TData.php';
//require 'core/modules/index/model/FpagosData.php';
require 'core/modules/index/model/UserData.php';
require 'core/modules/index/model/ConfigurationData.php';
require 'core/controller/Executor.php';
require 'core/controller/Database.php';
require 'core/controller/Core.php';
require 'core/controller/Model.php';
require 'core/controller/Fpdf/fpdf.php';
if (isset($_POST)) {
  $error = validaDatos();
  if ($error != '') {
    print_r('
<script>
alert("' . $error . '")
window.close();
</script>
');
  } else {
    if ($_POST['tipo'] == 1) {
      tipoOne($_POST['desde'], $_POST['hasta'], $_POST['cliente']); //Resumido por sucursal y fecha
    } elseif ($_POST['tipo'] == 2) {
      pdfDetalle($_POST['desde'], $_POST['hasta'], $_POST['tipo']); //Detallado por sucursal y fecha
    }
  }
}


function tipoOne($desde, $hasta, $cliente)
{
  $documentosDeudas = DeudasData::getDeudasDocs($cliente, $desde, $hasta);
  $documentosCobros = CobroscabData::getCobrosForDay($cliente, $desde, $hasta);

  $ar_deudas = object_to_array($documentosDeudas);
  $ar_cobros = object_to_array($documentosCobros);

  $pdf = new FPDF();
  $pdf->AddPage();
  $pdf->SetFont('Arial', 'B', 16);
  $pdf->Cell(190, 5, "ESTADO DE CUENTA", 0, 1, 'C', 0, 1);
  $pdf->SetFont('Arial', '', 8);
  $pdf->Ln(3);
  $pdf->Cell(98, 4, utf8_decode('Fecha de emisión : ') . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
  $pdf->Cell(98, 4, 'Cliente : ' . PersonData::getById($_POST['cliente'])->cename, 0, 1, 'L', 0, 1);
  $pdf->Cell(190, 4, 'Periodo : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'L', 0, 1);
  $pdf->Ln(5);
  $pdf->Cell(190, 1, "", 'T', 1, 'L', 0, 1);
  $pdf->Cell(18, 4, "Fecha", 0, 0, 'C', 0, 1);
  $pdf->Cell(60, 4, "Cliente", 0, 0, 'L', 0, 1);
  $pdf->Cell(16, 4, "Tipo", 0, 0, 'C', 0, 1);
  $pdf->Cell(16, 4, "Numero", 0, 0, 'C', 0, 1);
  $pdf->Cell(16, 4, "Tipo", 0, 0, 'C', 0, 1);
  $pdf->Cell(16, 4, "Numero", 0, 0, 'C', 0, 1);
  $pdf->Cell(16, 4, utf8_decode("Débito"), 0, 0, 'L', 0, 1);
  $pdf->Cell(16, 4, utf8_decode("Crédito"), 0, 0, 'L', 0, 1);
  $pdf->Cell(16, 4, "Saldo", 0, 1, 'L', 0, 1);
  $pdf->Cell(190, 1, "", 'B', 1, 'L', 0, 1);
  $pdf->Ln(5);

  foreach ($documentosDeudas as $documentosDeuda) {
    //===== Devuelve el indice donde se encuentra el parametro buscado =======
    $pdf->Cell(18, 4, $documentosDeuda->defecha, 0, 0, 'C', 0, 1);
    $pdf->Cell(60, 4, PersonData::getById($documentosDeuda->ceid)->cename, 0, 0, 'L', 0, 1);
    $pdf->Cell(16, 4, DocumData::getById($documentosDeuda->tdid)->name, 0, 0, 'C', 0, 1);
    $pdf->Cell(16, 4, $documentosDeuda->derefer, 0, 0, 'C', 0, 1);
    $pdf->Cell(16, 4, "", 0, 0, 'C', 0, 1);
    $pdf->Cell(16, 4, "", 0, 0, 'C', 0, 1);
    $pdf->Cell(16, 4, $documentosDeuda->detotal, 0, 0, 'L', 0, 1);
    $pdf->Cell(16, 4, "", 0, 0, 'L', 0, 1);
    $pdf->Cell(16, 4, $documentosDeuda->desaldo, 0, 1, 'L', 0, 1);

    $ar_indices = array_keys(array_column($ar_cobros, 'deid'), $documentosDeuda->deid);
    $posSaldo = 0;
    for ($i = 0; $i < count($ar_indices); $i++) {
      $k = $ar_indices[$i];

      if ($posSaldo == 0){
        $posSaldo = $documentosDeuda->detotal - $posSaldo - $ar_cobros[$k]['cdvalor'] ;
      }else{
        $posSaldo = $posSaldo - $ar_cobros[$k]['cdvalor'];
      }

      $pdf->Cell(18, 4, CobroscabData::getById($ar_cobros[$k]['coid'])->cofecha, 0, 0, 'C', 0, 1);
      $pdf->Cell(60, 4, "", 0, 0, 'L', 0, 1);
      $pdf->Cell(16, 4, "", 0, 0, 'C', 0, 1);
      $pdf->Cell(16, 4, "", 0, 0, 'C', 0, 1);
      $pdf->Cell(16, 4, FormasData::getById(CobroscabData::getById($ar_cobros[$k]['coid'])->tcid)->cfname, 0, 0, 'C', 0, 1);
      $pdf->Cell(16, 4, $ar_cobros[$k]['coid'], 0, 0, 'C', 0, 1);
      $pdf->Cell(16, 4, "", 0, 0, 'L', 0, 1);
      $pdf->Cell(16, 4, $ar_cobros[$k]['cdvalor'], 0, 0, 'L', 0, 1);
      $pdf->Cell(16, 4, $posSaldo , 0, 1, 'L', 0, 1);
    }
  }

  $pdfile = $pdf->Output();
  return $pdfile;
}

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
  if (empty($_POST['tipo'])) {
    $msj = "Debe seleccionar tipo de reporte";
  }
  if (empty($_POST['cliente'])) {
    $msj = "Debe seleccionar cliente";
  }
  return $msj;
}


function object_to_array($array)
{
  $foundItems = array();
  foreach ($array as $item) {
    array_push($foundItems, (array)$item);
  }
  return $foundItems;
}


function validaDatosArrays($ar_deudas, $ar_cobros)
{
  foreach ($ar_deudas as $t => $value) {
    $keypromo = array_keys(array_column($ar_cobros, 'product_id'), $value['id']);
    if ($keypromo) {
      $nkeypromo = $keypromo[0];
      $nlprecio = $ar_deudas[$t]['price_out'];
      $ar_deudas[$t]['price_out'] = 1;
      $ar_deudas[$t]['cooked_status'] = 2;
    }
  }
  return $ar_deudas;
}

/*function seekpreciohappy()
{
    $promociones = PreciosData::getProductPromo();
    $Apromocion = object_to_array($promociones);
    return $Apromocion;
}*/