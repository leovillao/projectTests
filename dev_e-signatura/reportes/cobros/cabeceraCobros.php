<?php
session_start();
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
    $this->Cell(190,7,'INFORME DE COBROS',0,1,'C',0,0);
  }
}