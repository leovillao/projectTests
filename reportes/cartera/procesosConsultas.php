<?php
date_default_timezone_set('America/Guayaquil'); // por ejemplo por poner algo
session_start();
require '../../core/modules/index/model/AnticipocabData.php';
require '../../core/modules/index/model/AnticipodetData.php';
require '../../core/modules/index/model/AnticipoData.php';
require '../../core/modules/index/model/CrucecabData.php';
require '../../core/modules/index/model/CobroscabData.php';
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
//$rs = AnticipocabData::getByAllAnticiposAllReportes();
//foreach ($rs as $r) {
//    $va[] = $r->anvalor;
//    $ap[] = $r->aplicado;
//    $an[] = $r->ansaldo;
//    $saldo[] = $r->anvalor - $r->aplicado;
//}
//
//var_dump(array_sum($va));
//var_dump("Aplicado // ".array_sum($ap));
//var_dump(array_sum($an));
//var_dump(array_sum($saldo));
//$compensaciones = CobroscabData::getCompensacionesDiferncias();
//$proceso = new CobroscabData();
//$processFallo = false;
//if ($proceso->AbrirTransaccion() == false) {
//    $processFallo = true;
//} else {
//    $conx1 = $proceso::$Conx;
//    foreach ($compensaciones as $compensacione) {
//        if ($compensacione->difer != 0) {
//            if ($processFallo == false) {
//                $t = CobroscabData::update_T(1, date('Y-m-d H:i:s'), $compensacione->crid, $conx1);
//                if ($t[0] == false) {
//                    $processFallo = true;
//                    $msj = $t[2];
//                }
//            }
//        }
//    }
//}
////$processFallo = true;
//if ($processFallo == true) {
//    $proceso->CancelarTransaccion($conx1);
//    $respuesta = $msj;
//}else{
//    $proceso->CerrarTransaccion($conx1);
//    $respuesta = "procesos exitoso";
//}
//echo $respuesta;

$deudas = DeudasData::getAgrupadosDeid();
foreach ($deudas as $deuda) {
//    $valorCobr = DeudasData::updateAbonoProcesosO($deuda->deid);
//    $valorCobro = DeudasData::updateAbonoProcesos($deuda->valorcompensado,$deuda->deid);
//    var_dump($valorCobro);
}

$cobros = DeudasData::getAgrupadosCobrosDeuda();
//foreach ($cobros as $cobro) {
//    $valorCo = DeudasData::updateAbonoProcesosO($cobro->deid);
//    var_dump($valorCo);
//}
//foreach ($cobros as $cobro) {
//    $valorCobro = DeudasData::updateAbonoCobro($cobro->valorcobrado, $cobro->deid);
//    var_dump($valorCobro) . "<br>";
//}
//var_dump($cobros)."<br>";
$update = DeudasData::updateSaldoAllDeudas();
var_dump($update);
