<?php

class FabricaFact
{
    public static function CreaFactura($objCabRet, $objDetRet, $objDifRet, $objDetPagos)
    {
        $factura = new \factura();
        $factura->ambiente = $objCabRet->ambiente;
        $factura->tipoEmision = $objCabRet->tipoEmision;
        $factura->razonSocial = self::replaceDato($objCabRet->razonSocial);
        $factura->nombreComercial = self::replaceDato($objCabRet->nombreComercial);
        $factura->ruc = $objCabRet->ruc;
        $factura->claveAcceso = $objCabRet->fi_claveacceso;
        $factura->codDoc = $objCabRet->codDoc;
        $factura->estab = $objCabRet->estab;
        $factura->ptoEmi = $objCabRet->ptoEmi;
        $factura->secuencial = $objCabRet->secuencial;
        $factura->dirMatriz = self::replaceDato($objCabRet->dirMatriz);
        $empresa = EmpresasData::getById(1);

        if ($empresa->agen_ret == 1) {
            $factura->agenteRetencion = $empresa->agen_ret;
        }

        if (!empty($empresa->contribuyenteEspecial) && $empresa->contribuyenteEspecial != NULL) {
            $factura->contribuyenteEspecial = $empresa->contribuyenteEspecial;
        }

        $factura->fechaEmision = $objCabRet->fechaEmision;
        $factura->dirEstablecimiento = self::replaceDato($objCabRet->dirEstablecimiento);
        $factura->obligadoContabilidad = $objCabRet->obligadoContabilidad;

//        $factura->contribuyenteEspecial = $objCabRet->contribuyenteEspecial;
        $factura->tipoIdentificacionComprador = $objCabRet->tipoIdentificacionComprador;
        $factura->razonSocialComprador = (!empty($objCabRet->razonSocialComprador)) ? self::replaceDato($objCabRet->razonSocialComprador) : self::replaceDato($objCabRet->nameComprador);
        $factura->identificacionComprador = $objCabRet->identificacionComprador;
        if (empty($objCabRet->direccionComprador)) {
            $direccion = '.';
        } else {
            $direccion = self::replaceDato($objCabRet->direccionComprador);
        }
        $factura->direccionComprador = $direccion;
        $factura->moneda = $objCabRet->moneda;
        $factura->propina = $objCabRet->propina;
        $factura->importeTotal = self::getFormatNumber($objCabRet->importeTotal);
        $factura->totalSinImpuestos = self::getFormatNumber($objCabRet->totalSinImpuestos); // traer en consulta Sql
        $factura->totalDescuento = self::getFormatNumber($objCabRet->totalDescuento); // traer en consulta Sql
        foreach ($objDetPagos as $objPago) {
            $detPagos = new \pago(); /* Formas de Pago */
            $detPagos->formaPago = (is_null($objPago->formaPago)) ? "20" : $objPago->formaPago;
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
            $detTotalConImpuestos->baseImponible = self::getFormatNumber($baseImponible);
            $detTotalConImpuestos->valor = self::getFormatNumber($valor);
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
            $detTotalConImpuestos->baseImponible = self::getFormatNumber($baseImponible);
            $detTotalConImpuestos->valor = self::getFormatNumber($valor);
            $arrayTotalConImpuestos[] = $detTotalConImpuestos;
        }
        $factura->totalConImpuestos = $arrayTotalConImpuestos;
        //Recorro todos los elementos
        $detalleArray = array();
        $name = '';
        $codigoPro = 0;
        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DETALLE
        foreach ($objDetRet as $d) {

            $name = $d->descripcion;
            $codigoPro = $d->codigoPrincipal;
            $detalleFactura = new \detalleFactura();
            $detalleFactura->cantidad = self::getFormatNumber($d->cantidad);
            $detalleFactura->codigoAuxiliar = self::replaceDato($codigoPro);
            $detalleFactura->codigoPrincipal = self::replaceDato($codigoPro);
            $detalleFactura->descripcion = self::replaceDato($name);
            if ($d->valDesc != 0) {
                $precioUnitario = $d->precioUnitario;
                $descuento = ($d->valDesc * self::getFormatNumber($d->cantidad)) + $d->descuentoGen;
                $precioTotalSinImpuesto = $d->totDesc;
                $baseImponible = $d->totDesc;
            } else {
                $precioUnitario = $d->precioUnitario;
                $descuento = $d->valDesc;
                $precioTotalSinImpuesto = $d->precioTotalSinImpuesto;
                $baseImponible = $d->precioTotalSinImpuesto;
            }
            $detalleFactura->precioUnitario = self::getFormatNumber($precioUnitario);
            $detalleFactura->descuento = self::getFormatNumber($descuento);
            $detalleFactura->precioTotalSinImpuesto = self::getFormatNumber($precioTotalSinImpuesto);
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
            $detaImpuesto->baseImponible = self::getFormatNumber($baseImponible);
            $detaImpuesto->valor = self::getFormatNumber($d->valor);
            $impuestDetArray = array($detaImpuesto);
            $detalleFactura->impuesto = $impuestDetArray;
            $detalleArray[] = $detalleFactura;
            $detallesAdicional = [];
            $namComment = self::loadNameComment();
            if (!empty($d->comentario)) {
                $detalleAdicional1 = new \detalleAdicional();
                $detalleAdicional1->nombre = $namComment[0];
                $detalleAdicional1->valor = $d->comentario;
                $detallesAdicional[] = $detalleAdicional1;
                $detalleFactura->detalleAdicional = $detallesAdicional;
            }
            if (!empty($d->comentario1)) {
                $detalleAdicional2 = new \detalleAdicional();
                $detalleAdicional2->nombre = $namComment[1];
                $detalleAdicional2->valor = $d->comentario1;
                $detallesAdicional[] = $detalleAdicional2;
                $detalleFactura->detalleAdicional = $detallesAdicional;
            }
            if (!empty($d->comentario2)) {
                $detalleAdicional3 = new \detalleAdicional();
                $detalleAdicional3->nombre = $namComment[2];
                $detalleAdicional3->valor = $d->comentario2;
                $detallesAdicional[] = $detalleAdicional3;
                $detalleFactura->detalleAdicional = $detallesAdicional;
            }

        }
        // DETALLE DE PRODUCTOS DE LA FACTURA DESDE LA TABLA DE OPERTATION.DIFERIDO
        if (!empty($objDifRet) || $objDifRet != null) {
            foreach ($objDifRet as $d) {
                $name = $d->descripcion;
                $codigoPro = $d->codigoPrincipal;
                $detalleFactura = new \detalleFactura();
                $detalleFactura->cantidad = self::getFormatNumber($d->cantidad);
                $detalleFactura->codigoAuxiliar = self::replaceDato($codigoPro);
                $detalleFactura->codigoPrincipal = self::replaceDato($codigoPro);
                $detalleFactura->descripcion = self::replaceDato($name);
                if ($d->valDesc != 0) {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = ($d->valDesc * self::getFormatNumber($d->cantidad)) + $d->descuentoGen;
                    $precioTotalSinImpuesto = $d->totDesc;
                    $baseImponible = $d->totDesc;
                } else {
                    $precioUnitario = $d->precioUnitario;
                    $descuento = $d->valDesc;
                    $precioTotalSinImpuesto = $d->precioTotalSinImpuesto;
                    $baseImponible = $d->precioTotalSinImpuesto;
                }
                $detalleFactura->precioUnitario = self::getFormatNumber($precioUnitario);
                $detalleFactura->descuento = self::getFormatNumber($descuento);
                $detalleFactura->precioTotalSinImpuesto = self::getFormatNumber($precioTotalSinImpuesto);
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
                $detaImpuesto->baseImponible = self::getFormatNumber($baseImponible);
                $detaImpuesto->valor = self::getFormatNumber($d->valor);
                $impuestDetArray = array($detaImpuesto);
                $detalleFactura->impuesto = $impuestDetArray;
                $detalleArray[] = $detalleFactura;
                $detallesAdicional = [];
                $namComment = self::loadNameComment();
                if (!empty($d->comentario)) {
                    $detalleAdicional1 = new \detalleAdicional();
                    $detalleAdicional1->nombre = $namComment[0];
                    $detalleAdicional1->valor = $d->comentario;
                    $detallesAdicional[] = $detalleAdicional1;
                    $detalleFactura->detalleAdicional = $detallesAdicional;
                }
                if (!empty($d->comentario1)) {
                    $detalleAdicional2 = new \detalleAdicional();
                    $detalleAdicional2->nombre = $namComment[1];
                    $detalleAdicional2->valor = $d->comentario1;
                    $detallesAdicional[] = $detalleAdicional2;
                    $detalleFactura->detalleAdicional = $detallesAdicional;
                }
                if (!empty($d->comentario2)) {
                    $detalleAdicional3 = new \detalleAdicional();
                    $detalleAdicional3->nombre = $namComment[2];
                    $detalleAdicional3->valor = $d->comentario2;
                    $detallesAdicional[] = $detalleAdicional3;
                    $detalleFactura->detalleAdicional = $detallesAdicional;
                }

            }
        }
        // SE INSERTA EL ARRAY CON EL DETALLE DE PRODUCTOS DE LA FACTURA
        $factura->detalles = $detalleArray;
        $camposAdicionales[] = array();
        /*$direccion = "";
        if (!empty($objCabRet->direccionComprador)) {
          $direccion = $objCabRet->direccionComprador;
        } else {
          $direccion = ".";
        }*/
        /*$campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'direccionComprador';
        $campoAdicional->valor = $direccion;
        $camposAdicionales[] = $campoAdicional;*/
        $emailers = '';
        if (!empty($objCabRet->email1) && $objCabRet->email1 != NULL) {
            $emailers .= $objCabRet->email1 . ",";
        } else {
            $emailers .= ".";
        }
        if (!empty($objCabRet->email2) && $objCabRet->email2 != NULL) {
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
        if ($empresa->micro_emp == 1) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Contribuyente:';
            $campoAdicional->valor = self::replaceDato("CONTRIBUYENTE RÉGIMEN MICROEMPRESAS");
            $camposAdicionales[] = $campoAdicional;
        }
        if ($empresa->regimen_rimpe == 1) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Contribuyente:';
            $campoAdicional->valor = self::replaceDato("CONTRIBUYENTE RÉGIMEN RIMPE");
            $camposAdicionales[] = $campoAdicional;
        }
        if (!empty($objCabRet->comentariofac)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'comentario';
            $campoAdicional->valor = $objCabRet->comentariofac;
            $camposAdicionales[] = $campoAdicional;
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

    public static function loadNameComment()
    {
        $comentario = "'com_xml_1','com_xml_2','com_xml_3'";
        $ids = ConfigurationData::getByComentariosProductos($comentario);
        foreach ($ids as $id) {
            $idd[] = $id->cgdatov;
        }
        return $idd;
    }

    public static function replaceDato($string)
    {
        $replaces = ReplacesData::getAll();
        $nString = $string;
        foreach ($replaces as $replace) {
            $nString = str_replace($replace->cf_search, $replace->cf_reempl, $nString);
        }
        return $nString;
    }

    public static function getName($objCabRet)
    {
        $name = 'Fact' . $objCabRet->ruc . $objCabRet->estab . $objCabRet->ptoEmi . $objCabRet->secuencial;
        return $name;
    }

    public static function getFormatNumber($number)
    {
        return number_format($number, 2, '.', '');
    }

    public static function GetXML($Objret)
    {
        $data = EmpresasData::getById(1);
        // // CONSTUYE EL XML en base a los datos del objeto retenci�n
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndentString("\t");
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement("factura"); // elemento
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
        $xml->writeElement("claveAcceso", $Objret->claveAcceso);
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
        $xml->startElement("infoFactura"); // elemento infoTributaria
        $xml->writeElement("fechaEmision", $Objret->fechaEmision);
        $xml->writeElement("dirEstablecimiento", $Objret->dirEstablecimiento);
        if (!empty($Objret->contribuyenteEspecial)) {
            $xml->writeElement("contribuyenteEspecial", $Objret->contribuyenteEspecial);
        }
        $xml->writeElement("obligadoContabilidad", $Objret->obligadoContabilidad);
        $xml->writeElement("tipoIdentificacionComprador", $Objret->tipoIdentificacionComprador);
        $xml->writeElement("razonSocialComprador", $Objret->razonSocialComprador);
        $xml->writeElement("identificacionComprador", $Objret->identificacionComprador);
        $xml->writeElement("direccionComprador", $Objret->direccionComprador);
        $xml->writeElement("totalSinImpuestos", $Objret->totalSinImpuestos);
        $xml->writeElement("totalDescuento", $Objret->totalDescuento);
        $xml->startElement("totalConImpuestos"); // elemento totalConImpuestos
        foreach ((array)$Objret->totalConImpuestos as $totalImpuestos) {
            $xml->startElement("totalImpuesto"); // elemento totalConImpuesto
            $xml->writeElement("codigo", $totalImpuestos->codigo);
            $xml->writeElement("codigoPorcentaje", $totalImpuestos->codigoPorcentaje);
            $xml->writeElement("baseImponible", $totalImpuestos->baseImponible);
            $xml->writeElement("tarifa", $totalImpuestos->tarifa);
            $xml->writeElement("valor", $totalImpuestos->valor);
            $xml->endElement(); // fin elemento totalImpuesto
        }
        $xml->endElement(); // elemento totalConImpuestos
        $xml->writeElement("propina", $Objret->propina);
        $xml->writeElement("importeTotal", $Objret->importeTotal);
        $xml->writeElement("moneda", $Objret->moneda);
        $xml->startElement("pagos"); // elemento Pagos
        foreach ((array)$Objret->pagos as $pagos) {
            $xml->startElement("pago"); // elemento Pago
            $xml->writeElement("formaPago", $pagos->formaPago);
            $xml->writeElement("total", $pagos->total);
            $xml->writeElement("plazo", $pagos->plazo);
            $xml->writeElement("unidadTiempo", $pagos->unidadTiempo);
            $xml->endElement(); // fin elemento pago
        }
        $xml->endElement(); // fin elemento Pagos
        $xml->endElement(); // fin elemento infoTributaria
        $xml->startElement("detalles"); // elemento detalles
        foreach ($Objret->detalles as $detalleFact) {
            $xml->startElement("detalle"); // elemento detalle
            $xml->writeElement("codigoPrincipal", $detalleFact->codigoPrincipal);
            $xml->writeElement("codigoAuxiliar", $detalleFact->codigoAuxiliar);
            $xml->writeElement("descripcion", $detalleFact->descripcion);
            $xml->writeElement("cantidad", $detalleFact->cantidad);
            $xml->writeElement("precioUnitario", $detalleFact->precioUnitario);
            $xml->writeElement("descuento", $detalleFact->descuento);
            $xml->writeElement("precioTotalSinImpuesto", $detalleFact->precioTotalSinImpuesto);
            if (!empty($detalleFact->detalleAdicional)) {
                $xml->startElement("detallesAdicionales"); // elemento detalleAdicional
                foreach ($detalleFact->detalleAdicional as $detalleAdi) {
                    $xml->startElement("detAdicional");
                    $xml->writeAttribute("nombre", $detalleAdi->nombre);
                    $xml->writeAttribute("valor", $detalleAdi->valor);
                    $xml->endElement();
                }
                $xml->endElement(); // fin elemento detalleAdicional
            }
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
        $xmls = $xml->outputMemory(TRUE);
        $xmlsimple = simplexml_load_string($xmls);
        if (!file_exists("xml/" . $_SESSION['ruc'] . "")) {
            mkdir("xml/" . $_SESSION['ruc'] . "/sinfirma", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/pdf", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/autorizados", 0777, true);
        }
        if (file_exists('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Fact' . $docum . '.xml')) {
            unlink('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Fact' . $docum . '.xml');
        }
        $xml_file = $xmlsimple->asXML('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Fact' . $docum . '.xml');
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
} // FABRICARET
