<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/DeudasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/VendedorData.php';
require '../../core/modules/index/model/VecinoData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/ProveeData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/RecetacabData.php';
require '../../core/modules/index/model/RecetadetData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/TipoOperationData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require 'PDF_MC_Table.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];

class pdfClase extends PDF_MC_Table
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 11); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
    }
}

$pdf=new pdfClase();
//$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial','',14);
$pdf->Ln(2);
$pdf->Cell(180, 4, 'RECETA # : ' . $_GET['id'], 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8); // titulos
$pdf->Ln(5);
$pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
$recetaCab = RecetacabData::getById($_GET['id']);
$pdf->SetFont('Arial', '', 9); // titulos
$pdf->Cell(30, 5, 'PRODUCTO : ', 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, ProductData::getById($recetaCab->itid)->itname, 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, 'CANTIDAD : ', 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, $recetaCab->rcant, 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, 'UNIDAD : ', 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, UnitData::getById($recetaCab->rcund)->undescrip, 0, 1, 'L', 0, 1);
$pdf->Cell(40, 5, strtoupper('Costo no declarado : '), 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, $recetaCab->rcpordcla ." % ", 0, 0, 'L', 0, 1);
$pdf->Cell(80, 5, strtoupper('Rentabilidad para estimar PVP ideal : '), 0, 0, 'L', 0, 1);
$pdf->Cell(30, 5, $recetaCab->rcporpvp ." % ", 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 12); // titulos
$pdf->Ln(5);
$pdf->Cell(180, 4, "INGREDIENTES", 0, 1, 'C', 0, 1);
$pdf->SetFont('Arial', '', 8); // titulos
//
//$pdf->Cell(60, 4, "Producto", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(30, 4, "CANT", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "Und", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "Bodega", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "% Aprovechado", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "% Desperdicio", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "Reemp", "T,B,L,R", 0, 'C', 0, 1);
//$pdf->Cell(15, 4, "Oblig", "T,B,L,R", 1, 'C', 0, 1);
//Table with 20 rows and 4 columns
$pdf->Ln(5);
$pdf->SetWidths(array(60,15,15,20,22,22,15,15));
srand(microtime()*1000000);
$pdf->Row(array("Producto","Cant","Und","Bodega","% Aprovechado","% Desperdicio","Reemp","Oblig"));
$recetaDets = RecetadetData::getByIdReceta($_GET['id']);
foreach ($recetaDets as $recetaDet){
    $reemp = "no";
    $oblig = "no";
    if ($recetaDet->rdremp == 1){
        $reemp = "si";
    }
    if ($recetaDet->rdoblig == 1){
        $oblig = "si";
    }
    $producto = ProductData::getById($recetaDet->itid)->itcodigo.' / '.ProductData::getById($recetaDet->itid)->itname;
    $pdf->Row(array($producto,$recetaDet->rdcant,UnitData::getById($recetaDet->rdunit)->undescrip,BodegasData::getById($recetaDet->boid)->bodescrip,$recetaDet->rdporap,$recetaDet->rdporde,$reemp,$oblig));
}

$pdf->Output();
