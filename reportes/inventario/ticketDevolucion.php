<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';
$facturaCab = FilesData::getByIdFiles($_GET['id']);
$operationsDet = OperationdetData::getFileId($_GET["id"]);
$operationsDif = OperationdifData::getFileId($_GET["id"]);
//$iva_val = ConfigurationData::getByPreffix("imp-val")->val;
$user = UserData::getById($_SESSION['user_id']); // usuario
$empresa = EmpresasData::getById(1); // Datos empresas
$razonSocial = $empresa->em_razon;
$nombreComercial = $empresa->em_comercial;
$dirMatriz = $empresa->em_dirmatriz;
$microEmpresa = $empresa->micro_emp;
$agentRetencion = $empresa->agent_ret;
$expHabitual = $empresa->exp_hab;


class PDF_JavaScript extends FPDF {

  protected $javascript;
  protected $n_js;

  function IncludeJS($script, $isUTF8 = false) {
    if (!$isUTF8)
      $script = utf8_encode($script);
    $this->javascript = $script;
  }

  function _putjavascript() {
    $this->_newobj();
    $this->n_js = $this->n;
    $this->_put('<<');
    $this->_put('/Names [(EmbeddedJS) ' . ($this->n + 1) . ' 0 R]');
    $this->_put('>>');
    $this->_put('endobj');
    $this->_newobj();
    $this->_put('<<');
    $this->_put('/S /JavaScript');
    $this->_put('/JS ' . $this->_textstring($this->javascript));
    $this->_put('>>');
    $this->_put('endobj');
  }

  function _putresources() {
    parent::_putresources();
    if (!empty($this->javascript)) {
      $this->_putjavascript();
    }
  }

  function _putcatalog() {
    parent::_putcatalog();
    if (!empty($this->javascript)) {
      $this->_put('/Names <</JavaScript ' . ($this->n_js) . ' 0 R>>');
    }
  }
}


class PDF_AutoPrint extends PDF_JavaScript {
  function AutoPrint($dialog = false) {
    //Open the print dialog or start printing immediately on the standard printer
    $param = ($dialog ? 'true' : 'false');
    $script = "print($param);";
    $this->IncludeJS($script);
  }

  function AutoPrintToPrinter($printer = '') {
    // Open the print dialog
    if ($printer) {
      $printer = str_replace('\\', '\\\\', $printer);
      $script = "var pp = getPrintParams();";
      $script .= "pp.interactive = pp.constants.interactionLevel.full;";
      $script .= "pp.printerName = '$printer'";
      $script .= "print(pp);";
    } else
      $script = 'print(true);';
    $this->IncludeJS($script);
  }
}


// $pdf = new FPDF($orientation='P',$unit='mm', array(80,250));
$pdf = new PDF_AutoPrint($orientation = 'P', $unit = 'mm', array(80, 420));
$pdf->AddPage();
$pdf->setX(3);
$pdf->SetFont('Arial', 'B', 16);    //Letra Arial, negrita (Bold), tam. 20
//$pdf->Cell(75, 5, $nombreComercial, 0, 1, 'C', 0, 1);
$pdf->MultiCell(75, 4, utf8_decode($nombreComercial), 0, 'C', 0);
$pdf->SetFont('Arial', 'B', 11);    //Letra Arial, negrita (Bold), tam. 20
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 4, $razonSocial, 0, 1, 'C', 0, 1);
$pdf->Cell(60, 4, utf8_decode('Dirección : ' . $dirMatriz), 0, 1, 'C', 0, 1);
if ($agentRetencion == 1) {
  $pdf->Cell(60, 4, utf8_decode('Resolución : AGENTE DE RETENCIÓN'), 0, 1, 'C', 0, 1);
}
if ($microEmpresa == 1) {
  $pdf->Cell(60, 4, utf8_decode("CONTRIBUYENTE RÉGIMEN MICROEMPRAS"), 0, 1, 'C', 0, 1);
}
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 13);    //Letra Arial, negrita (Bold), tam. 20
//$pdf->SetFont('Courier', 'B', 19);    //Letra Arial, negrita (Bold), tam. 20
//$pdf->Cell(60, 5, $numeroFactura, 0, 1, 'R');
///** */
//$pdf->Ln(3);
$pdf->setX(6);
$pdf->Cell(60, 4, 'Nota de Credito # : ', 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 15);    //Letra Arial, negrita (Bold), tam. 20
$pdf->SetTextColor(207, 37, 0);
$pdf->Cell(60, 5, $facturaCab->fi_docum, 0, 1, 'R', 0, 1);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Ln();
$pdf->setX(6);
$pdf->Cell(60, 4, 'Documento Relacionado : # ' . $facturaCab->fi_docrel, 0, 1,'L', 0,1);
$pdf->SetFont('Arial', '', 7);    //Letra Arial, negrita (Bold), tam. 20
$pdf->MultiCell(60, 4, $facturaCab->fi_claveacceso, 0, 'R', 0);
if ($facturaCab->fi_fecauto) {
  $pdf->MultiCell(60, 4, 'Fecha Autorizacion :' . $facturaCab->fi_fecauto, 0, 'R', 0);
} else {
  $pdf->MultiCell(60, 4, '', 0, 'R', 0);
//  MultiCell(float w, float h, string txt [, mixed border [, string align [, boolean fill]]])
}
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->setX(6);
$pdf->Cell(60, 4, 'Cliente : ' . utf8_decode(PersonData::getById($facturaCab->ceid)->cename), 0, 1, 'L', 0, 1);
$pdf->setX(6);
$pdf->MultiCell(60, 4, utf8_decode('Dirección : ') . utf8_decode(PersonData::getById($facturaCab->ceid)->ceaddress1), 0, 'L', 0);
$pdf->setX(6);
if (PersonData::getById($facturaCab->ceid)->cephone1 != "null") {
  $pdf->MultiCell(60, 4, 'Telefono : ' . utf8_decode(PersonData::getById($facturaCab->ceid)->cephone1), 0, 'L', 0);
} else {
  $pdf->MultiCell(60, 4, 'Telefono : ', 0, 'L', 0);
}
$pdf->setX(6);
$pdf->Ln(2);
$pdf->Cell(5, 4, '', 0, 1);
$pdf->setX(6);
$pdf->Cell(3, 3, '--------------------------------------------------------------', 0, 1);
$pdf->setX(6);
$pdf->Cell(25, 5, 'PRODUCTO', 0, 0, 'L', 0, 1);
$pdf->Cell(15, 5, 'CANT', 0, 0, 'C', 0, 1);
$pdf->Cell(15, 5, 'PVP', 0, 0, 'C', 0, 1);
$pdf->Cell(15, 5, 'TOTAL', 0, 1, 'C', 0, 1);
$pdf->setX(6);
$pdf->Cell(3, 3, '--------------------------------------------------------------', 0, 1);
$pdf->SetFont('Arial', '', 7);
$pdf->Ln(2);
$productos = getDetalleOpe($operationsDet, $operationsDif);
//$pdf->MultiCell(60, 4, var_export($productos), 0, 'R', 0);
for ($i = 0; $i < count($productos); $i++) {
  $pdf->setX(6);
  if ($productos[$i]['iva'] == 1) {
    $totaIva = $totaIva + $productos[$i]['total'];
  } else {
    $totanIva = $totanIva + $productos[$i]['total'];
  }
//  $pdf->Cell(25, 3, $productos[$i]['name'], 0, 0, 'L', 0, 1);
  $pdf->MultiCell(60, 4, $productos[$i]['name'], 0, 'L', 0);
  $pdf->Cell(35, 3, $productos[$i]['cantidad'], 0, 0, 'R', 0, 1);
  $pdf->Cell(15, 3, $productos[$i]['pvp'], 0, 0, 'C', 0, 1);
  $pdf->Cell(15, 3, number_format($productos[$i]['pvp'] * $productos[$i]['cantidad'], 5), 0, 1, 'C', 0, 1);
  $total = $total + $productos[$i]['total'];
  $ivaValor = $ivaValor + $productos[$i]['ivaval'];
}
$pdf->setX(6);
$pdf->Cell(3, 4, '--------------------------------------------------------------------------------', 0, 1);
$pdf->setX(6);
$pdf->Cell(55, 3, "SUBTOTAL : ", 0, 0, 'R', 0, 1);
$pdf->Cell(13, 3, number_format($total, 5), 0, 1, 'R', 0, 1);
$pdf->setX(6);
$pdf->Cell(55, 3, "GRABADO : ", 0, 0, 'R', 0, 1);
$pdf->Cell(13, 3, number_format($totaIva, 5), 0, 1, 'R', 0, 1);
$pdf->setX(6);
$pdf->Cell(55, 3, "EXENTO : ", 0, 0, 'R', 0, 1);
$pdf->Cell(13, 3, number_format($totanIva, 5), 0, 1, 'R', 0, 1);
$pdf->setX(6);
$pdf->Cell(55, 3, "IVA : ", 0, 0, 'R', 0, 1);
$pdf->Cell(13, 3, number_format($ivaValor, 5), 0, 1, 'R', 0, 1);
$pdf->setX(6);
$pdf->Cell(55, 3, "TOTAL : ", 0, 0, 'R', 0, 1);
$pdf->Cell(13, 3, number_format($totaIva + $totanIva + $ivaValor, 5), 0, 1, 'R', 0, 1);
$pdf->AutoPrint(true);
$pdf->output();
function getDetalleOpe($operationsDet, $operationsDif) {
  $arrayProduct = array();
  if ($operationsDet != null) {
    foreach ($operationsDet as $operationDet) {
      $iniva = (ProductData::getById($operationDet->itid)->itin_iva == 1) ? '**' : '';
      $arraydet = array(
              "codigo" => ProductData::getById($operationDet->itid)->itcodigo,
              "name" => ProductData::getById($operationDet->itid)->itname . ' ' . $iniva,
              "cantidad" => $operationDet->odtcandig,
              "pvp" => $operationDet->odpvp,
              "total" => $operationDet->odbruta,
              "ivaval" => $operationDet->odiva,
              "iva" => ProductData::getById($operationDet->itid)->itin_iva,
      );
      array_push($arrayProduct, $arraydet);
    }
  }
  if ($operationsDif != null) {
    foreach ($operationsDif as $operationDif) {
      $iniva = (ProductData::getById($operationDif->itid)->itin_iva == 1) ? '**' : '';
      $arraydif = array(
              "codigo" => ProductData::getById($operationDif->itid)->itcodigo,
              "name" => ProductData::getById($operationDif->itid)->itname . ' ' . $iniva,
              "cantidad" => $operationDif->odtcandig,
              "pvp" => $operationDif->odpvp,
              "total" => $operationDif->odbruta,
              "ivaval" => $operationDif->odiva,
              "iva" => ProductData::getById($operationDif->itid)->itin_iva,
      );
      array_push($arrayProduct, $arraydif);
    }
  }
  return $arrayProduct;
}