<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/PedidosdetData.php';
require '../../core/modules/index/model/PedidosData.php';
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
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
//include 'funcionesReporte.php';
//include 'ReporteAnticiposFunciones.php';
//require 'CabeceraReporte.php';

$desde = $_POST['desde'];
$hasta = $_POST['hasta'];

$where = "where DATE(pefecha) >= \"$desde\" and DATE(pefecha) <= \"$hasta\" ";

if ($_POST['cliente'] != 0) {
    $where .= " and ceid = " . $_POST['cliente'];
}
if ($_POST['vendedor'] != 0) {
    $where .= " and veid = " . $_POST['vendedor'];
}

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
$GLOBALS['titulo'] = $_POST['tituloPagina'];
class PDF extends FPDF
{
// Cabecera de página
    public function Header()
    {
        $this->SetFont('Arial', 'B', 13); // titulos
        $this->Cell(95, 6, $_SESSION['razonSocial'], 0, 0, 'L', 0, 0);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(95, 4, 'Usuario :' . UserData::getById($_SESSION['user_id'])->name, 0, 1, 'R', 0, 0);
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190,7,strtoupper($GLOBALS['titulo']),0,1,'C',0,0);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();


if ($_POST["tipo"] == 0) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(5);
    $pdf->Cell(190, 6, 'RESUMIDO ', 0, 1, 'C', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(190, 5, 'Fecha , desde :' . $_POST['desde'] . " , Hasta :" . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pedidos = PedidosData::getByAllFecha($where);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(192, 192, 192);
    $pdf->Cell(10, 5, '# ', 'T,B,L,R', 0, 'L', 1, 1);
    $pdf->Cell(25, 5, 'FECHA ', 'T,B,L,R', 0, 'L', 1, 1);
    $pdf->Cell(74, 5, 'CLIENTE ', 'T,B,L,R', 0, 'L', 1, 1);
    $pdf->Cell(27, 5, 'ESTADO ', 'T,B,L,R', 0, 'L', 1, 1);
    $pdf->Cell(27, 5, 'APROBADO ', 'T,B,L,R', 0, 'L', 1, 1);
    $pdf->Cell(27, 5, 'TOTAL', 'T,B,L,R', 1, 'R', 1, 1);
    $cliente = 0;
    $totalc = 0;
    $pdf->SetFont('Arial', '', 8);
    foreach ($pedidos as $pedido) {
        $pdf->Cell(10, 5, $pedido->peid, 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, $pedido->pefecha, 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(74, 5, PersonData::getById($pedido->ceid)->cecodigo . ' - ' . ucwords(strtolower(utf8_decode(PersonData::getById($pedido->ceid)->cename))), 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(27, 5, ($pedido->peestado == 1) ? "Activo" : "Anulado", 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(27, 5, ($pedido->peaprobado == "S") ? "Aprobado" : "Pendiente", 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(27, 5, number_format($pedido->petotal, 2, '.', ','), 'T,B,L,R', 1, 'R', 0, 1);
        $totalc += $pedido->petotal;
    }
    $pdf->Cell(110, 5, "TOTAL : ", 'T,B,L,R', 0, 'R', 0, 1);
    $pdf->Cell(80, 5, FData::formatoNumeroReportes($totalc), 'T,B,L,R', 1, 'R', 0, 1);

} elseif ($_POST['tipo'] == 1) {

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(5);
    $pdf->Cell(190, 6, 'DETALLE DE PEDIDOS ', 0, 1, 'C', 0, 1);

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(190, 7, 'Rango de fechas , Desde : ' . $_POST['desde'] . ", Hasta : " . $_POST['hasta'], 0, 1, 'R', 0, 1);

    $pedidos = PedidosData::getByAllDetallado($where);
    $pdf->Ln(5);
    $cliente = 0;
    $totalc = 0;
    $pdf->SetFont('Arial', '', 10);
    $documento = 0;
    $documentoe = 0;

    $alto = 5;
    $altoTitulo = 5;
    $tpvp = 0;
    $tdesc1 = 0;
    $tdesc2 = 0;
    $tiva = 0;
    $total = 0;
    $tentr = 0;
    $totalDocumento = 0;
    $pdf->SetFont('Arial', '', 6); // titulos
    foreach ($pedidos as $pedido) {

        if ($documento != $pedido->peid) {
            if ($total > 0) {
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(10, $alto, "", 'L,T,B', 0, 'L', 1, 0);
                $pdf->Cell(55, $alto, "", 'T,B', 0, 'L', 1, 0);
                $pdf->Cell(18, $alto, "", 'T,B', 0, 'R', 1, 0);
                $pdf->Cell(18, $alto, "Totales:", 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tpvp), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tdesc1), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tdesc2), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($total), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tiva), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tentr), 'T,B,R', 1, 'R', 1, 0);
                $total = 0;
                $pdf->Ln(2);
                $pdf->Cell(47, $alto, "Base Gravada : " . FData::formatoNumeroReportes($totalConIva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Base Exenta : " . FData::formatoNumeroReportes($totalSinIva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Iva : " . FData::formatoNumeroReportes($tiva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Total : " . FData::formatoNumeroReportes($tentr), '', 1, 'R', 0, 0);
                $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($pedido->veid)->name . ' ' . UserData::getById($pedido->veid)->lastname, '', 0, 'L', 0, 0);
                $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $pedido->pecreate_at, '', 1, 'R', 0, 0);
                $tpvp = 0;
                $tdesc1 = 0;
                $tdesc2 = 0;
                $tiva = 0;
                $total = 0;
                $tentr = 0;
                $totalConIva = 0;
                $totalSinIva = 0;
            }
            if ($documento != 0) {
                $pdf->Ln(15);
            }
            if (!is_null($pedido->ceid)) {
                $pdf->Cell(23, $altoTitulo, 'CLIENTE : ' . $pedido->cename, 0, 1, 'L', 0, 0);
            }

            $pdf->Cell(40, $altoTitulo, 'PEDIDO : ' . $pedido->peid, 0, 0, 'L', 0, 0);
            $pdf->Cell(40, $altoTitulo, 'SUCURSAL : ' . SucursalData::getById($pedido->peid)->suname, 0, 0, 'L', 0, 0);
//            $pdf->Cell(50, $altoTitulo, 'CONTROL : ' . $pedido->opid, 0, 0, 'R', 0, 0);
            $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $pedido->pefecha, 0, 1, 'R', 0, 0);
            $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $pedido->peobserva, 0, 1, 'L', 0, 0);

            $documento = $pedido->peid;
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(10, 5, "COD", 'L,T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(55, 5, "PRODUCTO", 'L,T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(18, 5, "CANTIDAD", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(18, 5, "UNIDAD", 'L,T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(15, 5, "PVP", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "DESC1", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "DESC2", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "SUBTOTAL", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "IVA", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "TOTAL", 'L,T,B,R', 1, 'R', 1, 0);
        }

        if ($documento == $pedido->peid) {
            $pdf->Cell(10, $alto, $pedido->itid, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(55, $alto, ProductData::getById($pedido->itid)->itcodigo . ' - ' . ucwords(strtolower($pedido->itname)), 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(18, $alto, FData::formatoNumeroReportes($pedido->pdcandig), 'L,T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(18, $alto, $pedido->undescrip, 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdpvp), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdpdscto1), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdpdscto2), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdtotal), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdiva), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdtotal + $pedido->pdiva), 'T,B,R', 1, 'R', 0, 0);
            if ($pedido->pdiva == 0) {
                $totalSinIva += $pedido->pdtotal;
            } else {
                $totalConIva += $pedido->pdtotal;
            }
            $tpvp += $pedido->pdpvp;
            $tdesc1 += $pedido->pdpdscto1;
            $tdesc2 += $pedido->pdpdscto2;
            $tiva += $pedido->pdiva;
            $total += $pedido->pdtotal;
            $tentr += $pedido->pdtotal + $pedido->pdiva;
        }
    }
    if ($total > 0) {
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(10, $alto, "", 'L,T,B', 0, 'L', 1, 0);
        $pdf->Cell(55, $alto, "", 'T,B', 0, 'L', 1, 0);
        $pdf->Cell(18, $alto, "", 'T,B', 0, 'R', 1, 0);
        $pdf->Cell(18, $alto, "Totales :", 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tpvp), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tdesc1), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tdesc2), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($total), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tiva), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, FData::formatoNumeroReportes($tentr), 'T,B,R', 1, 'R', 1, 0);

        $pdf->Ln(2);
        $pdf->Cell(47, $alto, "Base Gravada : " . FData::formatoNumeroReportes($totalConIva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Base Exenta : " . FData::formatoNumeroReportes($totalSinIva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Iva : " . FData::formatoNumeroReportes($tiva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Total : " . FData::formatoNumeroReportes($tentr), '', 1, 'R', 0, 0);
        $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($pedido->veid)->name . ' ' . UserData::getById($pedido->veid)->lastname, '', 0, 'L', 0, 0);
        $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $pedido->pecreate_at, '', 1, 'R', 0, 0);
    }
} else { // tipo 2

    $pedidos = PedidosData::getByAllDetalladoPendientes($where);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Ln(5);
    $pdf->Cell(190, 6, 'DETALLE / PEDIDOS POR ENTREGAR ', 0, 1, 'C', 0, 1);

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(190, 7, 'Rango de fechas , Desde : ' . $_POST['desde'] . ", Hasta : " . $_POST['hasta'], 0, 1, 'R', 0, 1);


    $pdf->Ln(5);
    $cliente = 0;
    $totalc = 0;
    $pdf->SetFont('Arial', '', 10);
    $documento = 0;
    $documentoe = 0;

    $alto = 5;
    $altoTitulo = 5;
    $tpvp = 0;
    $tdesc1 = 0;
    $tdesc2 = 0;
    $tiva = 0;
    $total = 0;
    $tentr = 0;
    $totalDocumento = 0;
    $pdf->SetFont('Arial', '', 6); // titulos
    foreach ($pedidos as $pedido) {

        if ($documento != $pedido->peid) {
            if ($total > 0) {
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(10, $alto, "", 'L,T,B', 0, 'L', 1, 0);
                $pdf->Cell(45, $alto, "", 'T,B', 0, 'L', 1, 0);
                $pdf->Cell(15, $alto, "", 'T,B', 0, 'R', 1, 0);
                $pdf->Cell(15, $alto, "Totales:", 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tpvp), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tdesc1), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tdesc2), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($total), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tiva), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tentr), 'T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tentrega), 'T,B,R', 0, 'R', 1, 0);
                $pdf->SetFillColor(212, 200, 212);
                $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tpendiente), 'T,B,R', 1, 'R', 1, 0);
                $total = 0;
                $pdf->Ln(2);
                $pdf->Cell(47, $alto, "Base Gravada : " . FData::formatoNumeroReportes($totalConIva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Base Exenta : " . FData::formatoNumeroReportes($totalSinIva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Iva : " . FData::formatoNumeroReportes($tiva), '', 0, 'R', 0, 0);
                $pdf->Cell(47, $alto, "Total : " . FData::formatoNumeroReportes($tentr), '', 1, 'R', 0, 0);
                $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($pedido->veid)->name . ' ' . UserData::getById($pedido->veid)->lastname, '', 0, 'L', 0, 0);
                $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $pedido->pecreate_at, '', 1, 'R', 0, 0);
                $tpvp = 0;
                $tdesc1 = 0;
                $tdesc2 = 0;
                $tiva = 0;
                $total = 0;
                $tentr = 0;
                $tentrega = 0;
                $tpendiente = 0;

                $totalConIva = 0;
                $totalSinIva = 0;
            }
            if ($documento != 0) {
                $pdf->Ln(15);
            }
            if (!is_null($pedido->ceid)) {
                $pdf->Cell(23, $altoTitulo, 'CLIENTE : ' . $pedido->cename, 0, 1, 'L', 0, 0);
            }

            $pdf->Cell(40, $altoTitulo, 'PEDIDO : ' . $pedido->peid, 0, 0, 'L', 0, 0);
            $pdf->Cell(40, $altoTitulo, 'SUCURSAL : ' . SucursalData::getById($pedido->peid)->suname, 0, 0, 'L', 0, 0);
//            $pdf->Cell(50, $altoTitulo, 'CONTROL : ' . $pedido->opid, 0, 0, 'R', 0, 0);
            $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $pedido->pefecha, 0, 1, 'R', 0, 0);
            $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $pedido->peobserva, 0, 1, 'L', 0, 0);

            $documento = $pedido->peid;
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(10, 5, "Cod", 'L,T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(45, 5, "Producto", 'L,T,B,R', 0, 'L', 1, 0);
            $pdf->Cell(15, 5, "Cantidad", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(15, 5, "Unidad", 'L,T,B,R', 0, 'C', 1, 0);
            $pdf->Cell(13, 5, "Pvp", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Desc1", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Desc2", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Subtotal", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Iva", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Total", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Cant Entg", 'L,T,B,R', 0, 'R', 1, 0);
            $pdf->Cell(13, 5, "Cant Pend", 'L,T,B,R', 1, 'R', 1, 0);
        }

        if ($documento == $pedido->peid) {
            $pdf->Cell(10, $alto, $pedido->itid, 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(45, $alto, ProductData::getById($pedido->itid)->itcodigo . ' - ' . ucwords(strtolower(substr($pedido->itname, 0, 30))), 'L,T,B,R', 0, 'L', 0, 0);
            $pdf->Cell(15, $alto, FData::formatoNumeroReportes($pedido->pdcandig), 'L,T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(15, $alto, $pedido->undescrip, 'T,B,R', 0, 'C', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdpvp), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdpdscto1), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdpdscto2), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdtotal), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdiva), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdtotal + $pedido->pdiva), 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdcanentrega), 'T,B,R', 0, 'R', 0, 0);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->Cell(13, $alto, FData::formatoNumeroReportes($pedido->pdcandig - $pedido->pdcanentrega), 'T,B,R,L', 1, 'R', 1, 0);
            if ($pedido->pdiva == 0) {
                $totalSinIva += $pedido->pdtotal;
            } else {
                $totalConIva += $pedido->pdtotal;
            }
            $tpvp += $pedido->pdpvp;
            $tdesc1 += $pedido->pdpdscto1;
            $tdesc2 += $pedido->pdpdscto2;
            $tiva += $pedido->pdiva;
            $total += $pedido->pdtotal;
            $tentr += $pedido->pdtotal + $pedido->pdiva;
            $tentrega += $pedido->pdcanentrega;
            $tpendiente += $pedido->pdcandig - $pedido->pdcanentrega;
        }
    }
    if ($total > 0) {
        $pdf->SetFillColor(220, 220, 220);
        $pdf->Cell(10, $alto, "", 'L,T,B', 0, 'L', 1, 0);
        $pdf->Cell(45, $alto, "", 'T,B', 0, 'L', 1, 0);
        $pdf->Cell(15, $alto, "", 'T,B', 0, 'R', 1, 0);
        $pdf->Cell(15, $alto, "Totales :", 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tpvp), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tdesc1), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tdesc2), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($total), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tiva), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tentr), 'T,B,R', 0, 'R', 1, 0);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tentrega), 'T,B,R', 0, 'R', 1, 0);
        $pdf->SetFillColor(212, 200, 212);
        $pdf->Cell(13, $alto, FData::formatoNumeroReportes($tpendiente), 'T,B,R', 1, 'R', 1, 0);

        $pdf->Ln(2);
        $pdf->Cell(47, $alto, "Base Gravada : " . FData::formatoNumeroReportes($totalConIva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Base Exenta : " . FData::formatoNumeroReportes($totalSinIva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Iva : " . FData::formatoNumeroReportes($tiva), '', 0, 'R', 0, 0);
        $pdf->Cell(47, $alto, "Total : " . FData::formatoNumeroReportes($tentr), '', 1, 'R', 0, 0);
        $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($pedido->veid)->name . ' ' . UserData::getById($pedido->veid)->lastname, '', 0, 'L', 0, 0);
        $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $pedido->pecreate_at, '', 1, 'R', 0, 0);
    }
}
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
        return PedidosData::getByAllFecha($where);
    }
}

