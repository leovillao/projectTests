<?php

//ini_set('default_socket_timeout', 600);

class comprobanteGeneral {

    public $ambiente; // string
    public $tipoEmision; // string
    public $razonSocial; // string
    public $nombreComercial; // string
    public $ruc; // string
    public $claveAcceso; // string
    public $codDoc; // string
    public $estab; // string
    public $ptoEmi; // string
    public $secuencial; // string
    public $dirMatriz; // string
    public $fechaEmision; // string
    public $dirEstablecimiento; // string
    public $obligadoContabilidad; // string
    


}

class factura extends comprobanteGeneral {

    public $detalles; // detalleFactura
    public $guiaRemision; // string
    public $identificacionComprador; // string
    public $importeTotal; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $pagos; // pago
    public $propina; // string
    public $razonSocialComprador; // string
    public $tipoIdentificacionComprador; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalDescuento; // string
    public $totalSinImpuestos; // string

}

class detalleFactura {

    public $cantidad; // string
    public $codigoAuxiliar; // string
    public $codigoPrincipal; // string
    public $descripcion; // string
    public $descuento; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class liquidacionCompra extends comprobanteGeneral {

    public $detalles; // detalleLiquidacionCompra
    public $direccionProveedor; // string
    public $identificacionProveedor; // string
    public $importeTotal; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $razonSocialProveedor; // string
    public $tipoIdentificacionProveedor; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalDescuento; // string
    public $totalSinImpuestos; // string
    public $pagos;

}

class detalleLiquidacionCompra {

    public $cantidad; // string
    public $codigoAuxiliar; // string
    public $codigoPrincipal; // string
    public $descripcion; // string
    public $descuento; // string
    public $detalleAdicional; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class detalleAdicional {

    public $nombre; // string
    public $valor; // string

}

class pago {

    public $formaPago; // string
    public $total; // string
    public $plazo; // string
    public $unidadTiempo; // string

}

class impuesto {

    public $baseImponible; // string
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $tarifa; // string
    public $valor; // string

}

class campoAdicional {

    public $nombre; // string
    public $valor; // string

}

class totalImpuesto {

    public $baseImponible; // string
    public $codigo; // string
    public $codigoPorcentaje; // string
    public $descuentoAdicional; // string
    public $tarifa; // string
    public $valor; // string

}

class guiaRemision extends comprobanteGeneral {

    public $destinatarios; // destinatario
    public $dirPartida; // string
    public $fechaFinTransporte; // string
    public $fechaIniTransporte; // string
    public $infoAdicional; // campoAdicional
    public $placa; // string
    public $razonSocialTransportista; // string
    public $rise; // string
    public $rucTransportista; // string
    public $tipoIdentificacionTransportista; // string

}

class destinatario {

    public $codDocSustento; // string
    public $codEstabDestino; // string
    public $detalles; // detalleGuiaRemision
    public $dirDestinatario; // string
    public $docAduaneroUnico; // string
    public $fechaEmisionDocSustento; // string
    public $identificacionDestinatario; // string
    public $motivoTraslado; // string
    public $numAutDocSustento; // string
    public $numDocSustento; // string
    public $razonSocialDestinatario; // string
    public $ruta; // string

}

class detalleGuiaRemision {

    public $cantidad; // string
    public $codigoAdicional; // string
    public $codigoInterno; // string
    public $descripcion; // string
    public $detallesAdicionales; // detalleAdicional

}

class comprobanteRetencion extends comprobanteGeneral {

 	//public $infoTributaria; // Comprobante general
 	public $identificacionSujetoRetenido; // string
    public $impuestos; // impuestoComprobanteRetencion
    public $infoAdicional; // campoAdicional
    public $periodoFiscal; // string
    public $razonSocialSujetoRetenido; // string
    public $tipoIdentificacionSujetoRetenido; // string
    

}

class impuestoComprobanteRetencion {

    public $baseImponible; // string
    public $codDocSustento; // string
    public $codigo; // string
    public $codigoRetencion; // string
    public $fechaEmisionDocSustento; // string
    public $numDocSustento; // string
    public $porcentajeRetener; // string
    public $valorRetenido; // string

}

class notaDebito extends comprobanteGeneral {

    public $codDocModificado; // string
    public $fechaEmisionDocSustento; // string
    public $identificacionComprador; // string
    public $impuestos; // impuesto
    public $infoAdicional; // campoAdicional
    public $motivos; // motivo
    public $numDocModificado; // string
    public $razonSocialComprador; // string
    public $rise; // string
    public $tipoIdentificacionComprador; // string
    public $totalSinImpuestos; // string
    public $valorTotal; // string

}

class motivo {

    public $razon; // string
    public $valor; // string

}

class notaCredito extends comprobanteGeneral {

    public $codDocModificado; // string
    public $detalles; // detalleNotaCredito
    public $fechaEmisionDocSustento; // string
    public $identificacionComprador; // string
    public $infoAdicional; // campoAdicional
    public $moneda; // string
    public $motivo; // string
    public $numDocModificado; // string
    public $razonSocialComprador; // string
    public $rise; // string
    public $tipoIdentificacionComprador; // string
    public $totalConImpuesto; // totalImpuesto
    public $totalSinImpuestos; // string
    public $valorModificacion; // string

}

class detalleNotaCredito {

    public $cantidad; // string
    public $codigoAdicional; // string
    public $codigoInterno; // string
    public $descripcion; // string
    public $descuento; // string
    public $detallesAdicionales; // detalleAdicional
    public $impuestos; // impuesto
    public $precioTotalSinImpuesto; // string
    public $precioUnitario; // string

}

class comprobantePendiente {

    public $ambiente; // string
    public $codDoc; // string
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $establecimiento; // string
    public $fechaEmision; // string
    public $ptoEmision; // string
    public $ruc; // string
    public $secuencial; // string
    public $tipoEmision; // string
    public $clavAcc;
    public $enviarEmail; // string
    public $otrosDestinatarios;

}

class procesarComprobanteLote {

    public $comprobanteLote; // comprobanteLote

}

class comprobanteLote {

    public $ambiente; // string
    public $claveAcceso; // string
    public $codDoc; // string
    public $comprobantes; // comprobanteGeneral
    public $configAplicacion; // configAplicacion
    public $configCorreo; // configCorreo
    public $establecimiento; // string
    public $fechaEmision; // string
    public $idUnico; // string
    public $ptoEmision; // string
    public $ruc; // string
    public $secuencial; // string
    public $tipoEmision; // string

}

class procesarComprobanteLoteResponse {

    public $return; // respuestaComprobanteLote

}

class respuestaComprobanteLote {

    public $claveAccesoConsultada; // string
    public $error; // boolean
    public $mensajeGeneral; // mensajeGenerado
    public $respuestas; // respuesta

}

class mensajeGenerado {

    public $identificador; // string
    public $informacionAdicional; // string
    public $mensaje; // string
    public $tipo; // string

}

class respuesta {

    public $claveAcceso; // string
    public $comprobanteID; // string
    public $estadoComprobante; // string
    public $mensajes; // mensajeGenerado
    public $numeroAutorizacion; // string
    public $fechaAutorizacion;

}

class procesarComprobantePendiente {

    public $comprobantePendiente; // comprobantePendiente

}

class procesarComprobantePendienteResponse {

    public $return; // respuesta

}

class procesarComprobante {

    public $comprobante; // comprobanteGeneral
    public $envioSRI;

}

class procesarComprobanteResponse {

    public $return; // respuesta

}

?>
