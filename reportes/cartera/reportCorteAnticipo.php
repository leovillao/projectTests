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
require '../../core/modules/index/model/SucursalData.php';
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
$GLOBALS['fechacorte'] = $_POST['hasta'];
$GLOBALS['sucursal'] = $_POST['sucursal'];

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
        $this->SetFont('Arial', 'B', 10); // titulos
        if ($GLOBALS['sucursal'] != 0) {
            $sucursal = "Sucursal : " . SucursalData::getById($GLOBALS['sucursal'])->suname;
        }
        $this->Cell(100, 7, $sucursal, 0, 0, 'L', 0, 0);
        $this->Cell(90, 7, "Fecha de Corte : " . $GLOBALS['fechacorte'], 0, 1, 'R', 0, 0);

        $this->Cell(10, 5, '#', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(20, 5, 'Fecha', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(90, 5, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $this->Cell(23, 5, 'Anticipo', 'T,B,L,R', 0, 'R', 0, 1);
        $this->Cell(23, 5, 'Aplicado', 'T,B,L,R', 0, 'R', 0, 1);
        $this->Cell(23, 5, 'Saldo', 'T,B,L,R', 1, 'R', 0, 1);
    }
}

$fuentes = FData::fuentesPdf();
$idetiqueta = 0;

if ($_POST['tipo'] == 2) {
    if (empty(Validacion::validaAnticipos($_POST['hasta']))) {
        print_r('
          <script>
          alert("No hay datos con los criterios seleccionados.")
            window.close();
          </script>
      ');
    } else {
        $and = "";
        if ($_POST['cliente'] != 0) {
            $and .= " and c.ceid = " . $_POST['cliente'] . " ";
        }
        if ($_POST['sucursal'] != 0) { // sucursales
            $and .= ' and c.suid = ' . $_POST['sucursal'] . " ";
        }
        if ($_POST['etiquetac'] != 0) { // zoid
            $and .= ' and t.setq_id = ' . $_POST['etiquetac'] . " ";
        }

        if ($_POST['tipoAnticipo'] != 0) {
            $and .= " and taid = " . $_POST['tipoAnticipo'];
        }

        $and .= "and anestado = 1 order by etiqueta, cliente , anfecha  ";


        $fechaCorte = $_POST['hasta'];

        $anticipos = AnticipocabData::getByForCorte($fechaCorte, $and);
//        var_dump($anticipos);
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $cliente = 0;
        $totalc = 0;
        $pdf->SetFont('Arial', '', 9);
        $idetiqueta = 0;
        $etiqueta = '';
        $tganticipo = 0;
        $tgaplicado = 0;
        $tgtotal = 0;
        $trr = 0;
        foreach ($anticipos as $anticipo) {
            if ($controw == 0) {
                $pdf->AddPage();
            }
            if ($anticipo->idetiqueta != $idetiqueta) {
                $pdf->SetFont('Arial', 'B', 9); // titulos
                if ($totalvalor != 0) {
                    $pdf->Cell(143, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($totalvalor)), '', 0, 'R', 0, 0);
                    $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalAplicado), '', 0, 'R', 0, 0);
                    $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalSaldo), '', 1, 'R', 0, 0);
                    $totalvalor = 0;
                    $totalAplicado = 0;
                    $totalSaldo = 0;
                    $controw++;
                }
                $idetiqueta = $anticipo->idetiqueta;
                $pdf->SetFont('Arial', 'B', 9); // titulos
                $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
                $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $anticipo->etiqueta), '', 1, 'L', 0, 0);
                $tdid = 0;
                $controw++;
            }
            $etiqueta = $anticipo->etiqueta;
            $pdf->SetFont('Arial', '', 7); // titulos
            $pdf->Cell(10, 5, $anticipo->anid, '', 0, 'L', 0, 1);
            $pdf->Cell(20, 5, $anticipo->anfecha, '', 0, 'L', 0, 1);
            $pdf->Cell(90, 6, utf8_decode(PersonData::getById($anticipo->ceid)->cecodigo) . ' - ' . ucwords(strtolower(utf8_decode(PersonData::getById($anticipo->ceid)->cename))), '', 0, 'L', 0, 1);
            $pdf->Cell(23, 5, '$ ' . number_format($anticipo->anvalor, 2, '.', ','), '', 0, 'R', 0, 1);
            $pdf->Cell(23, 5, '$ ' . number_format($anticipo->aplicado, 2, '.', ','), '', 0, 'R', 0, 1);
            $pdf->Cell(23, 5, '$ ' . number_format($anticipo->anvalor - $anticipo->aplicado, 2, '.', ','), '', 1, 'R', 0, 1);

            $totalvalor += $anticipo->anvalor;
            $totalAplicado += $anticipo->aplicado;
            $totalSaldo += $anticipo->anvalor - $anticipo->aplicado;

            $controw++;
            if ($controw >= 45) {
                $controw = 0;
            }

            $tganticipo += $anticipo->anvalor;
            $tgaplicado += $anticipo->aplicado;
            $tgtotal += $anticipo->anvalor - $anticipo->aplicado;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        if ($totalvalor != 0) {
            $pdf->Cell(143, 6, utf8_decode("Total : " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($totalvalor)), '', 0, 'R', 0, 0);
            $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalAplicado), '', 0, 'R', 0, 0);
            $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($totalSaldo), '', 1, 'R', 0, 0);
            $totalvalor = 0;
            $totalAplicado = 0;
            $totalSaldo = 0;
            $controw++;
        }
//        var_dump(array_sum($tgaplicado));
        $pdf->Cell(143, 6, utf8_decode("Total General : $ " . FData::formatoNumeroReportes($tganticipo)), '', 0, 'R', 0, 0);
        $pdf->Cell(23, 6, "$ " . number_format($tgaplicado,2), '', 0, 'R', 0, 0);
        $pdf->Cell(23, 6, "$ " . FData::formatoNumeroReportes($tgtotal), '', 1, 'R', 0, 0);
        $pdf->Output();
    }
} // opcion 2
$and = "";
if ($_POST['cliente'] != 0) {
    $and .= " and c.ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $and .= ' and c.suid = ' . $_POST['sucursal'] . " ";
}
if ($_POST['etiquetac'] != 0) { // zoid
    $and .= ' and t.setq_id = ' . $_POST['etiquetac'] . " ";
}

if ($_POST['tipoAnticipo'] != 0) {
    $and .= " and a.taid = " . $_POST['tipoAnticipo'];
}
$and .= "and anestado = 1 order by etiqueta, cliente , anfecha";
$hasta = $_POST['hasta'];


if (isset($_POST)) {
    if (empty(Validacion::validaData($hasta, $and))) {
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

    public static function validaData($fecha, $and)
    {
        return AnticipocabData::getByAllFechaData($fecha, $and);
    }

    public static function validaAnticipos($fecha)
    {
        $and = "";
        if ($_POST['cliente'] != 0) {
            $and .= " and c.ceid = " . $_POST['cliente'] . " ";
        }
        if ($_POST['sucursal'] != 0) { // sucursales
            $and .= ' and c.suid = ' . $_POST['sucursal'] . " ";
        }
        if ($_POST['etiquetac'] != 0) { // zoid
            $and .= ' and t.setq_id = ' . $_POST['etiquetac'] . " ";
        }
        $and .= "and anestado = 1 order by etiqueta, cliente ";
        return AnticipocabData::getByForCorte($fecha, $and);
    }
}

