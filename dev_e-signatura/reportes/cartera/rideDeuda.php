<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/TipodeudaData.php';
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

require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CrucedeudasData.php';
require '../../core/modules/index/model/CruceanticiposData.php';
require '../../core/modules/index/model/DeudasData.php';

require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
//require 'cabeceraCartera.php';
require '../../core/controller/Fpdf/fpdf.php';

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial','B',13); // titulos
        $this->Cell(95,6,$_SESSION['razonSocial'],0,0,'L',0,0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95,4,'Usuario :' .UserData::getById($_SESSION['user_id'])->name,0,1,'R',0,0);
        $this->Cell(193,4,'Pagina ' .$this->PageNo() . "/{nb}",0,1,'R',0,0);
//    $this->Cell(193,4,'Pagina ' .$this->PageNo(),0,1,'R',0,0);
        $this->SetFont('Arial','',9); // titulos
        $this->Cell(95,7,utf8_decode('Fecha Emisión :').date('d-m-Y H:i:s'),'T',0,'L',0,0);
        $this->Cell(95,7,"SMARTTAG plataforma de negocios",'T',1,'R',0,0);
        $this->SetFont('Arial','B',17); // titulos
        $this->Cell(190,7,'INGRESO',0,1,'C',0,0);
    }
}
$deuda = DeudasData::getById($_GET['id']);

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(140, 6, PersonData::getById($deuda->ceid)->cename, 0, 0);
$pdf->SetFont('Arial', '', 13);
$pdf->Cell(50, 5.5, TipodeudaData::getById($deuda->tdid)->tdnombre.' # ' . $_GET['id'], 0, 1, 'R', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 5.5, 'Ruc:' . PersonData::getById($deuda->ceid)->cerut, 0, 1);
$pdf->Cell(150, 5.5, 'Fecha de Emision :' . $deuda->defecha, 0, 1, 'L', 0, 0);
//$pdf->SetFont('Arial', '', 12);
$pdf->Cell(65, 5.5, 'Observacion : ' . $deuda->deobserva, 0, 0, 'L', 0, 0);
//$pdf->Cell(65, 5.5, 'Total : $ ' . $deuda->detotal,  0, 1, 'R', 0, 0);
$pdf->SetFont('Arial', '', 10);


$pdf->ln(10);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(20, 5.5, '# DOC', 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(20, 5.5, 'FECHA', 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(25, 5.5, 'REFERENCIA', 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(62, 5.5, 'CLIENTE', 'L,T,B,R', 0, 'L', 0, 0);
$pdf->cell(64, 5.5, 'TOTAL', 'L,T,B,R', 1, 'R', 0, 0);
$totalAnt = 0;
$pdf->Cell(20, 5.5, $deuda->deid, 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(20, 5.5, $deuda->defecha, 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(25, 5.5, $deuda->derefer, 'L,T,B,R', 0, 'C', 0, 0);
$pdf->cell(62, 5.5, PersonData::getById($deuda->ceid)->cename, 'L,T,B,R', 0, 'L', 0, 0);
$pdf->cell(64, 5.5, $deuda->detotal, 'L,T,B,R', 1, 'R', 0, 0);
$pdf->ln(1);
$pdf->Cell(63, 5.5, '', '', 0, 'C', 0, 0);
$pdf->cell(63, 5.5, 'TOTAL : ', '', 0, 'R', 0, 0);
$pdf->SetFillColor(222, 222, 222);
$pdf->cell(64, 5.5, $deuda->detotal, '', 1, 'R', 1, 0);

/* Salida del Documento PDF creado por la consulta */
$pdf->Output('IngresoDeuda_#' . $_GET['id'] . '.pdf', 'I');
?>