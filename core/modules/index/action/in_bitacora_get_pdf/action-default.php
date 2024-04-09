<?php

class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, 'Control de Versiones - SMARTTAG', 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :', 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, 'Fecha Emisión :' . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "E-SIGNATURA", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, 'Consulta Bitacora', 0, 1, 'C', 0, 0);
        $this->Ln(2);

        $this->SetFont('Arial', 'B', 10); // titulos
        $this->Cell(20, 5, 'USUARIO', 1, 0, 'C', 0, 0);
        $this->Cell(20, 5, 'FECHA', 1, 0, 'C', 0, 0);
        $this->Cell(130, 5, 'ACCION', 1, 0, 'C', 0, 0);
        $this->Cell(20, 5, 'PAGINA', 1, 1, 'C', 0, 0);
    }
}

class PDFF extends PDF
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

$where = ' where date(bicreate_at) between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '" ';

if (!empty($_POST['usuario'])) {
    $where .= ' and user_id = ' . $_POST['usuario'];
}

$pdf = new PDFF();
// Primera página
$pdf->AddPage();
$pdf->AliasNbPages();
//ob_end_clean();
$pdf->SetFont('Arial', '', 9);
$bicatoras = BitacoraData::getAllForWhere($where);
$pdf->SetWidths(array(20, 20, 130, 20));
$pdf->SetAligns(array('C', 'C', 'L', 'C'));
$pdf->SetFont('Arial', '', 8);
foreach ($bicatoras as $bitacora) {
    $pdf->Row(array(
        UserData::getById($bitacora->user_id)->usr_nombre,
        $bitacora->bicreate_at,
        $bitacora->biaccion,
        $bitacora->bipage,
    ));
}
$pdf->Output('BitacoraPdf.pdf', 'D');
