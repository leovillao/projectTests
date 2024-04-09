<?php
//echo "procesos";
$d = new DeudasData();
$processError = false;
$msj = "sin error";
if ($d->AbrirTransaccion() == false) {
    $processError = true;
    $msj = "0-Error al abrir transaccion";
} else {
    $conx1 = $d::$Conx;
    foreach (DeudasData::getDeudasForAjusteSaldo() as $deudas) {
        $rr = DeudasData::updateSaldo_t($deudas->abonado, $deudas->abonadoDedua, $deudas->deid, $conx1);
        if ($rr[0] == false) {
            $processError = true;
            $msj = "0-" . $rr[2];
        }
    }
}
$processError = true;
if ($processError == true) {
    $d->CancelarTransaccion($conx1);
    echo json_encode($msj);
}else{
    $d->CerrarTransaccion($conx1);
    echo json_encode("Procesado con exito");
}

//echo count(DeudasData::getDeudasForAjusteSaldo());