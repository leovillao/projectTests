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
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/TipoOperationData.php';
//require../../ 'core/modules/index/model/FpagosData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/vwEstadoCuentaData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = "ESTADO DE CUENTA";
$GLOBALS['desde'] = $_POST['fechaDesde'];
$GLOBALS['hasta'] = $_POST['fechaHasta'];
$GLOBALS['cliente'] = $_POST['cliente'];

//$GLOBALS['corte'] = $_POST['ccdate'];

//var_dump($_POST);

class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : '';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
//            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,4,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
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
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 11); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
        if (!empty($GLOBALS['cliente'])) {
            $this->SetFont('Arial', '', 11); // titulos
            $this->Cell(100, 7, "Cliente : " . utf8_decode(PersonData::getById($GLOBALS['cliente'])->cename), 0, 1, 'L', 0, 0);
        }
//        $this->SetFont('Arial', '', 8); // titulos
//        $this->Cell(90, 4, 'Fecha de corte : ' . date("d/m/Y", strtotime($GLOBALS['corte'])), 0, 0, 'L', 0, 1);
        $this->Cell(190, 4, 'Rango de fecha : ' . date("d/m/Y", strtotime($GLOBALS['desde'])) . ' , Hasta : ' . date("d/m/Y", strtotime($GLOBALS['hasta'])), 0, 1, 'L', 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 7); // titulos

        $this->SetFillColor(192, 192, 192);
        $this->Cell(15, 6, 'Fecha', 'L,T,B,R', 0, 'C', 1, 0);
        $this->Cell(23, 6, 'Tipo', 'L,T,B,R', 0, 'C', 1, 0);
        $this->Cell(23, 6, 'Tipo Doc', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(20, 6, 'Referencia', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Cuota', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Deudor', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Acreedor', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(25, 6, 'Saldo Acumulado', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(35, 6, 'Comentario', 'T,B,R', 1, 'C', 1, 0);
    }
}

$where = 'where fecha between "' . $_POST['fechaDesde'] . '" and "' . $_POST['fechaHasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= ' and suid = ' . $_POST['sucursal'] . " ";
}


if ($_POST['orden'] == 2) {
    $where .= ' order by iddeuda,derefer,fecha asc ';
}else{
    $where .= ' order by fecha asc ';
}

function GenerateWord()
{
    //Get a random word
    $nb = rand(3, 10);
    $w = '';
    for ($i = 1; $i <= $nb; $i++) {
        $w .= chr(rand(ord('a'), ord('z')));
    }
    return $w;
}
function GenerateSentence()
{
    //Get a random sentence
    $nb = rand(1, 10);
    $s = '';
    for ($i = 1; $i <= $nb; $i++) {
        $s .= GenerateWord() . ' ';
    }
    return substr($s, 0, -1);
}

$saldos = vwEstadoCuentaData::getAllFecha($where);
//var_dump($saldos);
$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 7);
//Table with 20 rows and 4 columns
$pdf->SetWidths(array(15,23,20,23,16,16,16,25,35));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'R', 'R', 'C', 'C'));
//srand(microtime() * 1000000);
$saldoAcumulado = 0;
foreach ($saldos as $saldo) {
    $val = 0;
    $saldo1 = $saldo->factor * $saldo->valor;
    $saldoAcumulado += $saldo1;
    $deudor = 0;
    $acreedor = 0;
    if ($saldo->factor == "-1") {
        $acreedor = $saldo->valor;
    }else{
        $deudor = $saldo->valor;
    }
    //$pdf->Row(array(GenerateSentence(), GenerateSentence(), GenerateSentence(), GenerateSentence()));
    $pdf->Row(array($saldo->fecha,$saldo->tipocobro,$saldo->tipodeuda,$saldo->derefer,$saldo->decuota,$deudor,$acreedor,number_format($saldoAcumulado,2),$saldo->observa));
}
$pdf->Output();

