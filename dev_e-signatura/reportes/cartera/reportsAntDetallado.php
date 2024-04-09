<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/TipocobroData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/vWFormasCobrosDetalleData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];
$GLOBALS['desde'] = $_POST['desde'];
$GLOBALS['hasta'] = $_POST['hasta'];

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
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . "/{nb}", 0, 1, 'R', 0, 0);
//    $this->Cell(193,4,'Pagina ' .$this->PageNo(),0,1,'R',0,0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(90, 6, 'ANTICIPOS DETALLADO', 0, 0, 'C', 0, 1);
        $this->Cell(100, 6, 'Rango de fechas , Desde : ' . $GLOBALS['desde'] . ", Hasta : " . $GLOBALS['hasta'], 0, 1, 'R', 0, 1);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(10, 6, 'Num ', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(17, 6, 'Fecha', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(20, 6, 'Tipo', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(23, 6, 'Numero', 'T,B,L,R', 0, 'C', 0, 1);
        $this->Cell(60, 6, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(20, 6, 'Anticipo', 'T,B,L,R', 0, 'R', 0, 1);
        $this->Cell(20, 6, 'Abono', 'T,B,L,R', 0, 'R', 0, 1);
        $this->Cell(20, 6, 'Saldo', 'T,B,L,R', 1, 'R', 0, 1);
    }
}

$fuentes = FData::fuentesPdf();
$desde = $_POST['desde'];
$hasta = $_POST['hasta'];
$where = " where DATE(anfecha) >= \"$desde\" and DATE(anfecha) <= \"$hasta\"  ";
$pdf = new PDF();
//$pdf->AddPage();
$pdf->AliasNbPages();
if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= ' and a.suid = ' . $_POST['sucursal'] . " ";
}
if ($_POST['etiquetac'] != 0) { // zoid
    $where .= ' and c.setq_id = ' . $_POST['etiquetac'] . " ";
}
if ($_POST['tipoAnticipo'] != 0) { // zoid
    $where .= ' and a.taid = ' . $_POST['tipoAnticipo'] . " ";
}
$t1 = 10;
$t2 = 17;
$t3 = 23;
$t4 = 60;
$t5 = 20;
$t6 = 20;
$t7 = 20;
$pdf->SetFont('Arial', 'B', $fuentes["titulo"]);
$pdf->Ln(5);

$where .= " and anestado = 1 order by a.tcid,c.setq_id ,a.anfecha,anrefer asc ";
$anticipos = AnticipocabData::getByAllFecha($where);
//echo '<pre>';
//var_dump($anticipos);
//echo '</pre>';
$pdf->Ln(5);
$cliente = 0;
$totalc = 0;
$controw = 0;
$pdf->SetFont('Arial', '', $fuentes["detalle"]);
$pdf->SetWidths(array(10, 17, 20, 23, 60, 20, 20, 20));
$pdf->SetAligns(array('C', 'C', 'C', 'C', 'L', 'C', 'R', 'R'));
foreach ($anticipos as $anticipo) {
    if ($controw == 0) {
        $pdf->AddPage();
    }
    if ($anticipo->idetiqueta != $idetiqueta) {
        $pdf->SetFont('Arial', 'B', 9); // titulos
        if ($tanvalor != 0) {
            $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($tanvalor)), '', 0, 'R', 0, 0);
            $pdf->Cell(20, 6, "$ " . FData::formatoNumeroReportes($tanaplica), '', 0, 'R', 0, 0);
            $pdf->Cell(20, 6, "$ " . FData::formatoNumeroReportes($tansaldo), '', 1, 'R', 0, 0);
            $tanvalor = 0;
            $tanaplica = 0;
            $tansaldo = 0;
            $controw++;
        }
        $idetiqueta = $anticipo->idetiqueta;
        $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
        $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $anticipo->etiqueta), '', 1, 'L', 0, 0);
        $tdid = 0;
        $controw++;
    }
    $etiqueta = $anticipo->etiqueta;
    $pdf->SetFont('Arial', '', 7); // titulos
    $pdf->Row(array($anticipo->anid, $anticipo->anfecha, $anticipo->tcdescrip, $anticipo->anrefer, utf8_decode(PersonData::getById($anticipo->ceid)->cecodigo) . ' - ' . ucwords(strtolower(utf8_decode(PersonData::getById($anticipo->ceid)->cename))), '$ ' . $anticipo->anvalor, '$ ' . $anticipo->anaplica, '$ ' . $anticipo->ansaldo));
    $controw++;
    $detalles = AnticipodetData::getByAnId($anticipo->anid);

    foreach ($detalles as $detalle) {
        $pdf->Cell($t1, 5, "", '', 0, 'L', 0, 1);
        $pdf->Cell($t2, 5, "", '', 0, 'L', 0, 1);
        $pdf->Cell($t3, 5, "", '', 0, 'L', 0, 1);
        $pdf->Cell($t4, 5, FormasData::getById($detalle->cfid)->cfname, '', 0, 'L', 0, 1);
        $pdf->Cell($t5, 5, '$ ' . $detalle->afvalor, '', 0, 'R', 0, 1);
        $pdf->Cell($t6, 5, "", '', 0, 'L', 0, 1);
        $pdf->Cell($t7, 5, "", '', 1, 'L', 0, 1);
        $controw++;
    }

    $pdf->Ln(1);
    $tanvalor += $anticipo->anvalor;
    $tanaplica += $anticipo->anaplica;
    $tansaldo += $anticipo->ansaldo;

    $tganticipo += $anticipo->anvalor;
    $tgaplicado += $anticipo->anaplica;
    $tgtotal += $anticipo->ansaldo;

    $controw++;
    if ($controw >= 41) {
        $controw = 0;
    }
}
if ($tanvalor != 0) {
    $pdf->SetFont('Arial', 'B', 9); // titulos
    $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($tanvalor)), '', 0, 'R', 0, 0);
    $pdf->Cell(20, 6, "$ " . FData::formatoNumeroReportes($tanaplica), '', 0, 'R', 0, 0);
    $pdf->Cell(20, 6, "$ " . FData::formatoNumeroReportes($tansaldo), '', 1, 'R', 0, 0);
    $tanvalor = 0;
    $tanaplica = 0;
    $tansaldo = 0;
    $controw++;
}
$pdf->Cell($t1, 6, '', '', 0, 'L', 0, 1);
$pdf->Cell($t2, 6, '', '', 0, 'L', 0, 1);
$pdf->Cell($t3, 6, '', '', 0, 'L', 0, 1);
$pdf->Cell($t4, 6, 'TOTALES GENERALES : ', '', 0, 'R', 0, 1);
$pdf->Cell($t5, 6, '$ ' . FData::formatoNumeroReportes($tganticipo), '', 0, 'R', 0, 1);
$pdf->Cell($t6, 6, '$ ' . FData::formatoNumeroReportes($tgaplicado), '', 0, 'R', 0, 1);
$pdf->Cell($t7, 6, '$ ' . FData::formatoNumeroReportes($tgtotal), '', 1, 'R', 0, 1);


$pdf->Output();

// INFORME DETALLADO

class Validacion
{
    public static function ValidaFecha($fechaini, $fechahasta)
    {
        $t = '';
        if (empty($fechaini) && empty($fechahasta)) {
            $t = "Debe ingresar Rango de fecha valido";
        } elseif (empty($fechaini)) {
            $t = "Debe ingresar Fecha de inicio valido";
        } elseif (empty($fechahasta)) {
            $t = "Debe ingresar Fecha de Hasta valido";
        }
        return $t;
    }

    public static function tipoDocumento($tipo)
    {
        $array = array(
            "FACTURA" => "01",
            "NOTA DE CRÉDITO" => "04",
            "RETENCION" => "07"
        );
        $indice = array_search($tipo, $array, false);
        return $indice;
    }

    public static function validaDatos()
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
        return $msj;
    }

    public static function validaData($where)
    {
        return AnticipocabData::getByAllFecha($where);
    }

    public static function validaAnticipos($fecha)
    {
        return AnticipocabData::getByForCorte($fecha);
    }
}

