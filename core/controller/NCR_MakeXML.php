<?php

class FabricaNcr
{
    public static function CreaNcr($objCabNcr, $objDetNcr, $objDifNcr)
    {
      $empresa = EmpresasData::getById(1);
      $factura = new \notaCredito();
        $factura->ambiente = $objCabNcr->ambiente;
        $factura->tipoEmision = $objCabNcr->tipoEmision;
        $factura->razonSocial = $objCabNcr->razonSocial;
        $factura->nombreComercial = $objCabNcr->nombreComercial;
        $factura->ruc = $objCabNcr->ruc;
        $factura->claveAcceso = '';
        $factura->codDoc = $objCabNcr->codDoc;
        $factura->estab = $objCabNcr->estab;
        $factura->ptoEmi = $objCabNcr->ptoEmi;
        $factura->secuencial = $objCabNcr->secuencial;
        $factura->dirMatriz = $objCabNcr->dirMatriz;
        /*if ($empresa->micro_emp == 1) {
            $factura->regimenMicroempresas = "CONTRIBUYENTE RÉGIMEN MICROEMPRAS";
        }*/
        if ($empresa->agent_ret == 1) {
            $factura->agenteRetencion = $empresa->agent_ret;
        }
        $factura->fechaEmision = $objCabNcr->fechaEmision;

        $claveAcceso = self::claveAcceso($objCabNcr->codDoc, $objCabNcr->ruc, $objCabNcr->ambiente, $objCabNcr->estab.$objCabNcr->ptoEmi.$objCabNcr->secuencial, $objCabNcr->fechaEmision);
        self::updateClaveAcceso($claveAcceso,$objCabNcr->puntero);
        $factura->dirEstablecimiento = $objCabNcr->dirEstablecimiento;
        $factura->tipoIdentificacionComprador = $objCabNcr->tipoIdentificacionComprador;
        $factura->razonSocialComprador = $objCabNcr->razonSocialComprador;
        $factura->identificacionComprador = $objCabNcr->identificacionComprador;

        if ($empresa->contribuyenteEspecial != "000" || $empresa->contribuyenteEspecial != "") {
            $factura->contribuyenteEspecial = $objCabNcr->contribuyenteEspecial;
        }
        $factura->obligadoContabilidad = $objCabNcr->obligadoContabilidad;
        if (empty($objCabNcr->direccionComprador)) {
            $direccion = '.';
        } else {
            $direccion = $objCabNcr->direccionComprador;
        }
        $factura->codDocModificado = $objCabNcr->codDocModificado;
        $factura->numDocModificado = $objCabNcr->numDocModificado;
        $factura->fechaEmisionDocSustento = $objCabNcr->fechaEmisionDocSustento;
        $factura->totalSinImpuestos = $objCabNcr->totalSinImpuestos; // traer en consulta Sql
        $factura->valorModificado = $objCabNcr->valorModificado; // traer en consulta Sql
        $factura->moneda = $objCabNcr->moneda;

        $factura->direccionComprador = $direccion;
        $factura->propina = $objCabNcr->propina;
        $factura->importeTotal = $objCabNcr->importeTotal;
        $factura->totalDescuento = $objCabNcr->totalDescuento; // traer en consulta Sql

        if ($objCabNcr->grabado) {
            $detTotalConImpuestos = new \impuesto();
            $codigo = 2;
            $tarifa = 12;
            $baseImponible = $objCabNcr->grabado;
            $valor = $objCabNcr->iva;
            $detTotalConImpuestos->codigo = $codigo;
            $detTotalConImpuestos->codigoPorcentaje = $codigo;
            $detTotalConImpuestos->tarifa = $tarifa;
            $detTotalConImpuestos->baseImponible = $baseImponible;
            $detTotalConImpuestos->valor = $valor;
            $arrayTotalConImpuestos[] = $detTotalConImpuestos;
        }
        if ($objCabNcr->exento) {
            $detTotalConImpuestos = new \impuesto();
            $codigo = 2;
            $tarifa = 0;
            $baseImponible = $objCabNcr->exento;
            $valor = 0;
            $detTotalConImpuestos->codigo = $codigo;
            $detTotalConImpuestos->codigoPorcentaje = 0;
            $detTotalConImpuestos->tarifa = $tarifa;
            $detTotalConImpuestos->baseImponible = $baseImponible;
            $detTotalConImpuestos->valor = $valor;
            $arrayTotalConImpuestos[] = $detTotalConImpuestos;
        }
        $factura->totalConImpuestos = $arrayTotalConImpuestos;
        //Recorro todos los elementos
        $detalleArray = array();
        $name = '';
        $codigoPro = 0;

        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DETALLE
        foreach ($objDetNcr as $d) {

            if (!empty($d->shortname)) {
                $name = $d->shortname;
                if (!empty($d->descript)) {
                    $codigoPro = $d->codigoPrincipal;
                } else {
                    $codigoPro = $d->codigoPrincipal;
                }
            } else {
                $name = $d->descripcion;
                $codigoPro = $d->codigoPrincipal;
            }

            $detalleFactura = new \detalleNotaCredito();
            $detalleFactura->cantidad = $d->cantidad;
            $detalleFactura->codigoAuxiliar = $codigoPro;
            $detalleFactura->codigoPrincipal = $codigoPro;
            $detalleFactura->descripcion = $name;

            $detalleFactura->precioUnitario = 4;
            $detalleFactura->descuento = 5;
            $detalleFactura->precioTotalSinImpuesto = 9;
            $impuestDetArray = [];
            $detaImpuesto = new \impuesto();
            if ($d->tarifa == 0) {
                $codigo = 2;
                $codigoPorcentaje = 0;
            } else {
                $codigo = 2;
                $codigoPorcentaje = 2;
            }

            $detaImpuesto->codigo = $codigo;
            $detaImpuesto->codigoPorcentaje = 45;
            $detaImpuesto->tarifa = 232;
            $detaImpuesto->baseImponible = 4;
            $detaImpuesto->valor = 3;

            $impuestDetArray = array($detaImpuesto);
            $detalleFactura->impuesto = $impuestDetArray;

            $detalleArray[] = $detalleFactura;
        }
        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DIFERIDO
        if (!empty($objDifNcr) || $objDifNcr != null) {
            foreach ($objDifNcr as $d) {
                if (!empty($d->shortname)) {
                    $name = $d->shortname;
                    if (!empty($d->descript)) {
                        $codigoPro = $d->codigoPrincipal;
                    } else {
                        $codigoPro = $d->codigoPrincipal;
                    }
                } else {
                    $name = $d->descripcion;
                    $codigoPro = $d->codigoPrincipal;
                }
                $detalleFactura = new \detalleFactura();
                $detalleFactura->cantidad = $d->cantidad;
                $detalleFactura->codigoAuxiliar = $codigoPro;
                $detalleFactura->codigoPrincipal = $codigoPro;
                $detalleFactura->descripcion = $name;
                if ($d->valDesc != 0) {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = $d->valDesc;
                    $precioTotalSinImpuesto = $d->totDesc;
                    $baseImponible = $d->totDesc;
                } else {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = $d->valDesc;
                    $precioTotalSinImpuesto = $d->cantidad * $d->precioUnitario;
                    $baseImponible = $d->cantidad * $d->precioUnitario;
                }
                $detalleFactura->precioUnitario = number_format($precioUnitario,2,'.','');
                $detalleFactura->descuento = $descuento;
                $detalleFactura->precioTotalSinImpuesto = number_format($precioTotalSinImpuesto,2,'.','');
                $impuestDetArray = [];
                $detaImpuesto = new \impuesto();
                if ($d->tarifa == 0) {
                    $codigo = 2;
                    $codigoPorcentaje = 0;
                } else {
                    $codigo = 2;
                    $codigoPorcentaje = 2;
                }
                $detaImpuesto->codigo = $codigo;
                $detaImpuesto->codigoPorcentaje = $codigoPorcentaje;
                $detaImpuesto->tarifa = number_format($d->tarifa,0);
                $detaImpuesto->baseImponible = number_format($baseImponible,2,'.','');
                $detaImpuesto->valor = number_format($d->valorIva,2,'.','');
                $impuestDetArray = array($detaImpuesto);
                $detalleFactura->impuesto = $impuestDetArray;
                $detalleArray[] = $detalleFactura;
            }
        }
        // SE INSERTA EL ARRAY CON EL DETALLE DE PRODUCTOS DE LA FACTURA

        $factura->detalles = $detalleArray;
        $camposAdicionales[] = array();
        $direccion = "";
        if (!empty($objCabNcr->direccionComprador)) {
            $direccion = $objCabNcr->direccionComprador;
        } else {
            $direccion = ".";
        }
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'direccionComprador';
        $campoAdicional->valor = $direccion;
        $camposAdicionales[] = $campoAdicional;
        $emailers = '';
        if (!empty($objCabNcr->email1)) {
            $emailers .= $objCabNcr->email1 . ",";
        } else {
            $emailers .= ".";
        }
        if (!empty($objCabNcr->email2)) {
            $emailers .= $objCabNcr->email2 . ",";
        } else {
            $emailers .= ".";
        }
        $telefono = '';
        if (!empty($objCabNcr->phono1)) {
            $telefono .= $objCabNcr->telefono1 . ",";
        } else {
            $telefono .= ".";
        }
        if (!empty($objCabNcr->phono2)) {
            $telefono .= $objCabNcr->telefono2 . ",";
        }
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'correoCliente';
        $campoAdicional->valor = $emailers;
        $camposAdicionales[] = $campoAdicional;
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'telefonoCliente';
        $campoAdicional->valor = $telefono;
        $camposAdicionales[] = $campoAdicional;
        if (count($camposAdicionales) > 0) {
            $factura->infoAdicional = $camposAdicionales;
        }
        return $factura;

    }

    public static function updateClaveAcceso($clave,$id){
      $update = FilesData::UpdateClaveAcceso($clave,$id);

    }

    public static function CreaNcr1($objCabRet, $objDetRet, $objDifRet, $objDetPagos)
    {
        $factura = new \notaCredito();
        $factura->ambiente = $objCabRet->ambiente;
        $factura->tipoEmision = $objCabRet->tipoEmision;
        $factura->razonSocial = $objCabRet->razonSocial;
        $factura->nombreComercial = $objCabRet->nombreComercial;
        $factura->ruc = $objCabRet->ruc;
        $factura->claveAcceso = '';
        $factura->codDoc = $objCabRet->codDoc;
        $factura->estab = $objCabRet->estab;
        $factura->ptoEmi = $objCabRet->ptoEmi;
        $factura->secuencial = $objCabRet->secuencial;
        $factura->dirMatriz = $objCabRet->dirMatriz;
        $empresa = EmpresasData::getById(1);
        if ($empresa->micro_emp == 1) {
            $factura->regimenMicroempresas = "CONTRIBUYENTE RÉGIMEN MICROEMPRAS";
        }
        if ($empresa->agent_ret == 1) {
            $factura->agenteRetencion = $empresa->agent_ret;
        }
        $factura->fechaEmision = $objCabRet->fechaEmision;
        $factura->dirEstablecimiento = $objCabRet->dirEstablecimiento;
        $factura->tipoIdentificacionComprador = $objCabRet->tipoIdentificacionComprador;
        $factura->razonSocialComprador = $objCabRet->razonSocialComprador;
        $factura->identificacionComprador = $objCabRet->identificacionComprador;
        if ($objCabRet->contribuyenteEspecial != "000" || $objCabRet->contribuyenteEspecial != "") {
            $factura->contribuyenteEspecial = $objCabRet->contribuyenteEspecial;
        }
        $factura->obligadoContabilidad = $objCabRet->obligadoContabilidad;
        if (empty($objCabRet->direccionComprador)) {
            $direccion = '.';
        } else {
            $direccion = $objCabRet->direccionComprador;
        }
        $factura->codDocModificado = $objCabRet->codDocModificado;
        $factura->numDocModificado = $objCabRet->numDocModificado;
        $factura->fechaEmisionDocSustento = $objCabRet->fechaEmisionDocSustento;
        $factura->totalSinImpuestos = $objCabRet->totalSinImpuestos; // traer en consulta Sql
        $factura->valorModificado = $objCabRet->valorModificado; // traer en consulta Sql
        $factura->moneda = $objCabRet->moneda;

        $factura->direccionComprador = $direccion;
        $factura->propina = $objCabRet->propina;
        $factura->importeTotal = $objCabRet->importeTotal;
        $factura->totalDescuento = $objCabRet->totalDescuento; // traer en consulta Sql
        foreach ($objDetPagos as $objPago) {
            $detPagos = new \pago(); /* Formas de Pago */
            $detPagos->formaPago = $objPago->formaPago;
            $detPagos->total = $objPago->total;
            $detPagos->plazo = $objPago->plazo;
            $detPagos->unidadTiempo = $objPago->unidadTiempo;
            $arrayPagos[] = $detPagos;
        }
        $factura->pagos = $arrayPagos;
        if ($objCabRet->grabado) {
            $detTotalConImpuestos = new \impuesto();
            $codigo = 2;
            $tarifa = 12;
            $baseImponible = $objCabRet->grabado;
            $valor = $objCabRet->iva;
            $detTotalConImpuestos->codigo = $codigo;
            $detTotalConImpuestos->codigoPorcentaje = $codigo;
            $detTotalConImpuestos->tarifa = $tarifa;
            $detTotalConImpuestos->baseImponible = $baseImponible;
            $detTotalConImpuestos->valor = $valor;
            $arrayTotalConImpuestos[] = $detTotalConImpuestos;
        }
        if ($objCabRet->exento) {
            $detTotalConImpuestos = new \impuesto();
            $codigo = 2;
            $tarifa = 0;
            $baseImponible = $objCabRet->exento;
            $valor = 0;
            $detTotalConImpuestos->codigo = $codigo;
            $detTotalConImpuestos->codigoPorcentaje = 0;
            $detTotalConImpuestos->tarifa = $tarifa;
            $detTotalConImpuestos->baseImponible = $baseImponible;
            $detTotalConImpuestos->valor = $valor;
            $arrayTotalConImpuestos[] = $detTotalConImpuestos;
        }
        $factura->totalConImpuestos = $arrayTotalConImpuestos;
        //Recorro todos los elementos
        $detalleArray = array();
        $name = '';
        $codigoPro = 0;
        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DETALLE
        foreach ($objDetRet as $d) {

            if (!empty($d->shortname)) {
                $name = $d->shortname;
                if (!empty($d->descript)) {
                    $codigoPro = $d->descript;
                } else {
                    $codigoPro = $d->codigoPrincipal;
                }
            } else {
                $name = $d->descripcion;
                $codigoPro = $d->codigoPrincipal;
            }

            $detalleFactura = new \detalleNotaCredito();
            $detalleFactura->cantidad = $d->cantidad;
            $detalleFactura->codigoInterno = $codigoPro;
//            $detalleFactura->codigoPrincipal = $codigoPro;
            $detalleFactura->descripcion = $name;

            if ($d->valDesc != 0) {
                $precioUnitario = $d->precioUnitario;
                $descuento = $d->valDesc;
                $precioTotalSinImpuesto = $d->totDesc;
                $baseImponible = $d->totDesc;
            } else {
                $precioUnitario = $d->precioUnitario;
                $descuento = $d->valDesc;
                $precioTotalSinImpuesto = $d->baseImponible;
                $baseImponible = $d->baseImponible;
            }

            $detalleFactura->precioUnitario = $precioUnitario;
            $detalleFactura->descuento = $descuento;
            $detalleFactura->precioTotalSinImpuesto = $precioTotalSinImpuesto;
            $impuestDetArray = [];
            $detaImpuesto = new \impuesto();
            if ($d->tarifa == 0) {
                $codigo = 2;
                $codigoPorcentaje = 0;
            } else {
                $codigo = 2;
                $codigoPorcentaje = 2;
            }
            $detaImpuesto->codigo = $codigo;
            $detaImpuesto->codigoPorcentaje = $codigoPorcentaje;
            $detaImpuesto->tarifa = $d->tarifa;
            $detaImpuesto->baseImponible = $baseImponible;
            $detaImpuesto->valor = $d->valorIva;
            $impuestDetArray = array($detaImpuesto);
            $detalleFactura->impuesto = $impuestDetArray;
            $detalleArray[] = $detalleFactura;
        }
        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DIFERIDO
        if (!empty($objDifRet) || $objDifRet != null) {
            foreach ($objDifRet as $d) {
                if (!empty($d->shortname)) {
                    $name = $d->shortname;
                    if (!empty($d->descript)) {
                        $codigoPro = $d->descript;
                    } else {
                        $codigoPro = $d->codigoPrincipal;
                    }
                } else {
                    $name = $d->descripcion;
                    $codigoPro = $d->codigoPrincipal;
                }
                $detalleFactura = new \detalleFactura();
                $detalleFactura->cantidad = $d->cantidad;
                $detalleFactura->codigoInterno = $codigoPro;
//                $detalleFactura->codigoPrincipal = $codigoPro;
                $detalleFactura->descripcion = $name;
                if ($d->valDesc != 0) {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = $d->valDesc;
                    $precioTotalSinImpuesto = $d->totDesc;
                    $baseImponible = $d->totDesc;
                } else {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = $d->valDesc;
                    $precioTotalSinImpuesto = $d->precioTotalSinImpuesto;
                    $baseImponible = $d->precioTotalSinImpuesto;
                }
                $detalleFactura->precioUnitario = $precioUnitario;
                $detalleFactura->descuento = $descuento;
                $detalleFactura->precioTotalSinImpuesto = $precioTotalSinImpuesto;
                $impuestDetArray = [];
                $detaImpuesto = new \impuesto();
                if ($d->tarifa == 0) {
                    $codigo = 2;
                    $codigoPorcentaje = 0;
                } else {
                    $codigo = 2;
                    $codigoPorcentaje = 2;
                }
                $detaImpuesto->codigo = $codigo;
                $detaImpuesto->codigoPorcentaje = $codigoPorcentaje;
                $detaImpuesto->tarifa = $d->tarifa;
                $detaImpuesto->baseImponible = $baseImponible;
                $detaImpuesto->valor = $d->valor;
                $impuestDetArray = array($detaImpuesto);
                $detalleFactura->impuesto = $impuestDetArray;
                $detalleArray[] = $detalleFactura;
            }
        }
        // SE INSERTA EL ARRAY CON EL DETALLE DE PRODUCTOS DE LA FACTURA
        $factura->detalles = $detalleArray;
        $camposAdicionales[] = array();
        $direccion = "";
        if (!empty($objCabRet->direccionComprador)) {
            $direccion = $objCabRet->direccionComprador;
        } else {
            $direccion = ".";
        }
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'direccionComprador';
        $campoAdicional->valor = $direccion;
        $camposAdicionales[] = $campoAdicional;
        $emailers = '';
        if (!empty($objCabRet->email1)) {
            $emailers .= $objCabRet->email1 . ",";
        } else {
            $emailers .= ".";
        }
        if (!empty($objCabRet->email2)) {
            $emailers .= $objCabRet->email2 . ",";
        } else {
            $emailers .= ".";
        }
        $telefono = '';
        if (!empty($objCabRet->phono1)) {
            $telefono .= $objCabRet->telefono1 . ",";
        } else {
            $telefono .= ".";
        }
        if (!empty($objCabRet->phono2)) {
            $telefono .= $objCabRet->telefono2 . ",";
        }
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'correoCliente';
        $campoAdicional->valor = $emailers;
        $camposAdicionales[] = $campoAdicional;
        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'telefonoCliente';
        $campoAdicional->valor = $telefono;
        $camposAdicionales[] = $campoAdicional;
        if (count($camposAdicionales) > 0) {
            $factura->infoAdicional = $camposAdicionales;
        }
        return $factura;

    }

    public static function getName($objCabRet)
    {
        $name = 'Ncr' . $objCabRet->ruc . $objCabRet->estab . $objCabRet->ptoEmi . $objCabRet->secuencial;
        return $name;
    }

    public static function GetXML($Objret)
    {
        $data = EmpresasData::getById(1);
        // // CONSTUYE EL XML en base a los datos del objeto retenci�n
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndentString("\t");
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement("notaCredito"); // elemento
        $xml->writeAttribute('id', 'comprobante');
        $xml->writeAttribute('version', '1.1.0');
        $xml->startElement("infoTributaria"); // elemento infoTributaria
        $xml->writeElement("ambiente", $Objret->ambiente);
        $xml->writeElement("tipoEmision", $Objret->tipoEmision);
        $xml->writeElement("razonSocial", $Objret->razonSocial);
        if ($Objret->nombreComercial == "") {
            $xml->writeElement("nombreComercial", $Objret->razonSocial);
        } else {
            $xml->writeElement("nombreComercial", $Objret->nombreComercial);
        }
        $xml->writeElement("ruc", $Objret->ruc);
        $fechaEmision = $Objret->fechaEmision;
        $docum = $Objret->estab;
        $docum .= $Objret->ptoEmi;
        $docum .= str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT);
        $xml->writeElement("claveAcceso", self::claveAcceso($Objret->codDoc, $Objret->ruc, $Objret->ambiente, $docum, $fechaEmision));

        $xml->writeElement("codDoc", $Objret->codDoc);
        $xml->writeElement("estab", $Objret->estab);
        $xml->writeElement("ptoEmi", $Objret->ptoEmi);
        $xml->writeElement("secuencial", str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT));
        $xml->writeElement("dirMatriz", $Objret->dirMatriz);
        if (isset($Objret->regimenMicroempresas)) {
            $xml->writeElement("regimenMicroempresas", $Objret->regimenMicroempresas);
        }
        if (isset($Objret->agenteRetencion)) {
            $xml->writeElement("agenteRetencion", $Objret->agenteRetencion);
        }
        $xml->endElement(); // fin elemento infoTributaria
        $xml->startElement("infoNotaCredito"); // elemento infoTributaria
        $xml->writeElement("fechaEmision", $Objret->fechaEmision);
        $xml->writeElement("dirEstablecimiento", $Objret->dirEstablecimiento);
        $xml->writeElement("tipoIdentificacionComprador", $Objret->tipoIdentificacionComprador);
        $xml->writeElement("razonSocialComprador", $Objret->razonSocialComprador);
        $xml->writeElement("identificacionComprador", $Objret->identificacionComprador);
        if ($Objret->contribuyenteEspecial) {
            $xml->writeElement("contribuyenteEspecial", $Objret->contribuyenteEspecial);
        }
        $xml->writeElement("obligadoContabilidad", $Objret->obligadoContabilidad);
//        $xml->writeElement("direccionComprador", $Objret->direccionComprador);
        $xml->writeElement("codDocModificado", $Objret->codDocModificado);
        $xml->writeElement("numDocModificado", substr($Objret->numDocModificado,0,3).'-'.substr($Objret->numDocModificado,3,3).'-'.substr($Objret->numDocModificado,6,10));
        $xml->writeElement("fechaEmisionDocSustento", date("d/m/Y", strtotime($Objret->fechaEmisionDocSustento)));
        $xml->writeElement("totalSinImpuestos", $Objret->totalSinImpuestos);
        $xml->writeElement("valorModificacion", $Objret->valorModificado);
        //        $xml->writeElement("totalDescuento", $Objret->totalDescuento);

        $xml->writeElement("moneda", $Objret->moneda);

        $xml->startElement("totalConImpuestos"); // elemento totalConImpuestos
        foreach ((array)$Objret->totalConImpuestos as $totalImpuestos) {
            $xml->startElement("totalImpuesto"); // elemento totalConImpuesto
            $xml->writeElement("codigo", $totalImpuestos->codigo);
            $xml->writeElement("codigoPorcentaje", $totalImpuestos->codigoPorcentaje);
            $xml->writeElement("baseImponible", $totalImpuestos->baseImponible);
//            $xml->writeElement("tarifa", $totalImpuestos->tarifa);
            $xml->writeElement("valor", $totalImpuestos->valor);
            $xml->endElement(); // fin elemento totalImpuesto
        }
        $xml->endElement(); // elemento totalConImpuestos
        $xml->writeElement("motivo", "motivo Anulacion");

        $xml->endElement(); // fin elemento infoTributaria
        $xml->startElement("detalles"); // elemento detalles
        foreach ($Objret->detalles as $detalleFact) {
            $xml->startElement("detalle"); // elemento detalle
            $xml->writeElement("codigoInterno", $detalleFact->codigoAuxiliar);
//            $xml->writeElement("codigoAuxiliar", $detalleFact->codigoAuxiliar);
            $xml->writeElement("descripcion", $detalleFact->descripcion);
            $xml->writeElement("cantidad", $detalleFact->cantidad);
            $xml->writeElement("precioUnitario", $detalleFact->precioUnitario);
            $xml->writeElement("descuento", $detalleFact->descuento);
            $xml->writeElement("precioTotalSinImpuesto", $detalleFact->precioTotalSinImpuesto);
//            $xml->writeElement("impuestos", $detalleFact->impuestos);
            $xml->startElement("impuestos"); // elemento impuesto
            foreach ($detalleFact->impuesto as $dImpuesto) {
                $xml->startElement("impuesto"); // elemento impuesto
                $xml->writeElement("codigo", $dImpuesto->codigo);
                $xml->writeElement("codigoPorcentaje", $dImpuesto->codigoPorcentaje);
                $xml->writeElement("tarifa", $dImpuesto->tarifa);
                $xml->writeElement("baseImponible", $dImpuesto->baseImponible);
                $xml->writeElement("valor", $dImpuesto->valor);
                $xml->endElement(); // fin elemento impuesto
            }
            $xml->endElement(); // fin elemento impuestos
            $xml->endElement(); // fin elemento detalle
        }
        $xml->endElement(); // fin elemento detalles
        $xml->startElement("infoAdicional"); // elemento impuestos
        foreach ($Objret->infoAdicional as $infoAdicional) {
            if (!empty($infoAdicional->nombre)) {
                $xml->startElement("campoAdicional");
                $xml->writeAttribute("nombre", $infoAdicional->nombre);
                $xml->text($infoAdicional->valor);
                $xml->endElement();
            }
        }
        $xml->endElement(); // fin elemento infoAdicional
        $xml->endElement(); // Fin retencion
        $xml->endDocument();
//        $pathXml = 'xml';
//        $pathXml = EmpresasData::getByRuc($_SESSION['ruc'])->path_xml;
        // $cadena = trim($xml->outputMemory());
        $xmls = $xml->outputMemory(TRUE);
        $xmlsimple = simplexml_load_string($xmls);
        $xml_file = $xmlsimple->asXML('xml/sinfirma/' . 'NCR' . $docum . '.xml');
        return $xml_file;
    }

    // SE GENERA LA CLAVE DE ACCESO DEL DOCUMENTO
    public static function claveAcceso($tipo, $emisor, $ambiente, $documento, $fechaEmision)
    {
        $fecha = str_replace("/", "", $fechaEmision);
        $claveAcceso = $fecha;
        $claveAcceso .= $tipo;
        $claveAcceso .= $emisor;
        $claveAcceso .= $ambiente;
        // $serie = $establecimiento->getCodigo() . $ptoEmision->getCodigo();
        // $claveAcceso .= $serie;
        $claveAcceso .= $documento;
        $claveAcceso .= $fecha;
        $claveAcceso .= '1'; // 1 = normal ya no existe contingente
        $claveAcceso .= self::modulo11($claveAcceso);
        return $claveAcceso;
    }

    // MODULO 11 , CODIGO CON EL ALGORITMO PARA CREAR LA CLAVE DE ACCESO
    private static function modulo11($claveAcceso)
    {
        $multiplos = [
            2,
            3,
            4,
            5,
            6,
            7
        ];
        $i = 0;
        $cantidad = strlen($claveAcceso);
        $total = 0;
        while ($cantidad > 0) {
            $total += intval(substr($claveAcceso, $cantidad - 1, 1)) * $multiplos[$i];
            $i++;
            $i = $i % 6;
            $cantidad--;
        }
        $modulo11 = 11 - $total % 11;
        if ($modulo11 == 11) {
            $modulo11 = 0;
        } else if ($modulo11 == 10) {
            $modulo11 = 1;
        }
        return strval($modulo11);
    } // modulo 11
} // FABRICANCR
