<?php
set_time_limit('60');
ini_set('memory_limit', "-1");
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
//require 'core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipocobroData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';


class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : '';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
//            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w, 4, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

class PDF extends PDF_MC_Table
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(177, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(275, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(170, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(100, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(275, 7, 'FICHA DE CLIENTES', 0, 1, 'C', 0, 0);

        $this->SetFont('Arial', '', 6);
        $this->Cell(25, 4, 'RUC / C.I.', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(40, 4, 'NOMBRE', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(40, 4, 'NOMBRE COMERCIAL', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(50, 4, 'DIRECION', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(45, 4, 'CORREO', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(28, 4, 'TELEFONO', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(15, 4, 'ETIQUETA', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(17, 4, 'SUBETIQUETA', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(17, 4, 'ESTADO', 'T,B,L,R', 1, 'C', 0, 1);
    }
}

$pdf = new PDF();

$pdf->AddPage('L', 'A4', '0');
$pdf->AliasNbPages();

$pdf->SetWidths(array(25, 40, 40, 50, 45, 28, 15, 17, 17));
$pdf->SetAligns(array('L', 'L', 'L', 'L', 'L', 'C', 'C','C','C'));
$clientes = PersonData::getAllDataClientes();
foreach ($clientes as $cliente) {
    $pdf->Row(
        array(
            $cliente->cerut,
            utf8_decode($cliente->cename),
            utf8_decode($cliente->cename_com),
            utf8_decode($cliente->ceaddress1) . ' - ' . utf8_decode($cliente->ceaddress2),
            $cliente->ceemail1 . ' - ' . $cliente->ceemail2 . ' - ' . $cliente->ceemail3,
            $cliente->cephone1 . ' - ' . $cliente->cephone2,
            $cliente->etiqueta,
            $cliente->subetiqueta,
            $cliente->estado,
        )
    );
}

$pdf->Output();


