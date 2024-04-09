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

class PDF extends FPDF
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
        $this->Cell(90, 6, '', 0, 0, 'C', 0, 1);
        $this->Cell(100, 6, 'Rango de fechas , Desde : ' . $GLOBALS['desde'] . ", Hasta : " . $GLOBALS['hasta'], 0, 1, 'R', 0, 1);
//        $this->SetFont('Arial', 'B', 8);
//        $this->Cell(10, 6, 'Num ', 'T,B,L,R', 0, 'L', 0, 1);
//        $this->Cell(20, 6, 'Tipo', 'T,B,L,R', 0, 'L', 0, 1);
//        $this->Cell(20, 6, 'Fecha', 'T,B,L,R', 0, 'L', 0, 1);
//        $this->Cell(80, 6, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
//        $this->Cell(20, 6, 'Anticipo', 'T,B,L,R', 0, 'R', 0, 1);
//        $this->Cell(20, 6, 'Abono', 'T,B,L,R', 0, 'R', 0, 1);
//        $this->Cell(20, 6, 'Saldo', 'T,B,L,R', 1, 'R', 0, 1);
    }
}

$fuentes = FData::fuentesPdf();


$desde = $_POST['desde'];
$hasta = $_POST['hasta'];


$where = " where DATE(anfecha) >= \"$desde\" and DATE(anfecha) <= \"$hasta\"  ";
if($_POST['tipoAnticipo'] != 0){
    $where .= " and a.taid = ". $_POST['tipoAnticipo'];
}
if($_POST['sucursal'] != 0){
    $where .= " and suid = ". $_POST['sucursal'];
}
if($_POST['etiquetac'] != 0){
    $where .= " and b.setq_id = ". $_POST['etiquetac'];
}

if ($_POST['cliente'] != 0) {
    $where .= " and b.ceid = " . $_POST['cliente'] . " ";
}
$where .= " and anestado = 1 ";
if (isset($_POST)) {
    if (empty(Validacion::validaData($where))) {
        print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
    }

    $error = Validacion::validaDatos();
    if ($error != '' || !isset($_SESSION)) {
        print_r('
      <script>
      alert("' . $error . '")
      window.close();
      </script>
      ');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();


//if ($_POST["tipo"] == 0) { // opcion 0 del select tipo de informe
//    if($_POST['tipoAnticipo'] != 0){
//        $where .= " and taid = ". $_POST['tipoAnticipo'];
//    }
    $anticipos = AnticipocabData::getByAllAnticipos($where);
//    var_dump($anticipos);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Ln(5);
    $pdf->Cell(190, 6, 'ANTICIPOS RESUMIDOS ', 0, 1, 'C', 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 6, 'Codigo ', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(80, 6, 'Cliente ', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(80, 6, 'Total Anticipo', 'T,B,L,R', 1, 'R', 0, 1);
    $cliente = 0;
    $totalc = 0;
    $pdf->SetFont('Arial', '', 10);
    foreach ($anticipos as $anticipo) {
        $pdf->Cell(30, 7, PersonData::getById($anticipo->ceid)->cecodigo, 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(80, 7, ucwords(strtolower(utf8_decode(PersonData::getById($anticipo->ceid)->cename))), 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(80, 7, '$ ' . number_format($anticipo->total, 2, '.', ','), 'T,B,L,R', 1, 'R', 0, 1);
        $totalc += $anticipo->total;
    }
    $pdf->Cell(110, 7, "TOTAL : ", 'T,B,L,R', 0, 'R', 0, 1);
    $pdf->Cell(80, 7, '$ ' . FData::formatoNumeroReportes($totalc), 'T,B,L,R', 1, 'R', 0, 1);

//}

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
        return AnticipocabData::getByAllAnticipos($where);
    }

    public static function validaAnticipos($fecha)
    {
        return AnticipocabData::getByForCorte($fecha);
    }
}

