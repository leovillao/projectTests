<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
include 'funcionesReporte.php';
require '../../core/controller/Fpdf/fpdf.php';

$GLOBALS['titulo'] = $_POST['tituloPagina'];
$GLOBALS['desde'] = $_POST['desde'];
$GLOBALS['hasta'] = $_POST['hasta'];
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
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);

        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
        $this->SetFont('Arial', 'B', 7); // titulos

        if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
            $this->Ln(1);
            $this->Cell(95, 5, 'VENDEDOR : TODOS', 0, 0, 'L', 0, 1);
            $this->Cell(95, 4, 'Desde : ' . $GLOBALS['desde'] . ' / Hasta : ' . $GLOBALS['hasta'], 0, 1, 'R', 0, 1);
            if ($GLOBALS['sucursal'] != 0) {
                $sucursal = "Sucursal : " . SucursalData::getById($GLOBALS['sucursal'])->suname;
            }
            $this->Cell(95, 7,  $sucursal , 0, 0, 'L', 0, 0);
            $this->Ln(1);
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(190, 5, 'DETALLADO POR DOCUMENTOS', 0, 1, 'C', 0, 1);
            $this->Ln(1);
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(15,4, '', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(20,4, 'Tipo', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(9, 4, '', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(22,4, '', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(15,4, 'Tipo', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(22,4, 'Doc', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(60,4, '', 'T,L,R', 0, 'C', 0, 1);
            $this->Cell(30,4, '', 'T,L,R', 1, 'C', 0, 1);
            $this->Cell(15,4, 'Fecha', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(20,4, 'Cobro', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(9, 4, 'Id', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(22,4, 'Referencia', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(15,4, 'Deuda', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(22,4, 'Deuda', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(60,4, 'Cliente', 'B,L,R', 0, 'C', 0, 1);
            $this->Cell(30,4, 'Valor', 'B,L,R', 1, 'C', 0, 1);
        } else {
            $this->SetFont('Arial', 'B', 9);
            $this->Ln();
            $this->Cell(95, 5, 'VENDEDOR : ' . UserData::getById($_SESSION['user_id'])->name, 0, 0, 'L', 0, 1);
            $this->Cell(95, 4, 'Desde : ' . $GLOBALS['desde'] . ' / Hasta : ' . $GLOBALS['hasta'], 0, 0, 'L', 0, 1);
            $this->Ln();
            $this->SetFont('Arial', 'B', 9);
            $this->Cell(190, 5, 'DETALLADO POR DOCUMENTOS', 0, 1, 'C', 0, 1);
            $this->Ln();
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(15, 6, 'Fecha', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(20, 6, 'Tipo Cobro', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(9, 6, 'Id', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(22, 6, 'Referencia', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(15, 6, 'Tipo Deuda', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(22, 6, 'Num Deuda', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(60, 6, 'Cliente', 'T,B,L,R', 0, 'C', 0, 1);
            $this->Cell(30, 6, 'Valor', 'T,B,L,R', 1, 'C', 0, 1);
            /*$data->coid
$data->fecha
$data->numFactura
$data->tcdescrip
$data->numNcr*/
        }
    }
}

if (isset($_POST)) {
    $error = validaDatos();
    if ($error != '' || !isset($_SESSION)) {
        print_r('
      <script>
      alert("' . $error . '")
      window.close();
      </script>
      ');
    }
}
if (UserData::getById($_SESSION['user_id'])->is_admin == 1) {
    /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
    if (!$idsCab) {
        $error = "No hay informacion en rangos seleccionados";
        if ($error != '' || !isset($_SESSION)) {
            print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
        }
    }
    /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
    $detCobros = ReporteCobros::getDetInfoDocumentos($_POST);
//    var_dump($detCobros);
    /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
    ReporteCobros::getDetalleDocumentosAdmin($detCobros, $_SESSION['user_id']);
//    echo json_encode($detCobros);
} else {
    /** SE OBTIENE LA CABECERA DE LOS COBROS POR FECHA , NO SE VALIDA EL PERFIL */
    $idsCab = ReporteCobros::getCabecera($_POST['desde'], $_POST['hasta']);
    if (!$idsCab) {
        $error = "No hay informacion en rangos seleccionados";
        if ($error != '' || !isset($_SESSION)) {
            print_r('
                      <script>
                      alert("' . $error . '")
                      window.close();
                      </script>
                      ');
        }
    }
    /** DEVUELVE EL DETALLE DEL COBRO DE ACUERDO A LOS IDS DE LA CABECERA */
    $detCobros = ReporteCobros::getDetInfoDocumentosNotAdmin($_POST);
    /** DEVUELVE EL PDF DE LA REPORTE DE ACUERDO A PARAMETROS DEL DETALLE DE COBRO */
    ReporteCobros::getDetalleDocumentosNotAdmin($detCobros, $_SESSION['user_id'], $_POST['desde'], $_POST['hasta']);
}


// INFORME DETALLADO
function validaDatos()
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

