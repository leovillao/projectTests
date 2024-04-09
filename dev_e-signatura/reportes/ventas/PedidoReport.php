<?php
session_start();
include "../../core/controller/Core.php";
include "../../core/controller/Database.php";
include "../../core/controller/Executor.php";
include "../../core/controller/Model.php";
include "../../core/modules/index/model/UserData.php";
include "../../core/modules/index/model/FilesData.php";
include "../../core/modules/index/model/PedidosData.php";
include "../../core/modules/index/model/PersonData.php";
include "../../core/modules/index/model/PedidosdetData.php";
include "../../core/modules/index/model/OperationdetData.php";
include "../../core/modules/index/model/OperationdifData.php";
include "../../core/modules/index/model/ProductData.php";
include "../../core/modules/index/model/UnitData.php";
include "../../core/modules/index/model/ConfigurationData.php";
//include 'core/controller/Fpdf/fpdf.php';
require 'CabeceraReporte.php';

$pedidosCab = PedidosData::getById($_GET['id']);
$pedidosDets = PedidosdetData::getByPedido($_GET['id']);

//$pdf->MultiCell(100, 8, json_encode($pedidosCab),0,'C',0);
/*class PDF_JavaScript extends FPDF {

  var $javascript;
  var $n_js;

  function IncludeJS($script) {
    $this->javascript=$script;
  }

  function _putjavascript() {
    $this->_newobj();
    $this->n_js=$this->n;
    $this->_out('<<');
    $this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
    $this->_out('>>');
    $this->_out('endobj');
    $this->_newobj();
    $this->_out('<<');
    $this->_out('/S /JavaScript');
    $this->_out('/JS '.$this->_textstring($this->javascript));
    $this->_out('>>');
    $this->_out('endobj');
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
      $this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
    }
  }
}


class PDF_AutoPrint extends PDF_JavaScript
{
  function AutoPrint($dialog=false)
  {
    //Open the print dialog or start printing immediately on the standard printer
    $param=($dialog ? 'true' : 'false');
    $script="print($param);";
    $this->IncludeJS($script);
  }

  function AutoPrintToPrinter($server, $printer, $dialog=false)
  {
    $script = "document.contentWindow.print();";
    $this->IncludeJS($script);
  }
}*/


// $pdf = new FPDF($orientation='P',$unit='mm', array(80,250));
$pdf = new PDF($orientation = 'P', $unit = 'mm', array(210, 270));
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(190, 6, 'FECHA :' . date('d-m-Y H:i:s'), 0, 1, 'R', 0, 1);
$pdf->SetFont('Arial', 'B', 16);
//$pdf->Cell(180, 8, 'REPORTE DE PEDIDOS', 0, 1, 'C', 0, 1);
$pdf->Cell(180, 8, 'PEDIDO # ' . $pedidosCab->peid, 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(115, 5, 'CLIENTE : ' . PersonData::getById($pedidosCab->ceid)->cerut . ' - ' . PersonData::getById($pedidosCab->ceid)->cename, 0, 0, 'L', 0, 1);
//$pdf->Cell(50, 5, 'NUMERO DE PRODUCTOS : ' .count($pedidosDets), 0, 1, 'L', 0, 1);
$pdf->Cell(50, 5, 'FECHA DE PEDIDO : ' . $pedidosCab->pefecha, 0, 1, 'L', 0, 1);
$pdf->Cell(50, 5, utf8_decode('OBSERVACIÓN : ' . $pedidosCab->peobserva), 0, 0, 'L', 0, 1);
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(10, 5, '#', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(30, 5, 'CODIGO', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(65, 5, utf8_decode('DESCRIPCIÓN'), 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'UNIDAD', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'CANTIDAD', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'PRECIO', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, 'TOTAL', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(5, 5, '', 'T,R,L,B', 1, 'C', 0, 1);

$pdf->SetFont('Arial', '', 9);
$i = 1;
foreach ($pedidosDets as $pedidosDet) {

  $pdf->Cell(10, 5, $i , 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(30, 5, ProductData::getById($pedidosDet->itid)->itcodigo, 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(65, 5, ProductData::getById($pedidosDet->itid)->itname, 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, UnitData::getById($pedidosDet->unid_dig)->undescrip, 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, $pedidosDet->pdcantot_dig, 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, $pedidosDet->pdpvp, 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(20, 5, number_format($pedidosDet->pdcantot_dig * $pedidosDet->pdpvp, 5), 'T,R,L,B', 0, 'C', 0, 1);
  $pdf->Cell(5, 5, '', 'T,R,L,B', 1, 'C', 0, 1);
  $tt = $tt + $pedidosDet->pdcantot_dig;
  $i++;
}
$pdf->Cell(10, 5, '', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(30, 5, '', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(85, 5, 'TOTAL DE ITEM', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(20, 5, $tt, 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, '', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(20, 5, '', 'T,R,L,B', 0, 'C', 0, 1);
$pdf->Cell(5, 5, '', 'T,R,L,B', 1, 'C', 0, 1);
//$pdf->AutoPrint(true);
$pdf->Output();
