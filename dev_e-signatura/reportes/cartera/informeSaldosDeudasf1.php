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
        $this->Cell(193, 4, 'Pagina ' . $this->PageNo() . ' / {nb}', 0, 1, 'R', 0, 0);
        $this->SetFont('Arial', '', 11); // titulos
        $this->Cell(95, 7, utf8_decode('Fecha Emisión :') . date('d-m-Y H:i:s'), 'T', 0, 'L', 0, 0);
        $this->Cell(95, 7, "SMARTTAG plataforma de negocios", 'T', 1, 'R', 0, 0);
        $this->SetFont('Arial', 'B', 17); // titulos
        $this->Ln(5);
        $this->Cell(190, 7, $GLOBALS['titulo'], 0, 1, 'C', 0, 0);
        $this->SetFont('Arial', '', 8); // titulos
        $this->Cell(190, 4, 'RANGO DE FECHA : ' . $GLOBALS['desde'] . ' HASTA ' . $GLOBALS['hasta'], 0, 1, 'C', 0, 1);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 7); // titulos

        $this->SetFillColor(192, 192, 192);
        $this->Cell(15, 6, 'Fecha', 'L,T,B,R', 0, 'L', 1, 0);
        $this->Cell(23, 6, 'Doc', 'L,T,B,R', 0, 'C', 1, 0);
        $this->Cell(55, 6, 'Cliente', 'T,B,R', 0, 'L', 1, 0);
        $this->Cell(20, 6, 'Documento', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Total', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Abono', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Saldo', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(16, 6, 'Fecha Venc.', 'T,B,R', 0, 'C', 1, 0);
        $this->Cell(12, 6, 'Dias Venc', 'T,B,R', 1, 'C', 1, 0);
    }
}


$where = 'where a.deestado = 1 and a.defecha between "' . $_POST['desde'] . '" and "' . $_POST['hasta'] . '"';

if ($_POST['cliente'] != 0) {
    $where .= " and a.ceid = " . $_POST['cliente'] . " ";
}
if ($_POST['sucursal'] != 0) { // sucursales
    $where .= 'and a.suid = ' . $_POST['sucursal'] . " ";
}
if ($_POST['zona'] != 0) { // zona
    $where .= 'and b.zoid = ' . $_POST['zona'] . " ";
}
if ($_POST['tipoDocumento'] != 0) { // zoid
    $where .= 'and a.tdid = ' . $_POST['tipoDocumento'] . " ";
}
if ($_POST['vendedor'] != 0) { // zoid
    $where .= 'and a.veid = ' . $_POST['vendedor'];
}
if ($_POST['etiquetac'] != 0) { // zoid
    $where .= ' and b.setq_id = ' . $_POST['etiquetac'];
}
//vencidosdesde
//vencidoshasta

if ($_POST['vencidosdesde'] >= 1 && $_POST['vencidoshasta'] >= 1) {
    if ($_POST['estadodocumentos'] === 'negativo') {
        $where .= ' and DATEDIFF(NOW(), a.devence) <= 0 ';
    } else if ($_POST['estadodocumentos'] === 'positivo') {
        $where .= ' and DATEDIFF(NOW(), a.devence) >= ' . $_POST['vencidosdesde'] . ' and DATEDIFF(NOW(), a.devence) <= ' . $_POST['vencidoshasta'].' ';
    }
}

$where .= " order by h.name ,a.defecha , a.deid desc ";

$cobros = DeudasData::getByDataAllFechasVista($where, $_POST['ccdate']);
//var_dump($_POST);
//var_dump($cobros);

$pdf = new PDF();
//$pdf->AddPage();
$pdf->AliasNbPages();
$altoTitulo = 8;
$alto = 6;
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8); // titulos

/**/
$nameSucursal = "";
$cobroTipo = 0;
$sucName = "";
$totalST = "";

$netoD = 0;
$totalNetoD = 0;
$totalivasD = 0;
$netoE = 0;
$totalNetoE = 0;
$totalivasE = 0;
$tdid = 0;
$idetiqueta = 0;
$etiqueta = "";
$controw = 0;
$netoG = 0;
$totalNetoG = 0;
$totalivasG = 0;
/**
 * 0 - todos
 * 1 - doc con saldo
 * 2 - doc venc
 * 3 - doc por vencer
 */
$fuentes = FData::fuentesPdf();
$datosVacios = true;
if (count($cobros) > 0) {
    if ($_POST['alcance'] == 1) {
        foreach ($cobros as $cobro) {
            // saldo es mayor a 0
            if ($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0) {
                $datosVacios = false;
                if ($controw == 0) {
                    $pdf->AddPage();
                }
                if ($cobro->id_etiq <> $idetiqueta) {
                    if (!empty($cobro->deid)) {
                        $pdf->SetFont('Arial', 'B', 10); // titulos
                        if ($netoD != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
                            $netoD = 0;
                            $totalNetoD = 0;
                            $totalivasD = 0;
                            $controw++;

                        }
                        if ($netoE != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
                            $netoE = 0;
                            $totalNetoE = 0;
                            $totalivasE = 0;
                            $controw++;

                        }

                        $idetiqueta = $cobro->id_etiq;
                        $pdf->Cell(30, $alto, utf8_decode("Etiqueta : " . $cobro->name), '', 1, 'L', 0, 0);
                        $tdid = 0;
                        $controw++;
                    }
                }
                if ($cobro->tdid <> $tdid) {
                    if (!empty($cobro->deid)) {
                        $tdid = $cobro->tdid;
                        $pdf->SetFont('Arial', 'B', 9); // titulos
                        $pdf->Cell(40, $alto, utf8_decode("Tipo : " . $cobro->tdnombre), '', 1, 'R', 0, 0);
                    }
                }
                $etiqueta = $cobro->name;
                $pdf->SetFont('Arial', '', 7); // titulos
                $pdf->Cell(15, 6, $cobro->defecha, '', 0, 'L', 0, 0);
                $pdf->Cell(23, 6, $cobro->deid, '', 0, 'C', 0, 0);
                $pdf->Cell(55, 6, substr(utf8_decode(ucwords(strtolower($cobro->cename))), 0, 46), '', 0, 'L', 0, 0);
                $pdf->Cell(20, 6, $cobro->derefer, '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->deabono + $cobro->decopensa), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal - ($cobro->deabono + $cobro->decopensa)), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, $cobro->devence, '', 0, 'R', 0, 0);
                $pdf->Cell(12, 6, $cobro->vencidos, '', 1, 'R', 0, 0);
                $pdf->SetFont('Arial', '', 7); // titulos

                $netoD += $cobro->detotal;
                $totalNetoD += $cobro->deabono + $cobro->decopensa;
                $totalivasD += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoE += $cobro->detotal;
                $totalNetoE += $cobro->deabono + $cobro->decopensa;
                $totalivasE += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoG += $cobro->detotal;
                $totalNetoG += $cobro->deabono + $cobro->decopensa;
                $totalivasG += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $controw++;
                if ($controw >= 36) {
                    $controw = 0;
                }
            }
        }
        $pdf->SetFont('Arial', 'B', 7); // titulos
        if ($netoD != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
        }
        if ($netoE != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
        }
        $pdf->Cell(129, 6, utf8_decode("Total General : " . " :   " . FData::formatoNumeroReportes($netoG)), '', 0, 'R', 0, 0);
        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoG), '', 0, 'R', 0, 0);
        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasG), '', 1, 'L', 0, 0);

        $pdf->Ln(10);
    } elseif ($_POST['alcance'] == 2) {
        foreach ($cobros as $cobro) {
            if (($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0) && $cobro->vencidos > 0) {
                $datosVacios = false;
                if ($controw == 0) {
                    $pdf->AddPage();
                }
                if ($cobro->id_etiq <> $idetiqueta) {
                    if (!empty($cobro->deid)) {
                        $pdf->SetFont('Arial', 'B', 10); // titulos
                        if ($netoD != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
                            $netoD = 0;
                            $totalNetoD = 0;
                            $totalivasD = 0;
                            $controw++;

                        }
                        if ($netoE != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
                            $netoE = 0;
                            $totalNetoE = 0;
                            $totalivasE = 0;
                            $controw++;

                        }

                        $idetiqueta = $cobro->id_etiq;
                        $pdf->Cell(30, $alto, utf8_decode("Etiqueta : " . $cobro->name), '', 1, 'L', 0, 0);
                        $tdid = 0;
                        $controw++;
                    }
                }
                if ($cobro->tdid <> $tdid) {
                    if (!empty($cobro->deid)) {
                        $tdid = $cobro->tdid;
                        $pdf->SetFont('Arial', 'B', 9); // titulos
                        $pdf->Cell(40, $alto, utf8_decode("Tipo : " . $cobro->tdnombre), '', 1, 'R', 0, 0);
                    }
                }

                $etiqueta = $cobro->name;

                $pdf->SetFont('Arial', '', 7); // titulos
                $pdf->Cell(15, 6, $cobro->defecha, '', 0, 'L', 0, 0);
                $pdf->Cell(23, 6, $cobro->deid, '', 0, 'C', 0, 0);
                $pdf->Cell(55, 6, substr(utf8_decode(ucwords(strtolower($cobro->cename))), 0, 46), '', 0, 'L', 0, 0);
                $pdf->Cell(20, 6, $cobro->derefer, '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->deabono + $cobro->decopensa), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal - ($cobro->deabono + $cobro->decopensa)), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, $cobro->devence, '', 0, 'R', 0, 0);
                $pdf->Cell(12, 6, $cobro->vencidos, '', 1, 'R', 0, 0);
                $pdf->SetFont('Arial', '', 7); // titulos

                $netoD += $cobro->detotal;
                $totalNetoD += $cobro->deabono + $cobro->decopensa;
                $totalivasD += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoE += $cobro->detotal;
                $totalNetoE += $cobro->deabono + $cobro->decopensa;
                $totalivasE += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoG += $cobro->detotal;
                $totalNetoG += $cobro->deabono + $cobro->decopensa;
                $totalivasG += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $controw++;
                if ($controw >= 36) {
                    $controw = 0;
                }
            }

        }
        $pdf->SetFont('Arial', 'B', 7); // titulos
        if ($netoD != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
        }
        if ($netoE != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
        }
        $pdf->Cell(129, 6, utf8_decode("Total General : " . " :   " . FData::formatoNumeroReportes($netoG)), '', 0, 'R', 0, 0);
        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoG), '', 0, 'R', 0, 0);
        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasG), '', 1, 'L', 0, 0);

        $pdf->Ln(10);
    } elseif ($_POST['alcance'] == 3) {
        foreach ($cobros as $cobro) {
            if ($cobro->vencidos <= 0 && ($cobro->detotal - ($cobro->deabono + $cobro->decopensa) > 0)) {
                $datosVacios = false;
                if ($controw == 0) {
                    $pdf->AddPage();
                }
                if ($cobro->id_etiq <> $idetiqueta) {
                    if (!empty($cobro->deid)) {
                        $pdf->SetFont('Arial', 'B', 10); // titulos
                        if ($netoD != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
                            $netoD = 0;
                            $totalNetoD = 0;
                            $totalivasD = 0;
                            $controw++;

                        }
                        if ($netoE != 0) {
                            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
                            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
                            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
                            $netoE = 0;
                            $totalNetoE = 0;
                            $totalivasE = 0;
                            $controw++;

                        }

                        $idetiqueta = $cobro->id_etiq;
                        $pdf->Cell(30, $alto, utf8_decode("Etiqueta : " . $cobro->name), '', 1, 'L', 0, 0);
                        $tdid = 0;
                        $controw++;
                    }
                }
                if ($cobro->tdid <> $tdid) {
                    if (!empty($cobro->deid)) {
                        $tdid = $cobro->tdid;
                        $pdf->SetFont('Arial', 'B', 9); // titulos
                        $pdf->Cell(40, $alto, utf8_decode("Tipo : " . $cobro->tdnombre), '', 1, 'R', 0, 0);
                    }
                }

                $etiqueta = $cobro->name;

                $pdf->SetFont('Arial', '', 7); // titulos
                $pdf->Cell(15, 6, $cobro->defecha, '', 0, 'L', 0, 0);
                $pdf->Cell(23, 6, $cobro->deid, '', 0, 'C', 0, 0);
                $pdf->Cell(55, 6, substr(utf8_decode(ucwords(strtolower($cobro->cename))), 0, 46), '', 0, 'L', 0, 0);
                $pdf->Cell(20, 6, $cobro->derefer, '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->deabono + $cobro->decopensa), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal - ($cobro->deabono + $cobro->decopensa)), '', 0, 'R', 0, 0);
                $pdf->Cell(16, 6, $cobro->devence, '', 0, 'R', 0, 0);
                $pdf->Cell(12, 6, $cobro->vencidos, '', 1, 'R', 0, 0);
                $pdf->SetFont('Arial', '', 7); // titulos

                $netoD += $cobro->detotal;
                $totalNetoD += $cobro->deabono + $cobro->decopensa;
                $totalivasD += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoE += $cobro->detotal;
                $totalNetoE += $cobro->deabono + $cobro->decopensa;
                $totalivasE += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $netoG += $cobro->detotal;
                $totalNetoG += $cobro->deabono + $cobro->decopensa;
                $totalivasG += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

                $controw++;
                if ($controw >= 36) {
                    $controw = 0;
                }
            }
        }
        $pdf->SetFont('Arial', 'B', 7); // titulos
        if ($netoD != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
        }
        if ($netoE != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
        }
        $pdf->Cell(129, 6, utf8_decode("Total General : " . " :   " . FData::formatoNumeroReportes($netoG)), '', 0, 'R', 0, 0);
        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoG), '', 0, 'R', 0, 0);
        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasG), '', 1, 'L', 0, 0);

        $pdf->Ln(10);
    } else {
        foreach ($cobros as $cobro) {
            $datosVacios = false;
            if ($controw == 0) {
                $pdf->AddPage();
            }
            if ($cobro->id_etiq <> $idetiqueta) {
                if (!empty($cobro->deid)) {
                    $pdf->SetFont('Arial', 'B', 10); // titulos
                    if ($netoD != 0) {
                        $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
                        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
                        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
                        $netoD = 0;
                        $totalNetoD = 0;
                        $totalivasD = 0;
                        $controw++;

                    }
                    if ($netoE != 0) {
                        $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
                        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
                        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
                        $netoE = 0;
                        $totalNetoE = 0;
                        $totalivasE = 0;
                        $controw++;

                    }

                    $idetiqueta = $cobro->id_etiq;
                    $pdf->Cell(30, $alto, utf8_decode("Etiqueta : " . $cobro->name), '', 1, 'L', 0, 0);
                    $tdid = 0;
                    $controw++;
                }
            }
            if ($cobro->tdid <> $tdid) {
                if (!empty($cobro->deid)) {
                    $tdid = $cobro->tdid;
                    $pdf->SetFont('Arial', 'B', 9); // titulos
                    $pdf->Cell(40, $alto, utf8_decode("Tipo : " . $cobro->tdnombre), '', 1, 'R', 0, 0);
                }
            }

            $etiqueta = $cobro->name;

            $pdf->SetFont('Arial', '', 7); // titulos
            $pdf->Cell(15, 6, $cobro->defecha, '', 0, 'L', 0, 0);
            $pdf->Cell(23, 6, $cobro->deid, '', 0, 'C', 0, 0);
            $pdf->Cell(55, 6, substr(utf8_decode(ucwords(strtolower($cobro->cename))), 0, 46), '', 0, 'L', 0, 0);
            $pdf->Cell(20, 6, $cobro->derefer, '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->deabono + $cobro->decopensa), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($cobro->detotal - ($cobro->deabono + $cobro->decopensa)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, $cobro->devence, '', 0, 'R', 0, 0);
            $pdf->Cell(12, 6, $cobro->vencidos, '', 1, 'R', 0, 0);
            $pdf->SetFont('Arial', '', 7); // titulos

            $netoD += $cobro->detotal;
            $totalNetoD += $cobro->deabono + $cobro->decopensa;
            $totalivasD += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

            $netoE += $cobro->detotal;
            $totalNetoE += $cobro->deabono + $cobro->decopensa;
            $totalivasE += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

            $netoG += $cobro->detotal;
            $totalNetoG += $cobro->deabono + $cobro->decopensa;
            $totalivasG += $cobro->detotal - ($cobro->deabono + $cobro->decopensa);

            $controw++;
            if ($controw >= 36) {
                $controw = 0;
            }
        }
        $pdf->SetFont('Arial', 'B', 7); // titulos
        if ($netoD != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $cobro->tdnombre . " :   " . FData::formatoNumeroReportes($netoD)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoD), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasD), '', 1, 'L', 0, 0);
        }
        if ($netoE != 0) {
            $pdf->Cell(129, 6, utf8_decode("Total " . $etiqueta . " :   " . FData::formatoNumeroReportes($netoE)), '', 0, 'R', 0, 0);
            $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoE), '', 0, 'R', 0, 0);
            $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasE), '', 1, 'L', 0, 0);
        }
        $pdf->Cell(129, 6, utf8_decode("Total General : " . " :   " . FData::formatoNumeroReportes($netoG)), '', 0, 'R', 0, 0);
        $pdf->Cell(16, 6, FData::formatoNumeroReportes($totalNetoG), '', 0, 'R', 0, 0);
        $pdf->Cell(44, 6, FData::formatoNumeroReportes($totalivasG), '', 1, 'L', 0, 0);
        $pdf->Ln(10);
    }
    if ($datosVacios) {
        echo '<script>
     var opcion = confirm("No existe información para los criterios seleccionados");
        if (opcion == true) {
            window.close();
        } else {
            window.close();
        }
        </script>';
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
