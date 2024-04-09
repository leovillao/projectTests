<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/FData.php';
require '../../core/modules/index/model/UnitData.php';
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
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
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
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 9); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 10); // titulos
$pdf->Cell(190, 7, 'Fecha Desde :' . $_POST['desde'] . " , Hasta : " . $_POST['hasta'], 0, 1, 'R', 0, 0);
$pdf->SetFont('Arial', '', 12); // titulos

if (isset($_POST['proveedor']) && !empty($_POST['proveedor'])) {
    $pdf->Cell(190, 7, "PROVEEDOR : " . ProveeData::getByRucProvee($_POST['proveedor'])->razon, 0, 1, 'L', 0, 0);
}

$where = 'where opfecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if (isset($_POST['proveedor']) && !empty($_POST['proveedor'])) { // proveedores
    $where .= 'and prid = ' . ProveeData::getByRucProvee($_POST['proveedor'])->id;
}

if ($_POST['opcionReporte'] == 1) {
    $where .= " and fi_id is NULL and prid IS NOT NULL";
}

if ($_POST['opcionReporte'] == 2) {
    $where .= " and fi_id is NOT NULL and prid IS NOT NULL";
}

// tipoReporte = 1 => resumido
// tipoReporte = 2 => detallado
if ($_POST['tipoReporte'] == 1) {
    $compras = OperationData::getInformacionComprasResumidos($where);
//    echo json_encode($compras);
} else {
    $compras = OperationData::getInformacionCompras($where);
}
if ($compras) {
//var_dump($compras);

    if ($_POST['tipoReporte'] == 1) {
        $pdf->SetFont('Arial', 'B', 12); // titulos
        $pdf->Cell(190, 5, "Resumido por Documento", '', 1, 'C', 0, 0);
        $pdf->SetFont('Arial', 'B', 10); // titulos
        $pdf->Ln(5);
        $pdf->SetFillColor(192, 192, 192);
        $pdf->Cell(15, 5, "Control", 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(26, 5, "Fecha", 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(26, 5, "Factura", 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(85, 5, "Proveedor", 'L,T,B,R', 0, 'L', 1, 0);
        $pdf->Cell(35, 5, "Total", 'L,T,B,R', 1, 'R', 1, 0);
        $documento = 0;
        $documentoe = 0;

        $alto = 5;
        $altoTitulo = 5;
        $total = 0;
        $totalDocumento = 0;
        $pdf->SetFont('Arial', '', 7); // titulos
        foreach ($compras as $venta) {

            if ($documento != $venta->opnumdoc) {
                $pdf->Cell(15, $alto, $venta->opnumdoc, 'L,T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(26, $alto, $venta->opfecha, 'L,T,B,R', 0, 'L', 0, 0);
                if (!is_null($venta->fi_id)) {
                    $pdf->Cell(26, $altoTitulo, FilesData::getByIdOne($venta->fi_id)->fi_docum, 'L,T,B,R', 0, 'L', 0, 0);
                } else {
                    $pdf->Cell(26, $altoTitulo, 'PENDIENTE', 'L,T,B,R', 0, 'L', 0, 0);
                }
                if ($venta->prid != null) {
                    $pdf->Cell(85, $alto, ProveeData::getById($venta->prid)->ruc . ' - ' . ucwords(strtolower(ProveeData::getById($venta->prid)->razon)), 'T,B,R', 0, 'L', 0, 0);
                } else {
                    $pdf->Cell(85, $alto, '', 'T,B,R', 0, 'L', 0, 0);
                }

                $pdf->Cell(35, $alto, FData::formatoNumeroReportes($venta->total), 'T,B,R', 1, 'R', 0, 0);
                $total += $venta->total;
            }
        }
        $pdf->Cell(32, $alto, "", 'L,T,B', 0, 'L', 0, 0);
        $pdf->Cell(85, $alto, "", 'T,B', 0, 'L', 0, 0);
        $pdf->Cell(35, $alto, "Total", 'T,B,R', 0, 'R', 0, 0);
        $pdf->Cell(35, $alto, FData::formatoNumeroReportes($total), 'L,T,B,R', 0, 'R', 0, 0);

    } else {
        $pdf->SetFont('Arial', '', 10); // titulos
        $documento = 0;
        $documentoe = 0;

        $alto = 5;
        $altoTitulo = 5;
        $total = 0;
        $totalDocumento = 0;
        $pdf->SetFont('Arial', '', 7); // titulos
        foreach ($compras as $venta) {
            if ($documento != $venta->opnumdoc) {
                if ($total > 0) {
                    $pdf->Cell(86, $alto, '', 'L,T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
                    $pdf->Cell(26, $alto, 'TOTAL : ', 'T,B,R', 0, 'R', 0, 0);
                    $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($total), 'T,B,R', 1, 'R', 0, 0);
                    $total = 0;
                    $pdf->Ln(2);
                    $pdf->Cell(95, $alto, "DIGITADO POR : " . UserData::getById($venta->user_id)->name . ' ' . UserData::getById($venta->user_id)->lastname, '', 0, 'L', 0, 0);
                    $pdf->Cell(95, $alto, "FECHA DE REGISTRO : " . $venta->opcreate_at, '', 1, 'R', 0, 0);
                }

                if ($documento != 0) {
                    $pdf->Ln(15);
                }
                if (!is_null($venta->prid)) {
                    $pdf->Cell(23, $altoTitulo, 'PROVEEDOR : ' . ProveeData::getById($venta->prid)->razon, 0, 1, 'L', 0, 0);
                }

                $pdf->Cell(40, $altoTitulo, 'DOCUMENTO : ' . $venta->opnumdoc, 0, 0, 'L', 0, 0);
                $pdf->Cell(40, $altoTitulo, 'BODEGA : ' . BodegasData::getById($venta->bodega)->bodescrip, 0, 0, 'L', 0, 0);
                $pdf->Cell(50, $altoTitulo, 'CONTROL : ' . $venta->opid, 0, 0, 'R', 0, 0);
                $pdf->Cell(50, $altoTitulo, 'FECHA : ' . $venta->opfecha, 0, 1, 'R', 0, 0);
                $pdf->Cell(140, $altoTitulo, 'COMENTARIO : ' . $venta->opcomenta, 0, 1, 'L', 0, 0);
                if (!is_null($venta->fi_id)) {
                    $pdf->Cell(23, $altoTitulo, 'FACTURA : ' . FilesData::getByIdOne($venta->fi_id)->fi_docum, 0, 1, 'L', 0, 0);
                } else {
                    $pdf->Cell(23, $altoTitulo, 'FACTURA : PENDIENTE', 0, 1, 'L', 0, 0);
                }
                if ($venta->opestado == 0) {
                    $pdf->Cell(190, $altoTitulo, 'ANULADO', 0, 1, 'R', 0, 0);
                }
                $documento = $venta->opnumdoc;
                $pdf->SetFillColor(220, 220, 220);
                $pdf->Cell(86, 5, "PRODUCTO", 'L,T,B,R', 0, 'L', 1, 0);
                $pdf->Cell(26, 5, "CANTIDAD", 'L,T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(26, 5, "UNIDAD", 'L,T,B,R', 0, 'C', 1, 0);
                $pdf->Cell(26, 5, "COSTO", 'L,T,B,R', 0, 'R', 1, 0);
                $pdf->Cell(26, 5, "TOTAL", 'L,T,B,R', 1, 'R', 1, 0);
            }

            if ($documento == $venta->opnumdoc) {
                $pdf->Cell(86, $alto, ProductData::getById($venta->itid)->itcodigo . ' - ' . ucwords(strtolower(ProductData::getById($venta->itid)->itname)), 'L,T,B,R', 0, 'L', 0, 0);
                $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($venta->odcandig), 'L,T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(26, $alto, ucwords(strtolower(UnitData::getById($venta->unid_dig)->undescrip)), 'T,B,R', 0, 'C', 0, 0);
                $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($venta->odcostoudig), 'T,B,R', 0, 'R', 0, 0);
                $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($venta->odcostotot), 'T,B,R', 1, 'R', 0, 0);
                $total = $total + $venta->odcostotot;
            }
        }
        if ($total > 0) {
            $pdf->Cell(86, $alto, '', 'L,T,B', 0, 'L', 0, 0);
            $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
            $pdf->Cell(26, $alto, '', 'T,B', 0, 'L', 0, 0);
            $pdf->Cell(26, $alto, 'TOTAL : ', 'T,B,R', 0, 'R', 0, 0);
            $pdf->Cell(26, $alto, FData::formatoNumeroReportsInventario($total), 'T,B,R', 1, 'R', 0, 0);
            $total = 0;
        }
    }
    $pdf->Output();
} else {
    echo '<script>
     var opcion = confirm("No existe información para los criterios seleccionados");
        if (opcion == true) {
            window.close();
        } else {
            window.close();
        }
        </script>';
}
