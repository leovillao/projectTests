<?php
session_start();
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ro_haberdesccabeceraData.php';
require '../../core/modules/index/model/ro_haberdescdetalleData.php';
require '../../core/modules/index/model/ro_periodosData.php';
require '../../core/modules/index/model/ro_empleadosData.php';
require '../../core/modules/index/model/ro_camposdefData.php';
require '../../core/modules/index/model/ro_cabtablaData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Model.php';
require '../../core/controller/FPDF/fpdf.php';
class PDF extends FPDF
{
    public function Header()
    {
        $cabecera = ro_haberdesccabeceraData::getByIdhd($_GET['id']);
        $this->SetFont('Arial','B',13); // titulos
        $this->Cell(95,6,$_SESSION['razonSocial'],0,0,'L',0,0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95,4,'Usuario :' .UserData::getById($_SESSION['user_id'])->name,0,1,'R',0,0);
        $this->Cell(193,4,'Pagina ' .$this->PageNo() . '/{nb}',0,1,'R',0,0);
        $this->SetFont('Arial','',9); // titulos
        $this->Cell(95,7,utf8_decode('Fecha Emisión :').date('d-m-Y H:i:s'),'T',0,'L',0,0);
        $this->Cell(95,7,"SMARTTAG plataforma de negocios",'T',1,'R',0,0);
        $this->SetFont('Arial','B',17); // titulos
        $this->Cell(190, 7, ro_camposdefData::getById($cabecera->cdid)->cddescrip, 0, 1, 'C', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Ln(4);
        $this->SetFillColor(192, 192, 192);
        $altoTitulo = 7;
        $this->Cell(23, $altoTitulo, 'Documento: #' . $cabecera->hdid , 0, 0, 'L', 0, 0);
        $this->Cell(53, $altoTitulo, 'Fecha: ' . $cabecera->hdfecha, 0, 0, 'C', 0, 0);
        $this->Cell(83, $altoTitulo, 'Tipo: ' . utf8_decode(ro_cabtablaData::getByTB($cabecera->tbid)->tbdescrip) , 0, 0, 'L', 0, 0);
        $this->Cell(100, $altoTitulo, 'Total: ' . $cabecera->hdtotal , 0, 1, 'L', 0, 0);
        $this->Cell(50, $altoTitulo, 'Empleado : ' . utf8_decode(ro_empleadosData::getByIdName($cabecera->emid)->emidlegal . " - " . ro_empleadosData::getByIdName($cabecera->emid)->emnombre . " " . ro_empleadosData::getByIdName($cabecera->emid)->emapellido) , 0, 0, 'L', 0, 0);
        $this->Cell(120, $altoTitulo, 'COMENTARIO : ' . $cabecera->hdobserva , 0, 1, 'C', 0, 0);
    }
}