<?php
date_default_timezone_set('America/Guayaquil');
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CobroscabData.php';
require '../../core/modules/index/model/CobrosdetData.php';
require '../../core/modules/index/model/BodegasData.php';
require '../../core/modules/index/model/ConfigurationData.php';
require '../../core/modules/index/model/EmpresasData.php';
require '../../core/modules/index/model/EntidadesData.php';
require '../../core/modules/index/model/FilesData.php';
require '../../core/modules/index/model/ProductData.php';
require '../../core/modules/index/model/UnitData.php';
require '../../core/modules/index/model/OperationData.php';
require '../../core/modules/index/model/OperationdetData.php';
require '../../core/modules/index/model/OperationdifData.php';
require '../../core/modules/index/model/FormasData.php';
require '../../core/modules/index/model/PersonData.php';
require '../../core/modules/index/model/RsmData.php';
require '../../core/modules/index/model/SecuenciaData.php';
require '../../core/modules/index/model/SucursalData.php';
require '../../core/modules/index/model/TipoOperationData.php';
require '../../core/modules/index/model/UserData.php';
require '../../core/modules/index/model/BoxData.php';
require '../../core/controller/Executor.php';
require '../../core/controller/Database.php';
require '../../core/controller/Core.php';
require '../../core/controller/Model.php';
require '../../core/controller/Fpdf/fpdf.php';
require '../../core/controller/Fpdf/code128.php';

$caja = BoxData::getById($_GET['id']);


$numDecimales = ConfigurationData::getByShortName("conf_num_decimal_precio")->cgdatoi;

$user = UserData::getById($_SESSION['user_id']); // usuario
$empresa = EmpresasData::getById(1); // Datos empresas
$razonSocial = $empresa->em_razon;
$nombreComercial = $empresa->em_comercial;
$dirMatriz = $empresa->em_dirmatriz;
$microEmpresa = $empresa->micro_emp;
$agentRetencion = $empresa->agent_ret;
$expHabitual = $empresa->exp_hab;
/**
 * `id`, `user_id`, `user_name`, `estado`, `factivas`, `fanuladas`, `totalventa`, `totalcont`, `totalcred`, `totalanti`, `totalncr`, `v_apertura`, `v_efectivo`, `v_cheque`, `v_tarjeta`, `v_retenci`, `v_transfer`, `v_rubro1`, `v_rubro2`, `v_rubro3`, `v_rubro4`, `v_rubro5`, `v_consumo`, `comentario`, `sucursal_id`, `emi_id`, `created_at`, `cierre_at`, `anula_at`, `mot_anulacion`
 */

// $pdf = new FPDF($orientation='P',$unit='mm', array(70,250));
$pdf = new FPDF($orientation = 'P', $unit = 'mm', array(80, 420));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 4, "CIERRE CAJA # " . $_GET['id'], 0, 1, 'C', 0, 1);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 5, "Usuario : " . $caja->user_name, 0, 1, 'L', 0, 1);
$pdf->Cell(60, 6, "Sucursal : " . SucursalData::getById($caja->sucursal_id)->suname, 0, 1, 'L', 0, 1);
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 6, "Fecha Apertura : " . $caja->created_at, 0, 1, 'L', 0, 1);
$pdf->Cell(60, 6, "Fecha Cierre : " . $caja->cierre_at, 0, 1, 'L', 0, 1);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 13);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 7, "Segun Sistema.", 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Ln(5);

$pdf->Cell(30, 5, "Facturas activas : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->factivas, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Facturas Anuladas : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->fanuladas, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Venta Contado : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->totalcont, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Total de Credito : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->totalcred, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Total de Venta : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->totalventa, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Total Anticipo : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->totalanti, 'T,B,R,L', 1, 'R', 0, 1);

$totalSistema = number_format($caja->totalventa + $caja->totalanti, 2);

$pdf->SetFont('Arial', 'B', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(30, 5, "Total Sistema : ", 'T,B,R,L', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $totalSistema, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$totalUsuario = 0;
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 13);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Cell(60, 5, "Segun Usuario.", 0, 1, 'L', 0, 1);
$pdf->SetFont('Arial', '', 9);    //Letra Arial, negrita (Bold), tam. 20
$pdf->Ln(5);

$pdf->Cell(30, 5, "Valor efectivo : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_efectivo, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Valor Cheque : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_cheque, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Valor Tarjeta : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_tarjeta, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Valor Retencion : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_retenci, 'T,B,R,L', 1, 'R', 0, 1);
$pdf->Cell(30, 5, "Valor transferencia : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_transfer, 'T,B,R,L', 1, 'R', 0, 1);

$totalUsuario = $caja->v_efectivo + $caja->v_cheque + $caja->v_tarjeta + $caja->v_retenci + $caja->v_transfer;

if ($caja->v_rubro1 != 0) {
    $pdf->Cell(40, 5, "Valor " . ConfigurationData::getByName('Rubro1')->cgdatov . ":", 'T,B,R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $caja->v_rubro1, 'T,R,B,L', 1, 'R', 0, 1);
    $totalUsuario += $caja->v_rubro1;
}
if ($caja->v_rubro2 != 0) {
    $pdf->Cell(40, 5, "Valor " . ConfigurationData::getByName('Rubro2')->cgdatov . ":", 'T,B,R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $caja->v_rubro2, 'T,R,B,L', 1, 'R', 0, 1);
    $totalUsuario += $caja->v_rubro2;
}
if ($caja->v_rubro3 != 0) {
    $pdf->Cell(40, 5, "Valor " . ConfigurationData::getByName('Rubro3')->cgdatov . ":", 'T,B,R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $caja->v_rubro3, 'T,R,B,L', 1, 'R', 0, 1);
    $totalUsuario += $caja->v_rubro3;
}
if ($caja->v_rubro4 != 0) {
    $pdf->Cell(40, 5, "Valor " . ConfigurationData::getByName('Rubro4')->cgdatov . ":", 'T,B,R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $caja->v_rubro4, 'T,R,B,L', 1, 'R', 0, 1);
    $totalUsuario += $caja->v_rubro4;
}
if ($caja->v_rubro5 != 0) {
    $pdf->Cell(40, 5, "Valor " . ConfigurationData::getByName('Rubro5')->cgdatov . ":", 'T,B,R,L', 0, 'L', 0, 1);
    $pdf->Cell(20, 5, $caja->v_rubro5, 'T,R,B,L', 1, 'R', 0, 1);
    $totalUsuario += $caja->v_rubro5;
}

$pdf->Cell(30, 5, "Valor apertura : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $caja->v_consumo, 'T,B,L,R', 1, 'R', 0, 1);

$totalUsuario += $caja->v_consumo;

$pdf->Cell(30, 5, "Total Usuario : ", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, $totalUsuario, 'T,B,L,R', 1, 'R', 0, 1);

$pdf->Cell(30, 5, "Diferencia :", 'T,B,L,R', 0, 'L', 0, 1);
$pdf->Cell(30, 5, number_format($totalSistema - $totalUsuario, 2), 'T,B,L,R', 1, 'R', 0, 1);

$pdf->Ln(5);

$pdf->Cell(60, 5, "Comentario :", 'T,B,L,R', 1, 'L', 0, 1);
$pdf->MultiCell(60, 6, $caja->comentario, 'L,R,B,T', 'L', 0);
//$pdf->Cell(60, 5, $caja->comentario,'T,B,L,R', 1, 'L', 0, 1);


$pdf->output();
