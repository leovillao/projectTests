<?php

class FabricaGuia
{
    public static function CreaGuia($guiaCab, $objDets)
    {
        $guia = new \guiaRemision();
        $guia->ambiente = $guiaCab->ambiente;
        $guia->tipoEmision = $guiaCab->tipoEmision;
        $guia->razonSocial = self::replaceDato($guiaCab->razonSocial);
        $guia->nombreComercial = self::replaceDato($guiaCab->nombreComercial);
        $guia->ruc = $guiaCab->ruc;
        $guia->claveAcceso = $guiaCab->claveAcceso;
        $guia->codDoc = $guiaCab->codDoc;
        $guia->estab = $guiaCab->estab;
        $guia->ptoEmi = $guiaCab->ptoEmision;
        $guia->secuencial = $guiaCab->secuencia;
        $guia->dirMatriz = self::replaceDato($guiaCab->dirMatriz);
        $empresa = EmpresasData::getById(1);
//        $guia->destinatarios = $guiacab->;
        $guia->dirPartida = $guiaCab->dirPartida;
        $guia->fechaFinTransporte = $guiaCab->fechaFinTransporte;
        $guia->fechaIniTransporte = $guiaCab->fechaIniTransporte;
//$guia->infoAdicional = $guiaCab->;
        $guia->placa = $guiaCab->placa;
        $guia->razonSocialTransportista = $guiaCab->razonSocialTransportista;
//$guia->rise = $guiaCab->;

        if (!empty($empresa->regimenRise) || $empresa->regimenRise != NULL) {
            $guia->rise = "Contribuyente Regimen Simplificado RISE";
        }

        $guia->rucTransportista = $guiaCab->rucTransportista;
        $guia->tipoIdentificacionTransportista = $guiaCab->tipoIdentificacionTransportista;
        $guia->secuencial = $guiaCab->secuencia;
//$guia->fechaEmision = $guiaCab->;
        $guia->dirEstablecimiento = $guiaCab->dirEstablecimiento;
        $guia->obligadoContabilidad = $guiaCab->obligadoContabilidad;

        if (!empty($empresa->contribuyenteEspecial) && $empresa->contribuyenteEspecial != NULL) {
            $guia->contribuyenteEspecial = $empresa->contribuyenteEspecial;
        }

//        $destinatario = new \destinatarios();
        $destinatar = new \destinatario();

        $destinatar->identificacionDestinatario = $guiaCab->identificacionDestinatario;
        $destinatar->razonSocialDestinatario = $guiaCab->razonSocialDestinatario;
        $destinatar->dirDestinatario = $guiaCab->dirDestinatario;
        $destinatar->motivoTraslado = $guiaCab->motivoTraslado;
        $destinatar->codEstabDestino = $guiaCab->codEstabDestino;
        $destinatar->ruta = $guiaCab->ruta;
        $destinatar->codDocSustento = $guiaCab->codDocSustento;

        if ($guiaCab->numDocSustento) {
            $factura = substr($guiaCab->numDocSustento, 0, 3) . "-" . substr($guiaCab->numDocSustento, 3, 3) . "-" . substr($guiaCab->numDocSustento, 6);
            $destinatar->numDocSustento = $factura;
        }

        $destinatar->numAutDocSustento = $guiaCab->numAutDocSustento;
        $destinatar->fechaEmisionDocSustento = $guiaCab->fechaEmisionDocSustento;

        foreach ($objDets as $objDet) {
            $detProducto = new \detalleGuiaRemision(); /* Formas de Pago */
            $detProducto->codigoInterno = $objDet->codigo;
            $detProducto->codigoAdicional = $objDet->codigo;
            $detProducto->descripcion = $objDet->descripcion;
            $detProducto->cantidad = $objDet->cantidad;
            $arrayProductos[] = $detProducto;
        }
        $destinatar->detalles = $arrayProductos;

        $detalleArray[] = $destinatar;

        $guia->destinatarios = $detalleArray;

        if (!empty($guiaCab->direccion1)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Direccion';
            $campoAdicional->valor = $guiaCab->direccion1;
            $camposAdicionales[] = $campoAdicional;
        }

        if (!empty($guiaCab->direccion2)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Direccion';
            $campoAdicional->valor = $guiaCab->direccion2;
            $camposAdicionales[] = $campoAdicional;
        }

        if (!empty($guiaCab->email1)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'email';
            $campoAdicional->valor = $guiaCab->email1;
            $camposAdicionales[] = $campoAdicional;
        }

        if (!empty($guiaCab->email2)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'email';
            $campoAdicional->valor = $guiaCab->email2;
            $camposAdicionales[] = $campoAdicional;
        }

        if (!empty($guiaCab->email3)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'email';
            $campoAdicional->valor = $guiaCab->email3;
            $camposAdicionales[] = $campoAdicional;
        }

        if (!empty($guiaCab->telefono)) {
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'telefono';
            $campoAdicional->valor = $guiaCab->telefono;
            $camposAdicionales[] = $campoAdicional;
        }


        if (count($camposAdicionales) > 0) {
            $guia->infoAdicional = $camposAdicionales;
        }

//        $guia->infoAdicional = $arrayAdicional;

        return $guia;
    }

    public static function GetXML($Objret)
    {
        $data = EmpresasData::getById(1);
        // // CONSTUYE EL XML en base a los datos del objeto retenci�n
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndentString("\t");
        $xml->startDocument('1.0', 'utf-8');
        $xml->startElement("guiaRemision"); // elemento
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
        $xml->writeElement("secuencial", $Objret->secuencial);
        $xml->writeElement("dirMatriz", $Objret->dirMatriz);
        if (isset($Objret->regimenMicroempresas)) {
            $xml->writeElement("regimenMicroempresas", $Objret->regimenMicroempresas);
        }
        if (isset($Objret->agenteRetencion)) {
            $xml->writeElement("agenteRetencion", $Objret->agenteRetencion);
        }
        $xml->endElement(); // fin elemento infoTributaria

        $xml->startElement("infoGuiaRemision"); // elemento infoGuiaRemision
        $xml->writeElement("dirEstablecimiento", $Objret->dirMatriz);
        $xml->writeElement("dirPartida", $Objret->dirPartida);
        $xml->writeElement("razonSocialTransportista", $Objret->razonSocialTransportista);
        $xml->writeElement("tipoIdentificacionTransportista", $Objret->tipoIdentificacionTransportista);
        if ($Objret->rise) {
            $xml->writeElement("rise", $Objret->rise);
        }
        $xml->writeElement("rucTransportista", $Objret->rucTransportista);
        $xml->writeElement("obligadoContabilidad", $Objret->obligadoContabilidad);
        if ($Objret->contribuyenteEspecial) {
            $xml->writeElement("contribuyenteEspecial", $Objret->contribuyenteEspecial);
        }
        $xml->writeElement("fechaIniTransporte", $Objret->fechaIniTransporte);
        $xml->writeElement("fechaFinTransporte", $Objret->fechaFinTransporte);
        $xml->writeElement("placa", $Objret->placa);
        $xml->endElement(); // fin elemento infoGuiaRemision

        $xml->startElement("destinatarios"); // elemento destinatarios
//        $xml->startElement("destinatarios"); // elemento totalConImpuestos
        $t = 0;
        foreach ($Objret->destinatarios as $totalDestinatario) {
            $xml->startElement("destinatario"); // elemento destinatario
//            $xml->writeElement("codDocSustento", $t++);
            $xml->writeElement("identificacionDestinatario", $totalDestinatario->identificacionDestinatario);
            $xml->writeElement("razonSocialDestinatario", $totalDestinatario->razonSocialDestinatario);
            $xml->writeElement("dirDestinatario", $totalDestinatario->dirDestinatario);
            $xml->writeElement("motivoTraslado", $totalDestinatario->motivoTraslado);
            if ($totalDestinatario->docAduaneroUnico) {
                $xml->writeElement("docAduaneroUnico", $totalDestinatario->docAduaneroUnico);
            }
            $xml->writeElement("codEstabDestino", $totalDestinatario->codEstabDestino);
            $xml->writeElement("ruta", $totalDestinatario->ruta);
            if ($totalDestinatario->codDocSustento) {
                $xml->writeElement("codDocSustento", $totalDestinatario->codDocSustento);
            }
            if (strlen($totalDestinatario->numDocSustento) > 0) {
                $xml->writeElement("numDocSustento", $totalDestinatario->numDocSustento);
            }
            if ($totalDestinatario->numAutDocSustento) {
                $xml->writeElement("numAutDocSustento", $totalDestinatario->numAutDocSustento);
            }
            if ($totalDestinatario->fechaEmisionDocSustento) {
                $xml->writeElement("fechaEmisionDocSustento", $totalDestinatario->fechaEmisionDocSustento);
            }
            $xml->startElement("detalles"); // elemento detalleAdicional
            foreach ($totalDestinatario->detalles as $detalle) {
                $xml->startElement("detalle");
                $xml->writeElement("codigoInterno", $detalle->codigoInterno);
                $xml->writeElement("descripcion", $detalle->descripcion);
                $xml->writeElement("cantidad", $detalle->cantidad);
                $xml->endElement();
            }
            $xml->endElement(); // fin elemento detalleAdicional

            $xml->endElement(); // fin elemento destinatario
        }

        $xml->endElement(); // fin elemento infoGuiaRemision
        $xml->startElement("infoAdicional"); // elemento impuestos
        foreach ($Objret->infoAdicional as $infoAdicional) {
//            if (!empty($infoAdicional->nombre)) {
            $xml->startElement("campoAdicional");
            $xml->writeAttribute("nombre", $infoAdicional->nombre);
            $xml->text($infoAdicional->valor);
            $xml->endElement();
//            }
        }
        $xml->endElement(); // fin elemento infoAdicional
//        $xml->endElement(); // Fin retencion
        $xml->endDocument();
        $xmls = $xml->outputMemory(TRUE);
        $xmlsimple = simplexml_load_string($xmls);
        if (!file_exists("xml/" . $_SESSION['ruc'] . "")) {
            mkdir("xml/" . $_SESSION['ruc'] . "/sinfirma", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/pdf", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/autorizados", 0777, true);
        }
        if (file_exists('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Guia' . $docum . '.xml')) {
            unlink('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Guia' . $docum . '.xml');
        }
        $xml_file = $xmlsimple->asXML('xml/' . $_SESSION['ruc'] . '/sinfirma/' . 'Guia' . $docum . '.xml');
        return $xml_file;
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

}
