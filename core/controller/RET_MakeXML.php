<?php
//require_once 'core/modules/index/model/EmpresasData.php';
//require_once 'Esquemas.php';
//require_once 'SendFileXml.php';
class FabricaRet
{

    public static function CreaRetencion($objCabRet,$objDetRet)
    {
        

        $retencion = new \comprobanteRetencion();
       
        $retencion->ambiente = $objCabRet->ambiente;
        $retencion->tipoEmision = $objCabRet->tipoEmision;
        $retencion->razonSocial = $objCabRet->razonSocial;
        $retencion->nombreComercial = $objCabRet->nombreComercial;
        $retencion->ruc = $objCabRet->ruc;
        $retencion->claveAcceso ='';
        $retencion->codDoc= $objCabRet->codDoc;
        $retencion->estab= $objCabRet->estab;
        $retencion->ptoEmi= $objCabRet->ptoEmi;
        $retencion->secuencial= $objCabRet->secuencial;
        $retencion->dirMatriz= $objCabRet->dirMatriz;
        
        
        $retencion->dirEstablecimiento = $objCabRet->dirEstablecimiento;
        $retencion->contribuyenteEspecial = $objCabRet->contribuyenteEspecial;
        $retencion->obligadoContabilidad = $objCabRet->obligadoContabilidad;
        $retencion->tipoIdentificacionSujetoRetenido = $objCabRet->tipoIdentificacionSujetoRetenido;
        $retencion->razonSocialSujetoRetenido = $objCabRet->razonSocialSujetoRetenido;
        $retencion->identificacionSujetoRetenido = $objCabRet->identificacionSujetoRetenido;
        $retencion->fechaEmision = $objCabRet->fechaEmision;
        
        // / AQUI VA UN BUCLE POR CADA IMPUESTO ENCONTRADO EN EL DETALLE
         $impuestoArray[] = array();         
        
         //Recorro todos los elementos
         foreach($objDetRet as $d){
             
             $impuesto = new \impuestoComprobanteRetencion();
             $impuesto->codigo = $d->codigo ;
             $impuesto->codigoRetencion = $d->codigoRetencion ;
             $impuesto->baseImponible = $d->baseImponible ;
             $impuesto->porcentajeRetener = $d->porcentajeRetener ;
             $impuesto->valorRetenido = $d->valorRetenido ;
             $impuesto->codDocSustento = $objCabRet->codDocSustento;
             $impuesto->numDocSustento = $objCabRet->numDocSustento;
             $impuesto->fechaEmisionDocSustento = $objCabRet->fechaEmisionDocSustento;
             $impuestoArray[] = $impuesto ;
            
         }
        // HASTA AQUI VA EL BLUCLE
         $retencion->impuestos = $impuestoArray;
         
         $retencion->periodoFiscal = $objCabRet->periodoFiscal;
        // $retencion->impuestos = $impuestoArray;

        // AQUI VAN LOS CAMPOS ADICIONALES; PUEDEN SER VARIOS
        $camposAdicionales[] = array();

        $campoAdicional = new \campoAdicional();
        $campoAdicional->nombre = 'IdTransac';
        $campoAdicional->valor = $objCabRet->puntero;
        $camposAdicionales[] = $campoAdicional;

        $empresa = EmpresasData::getEmpresaData();

        if($empresa->agent_ret == 1){
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Agente de Retención:';
            $campoAdicional->valor = "No. Resolución 1";
            $camposAdicionales[] = $campoAdicional;
        }

        if($empresa->regimen_rimpe == 1){
            $campoAdicional = new \campoAdicional();
            $campoAdicional->nombre = 'Regimen RIMPE:';
            $campoAdicional->valor = "No. Resolución 1";
            $camposAdicionales[] = $campoAdicional;
        }

        if (count($camposAdicionales) > 0) {
            $retencion->infoAdicional = $camposAdicionales;
        }
        return $retencion;
    }

    public static function getName($objCabRet){
        $name = 'Ret'.$objCabRet->ruc.$objCabRet->estab.$objCabRet->ptoEmi.$objCabRet->secuencial;
        return $name;
    }

    public static function GetXML($Objret)
    {

        // // CONSTUYE EL XML en base a los datos del objeto retenci�n
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndentString("\t");
        $xml->startDocument('1.0', 'utf-8');

        $xml->startElement("comprobanteRetencion"); // elemento
        $xml->writeAttribute('id', 'comprobante');
        $xml->writeAttribute('version', '1.0.0');

        $xml->startElement("infoTributaria"); // elemento infoTributaria
        $xml->writeElement("ambiente", $Objret->ambiente);
        $xml->writeElement("tipoEmision", $Objret->tipoEmision);
        $xml->writeElement("razonSocial", $Objret->razonSocial);
        if ($Objret->nombreComercial==""){
            $xml->writeElement("nombreComercial",$Objret->razonSocial);
        }else{
            $xml->writeElement("nombreComercial", $Objret->nombreComercial);
        }
        $xml->writeElement("ruc", $Objret->ruc );
        $fechaEmision =  $Objret->fechaEmision;
        $docum = $Objret->estab;
        $docum .=  $Objret->ptoEmi;
        $docum .=  str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT);
        $xml->writeElement("claveAcceso", self::claveAcceso($Objret->codDoc, $Objret->ruc, $Objret->ambiente, $docum, $fechaEmision));
        $xml->writeElement("codDoc", $Objret->codDoc);
        $xml->writeElement("estab", $Objret->estab);
        $xml->writeElement("ptoEmi", $Objret->ptoEmi);
        $xml->writeElement("secuencial", str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT));
        $xml->writeElement("dirMatriz", $Objret->dirMatriz);
        $xml->endElement(); // fin elemento infoTributaria
        //////////////////////////////////////////////////
        $xml->startElement("infoCompRetencion"); // elemento infoTributaria
        $xml->writeElement("fechaEmision", $Objret->fechaEmision);
        $xml->writeElement("dirEstablecimiento", $Objret->dirEstablecimiento);
        $xml->writeElement("contribuyenteEspecial", $Objret->contribuyenteEspecial);
        $xml->writeElement("obligadoContabilidad", $Objret->obligadoContabilidad);
        $xml->writeElement("tipoIdentificacionSujetoRetenido", $Objret->tipoIdentificacionSujetoRetenido);
        $xml->writeElement("razonSocialSujetoRetenido", $Objret->razonSocialSujetoRetenido);
        $xml->writeElement("identificacionSujetoRetenido", $Objret->identificacionSujetoRetenido);
        $xml->writeElement("periodoFiscal", $Objret->periodoFiscal);        
        $xml->endElement(); // fin elemento infoCompRetencion
        /////////////////////////////////////////////////////
        $xml->startElement("impuestos"); // elemento impuestos        
        foreach($Objret->impuestos as $impuestoret )        {
            if (!empty($impuestoret->codigo))
            {
                $xml->startElement("impuesto"); // elemento impuestos
                $xml->writeElement("codigo", $impuestoret->codigo );
                $xml->writeElement("codigoRetencion", $impuestoret->codigoRetencion );
                $xml->writeElement("baseImponible", $impuestoret->baseImponible );
                $xml->writeElement("porcentajeRetener", $impuestoret->porcentajeRetener );
                $xml->writeElement("valorRetenido", $impuestoret->valorRetenido );
                $xml->writeElement("codDocSustento", $impuestoret->codDocSustento );
                $xml->writeElement("numDocSustento", $impuestoret->numDocSustento );
                $xml->writeElement("fechaEmisionDocSustento", $impuestoret->fechaEmisionDocSustento );
                $xml->endElement(); // fin elemento impuesto
                
            }           
        }
        $xml->endElement(); // fin elemento impuestos
        ///////////////////////////////////////////////////
        $xml->startElement("infoAdicional"); // elemento impuestos
        foreach($Objret->infoAdicional as $infoAdicional ){
            if (!empty($infoAdicional->nombre))
            {
                $xml->startElement("campoAdicional");
                $xml->writeAttribute("nombre",$infoAdicional->nombre );
                $xml->text( $infoAdicional->valor);                
                $xml->endElement();
            }
        }
        
        $xml->endElement(); // fin elemento infoAdicional
        $xml->endElement(); // Fin retencion
        $xml->endDocument();
        $pathXml = EmpresasData::getByRuc($_SESSION['ruc'])->path_xml;

        $xmls = $xml->outputMemory(TRUE);
        $xmlsimple = simplexml_load_string($xmls);
//        $xml_file = $xmlsimple->asXML($pathXml.'/sinfirma/'.'Ret'.$docum.'.xml');
        if (!file_exists("xml/" . $_SESSION['ruc'] . "")) {
            mkdir("xml/" . $_SESSION['ruc'] . "/sinfirma", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/pdf", 0777, true);
            mkdir("xml/" . $_SESSION['ruc'] . "/autorizados", 0777, true);
        }
        if (file_exists('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml')) {
            unlink('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml');
        }
        $xml_file = $xmlsimple->asXML('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml');

        return $xml_file;
    }

    public static function GetXMLImp($Objret)
    {

        // // CONSTUYE EL XML en base a los datos del objeto retenci�n
        $xml = new XMLWriter();
        $xml->openMemory();
        $xml->setIndentString("\t");
        $xml->startDocument('1.0', 'utf-8');

        $xml->startElement("comprobanteRetencion"); // elemento
        $xml->writeAttribute('id', 'comprobante');
        $xml->writeAttribute('version', '1.0.0');

        $xml->startElement("infoTributaria"); // elemento infoTributaria
        $xml->writeElement("ambiente", $Objret->ambiente);
        $xml->writeElement("tipoEmision", $Objret->tipoEmision);
        $xml->writeElement("razonSocial", $Objret->razonSocial);
        if ($Objret->nombreComercial==""){
            $xml->writeElement("nombreComercial",$Objret->razonSocial);
        }else{
            $xml->writeElement("nombreComercial", $Objret->nombreComercial);
        }
        $xml->writeElement("ruc", $Objret->ruc );
        $fechaEmision =  $Objret->fechaEmision;
        $docum = $Objret->estab;
        $docum .=  $Objret->ptoEmi;
        $docum .=  str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT);
        $xml->writeElement("claveAcceso", self::claveAcceso($Objret->codDoc, $Objret->ruc, $Objret->ambiente, $docum, $fechaEmision));
        $xml->writeElement("codDoc", $Objret->codDoc);
        $xml->writeElement("estab", $Objret->estab);
        $xml->writeElement("ptoEmi", $Objret->ptoEmi);
        $xml->writeElement("secuencial", str_pad($Objret->secuencial, 9, 0, STR_PAD_LEFT));
        $xml->writeElement("dirMatriz", $Objret->dirMatriz);
        $xml->endElement(); // fin elemento infoTributaria
        //////////////////////////////////////////////////
        $xml->startElement("infoCompRetencion"); // elemento infoTributaria
        $xml->writeElement("fechaEmision", $Objret->fechaEmision);
        $xml->writeElement("dirEstablecimiento", $Objret->dirEstablecimiento);
        $xml->writeElement("contribuyenteEspecial", $Objret->contribuyenteEspecial);
        $xml->writeElement("obligadoContabilidad", $Objret->obligadoContabilidad);
        $xml->writeElement("tipoIdentificacionSujetoRetenido", $Objret->tipoIdentificacionSujetoRetenido);
        $xml->writeElement("razonSocialSujetoRetenido", $Objret->razonSocialSujetoRetenido);
        $xml->writeElement("identificacionSujetoRetenido", $Objret->identificacionSujetoRetenido);
        $xml->writeElement("periodoFiscal", $Objret->periodoFiscal);
        $xml->endElement(); // fin elemento infoCompRetencion
        /////////////////////////////////////////////////////
        $xml->startElement("impuestos"); // elemento impuestos
        foreach($Objret->impuestos as $impuestoret )        {
            if (!empty($impuestoret->codigo))
            {
                $xml->startElement("impuesto"); // elemento impuestos
                $xml->writeElement("codigo", $impuestoret->codigo );
                $xml->writeElement("codigoRetencion", $impuestoret->codigoRetencion );
                $xml->writeElement("baseImponible", $impuestoret->baseImponible );
                $xml->writeElement("porcentajeRetener", $impuestoret->porcentajeRetener );
                $xml->writeElement("valorRetenido", $impuestoret->valorRetenido );
                $xml->writeElement("codDocSustento", $impuestoret->codDocSustento );
                $xml->writeElement("numDocSustento", $impuestoret->numDocSustento );
                $xml->writeElement("fechaEmisionDocSustento", $impuestoret->fechaEmisionDocSustento );
                $xml->endElement(); // fin elemento impuesto

            }
        }
        $xml->endElement(); // fin elemento impuestos
        ///////////////////////////////////////////////////
        $xml->startElement("infoAdicional"); // elemento impuestos
        foreach($Objret->infoAdicional as $infoAdicional ){
            if (!empty($infoAdicional->nombre))
            {
                $xml->startElement("campoAdicional");
                $xml->writeAttribute("nombre",$infoAdicional->nombre );
                $xml->text( $infoAdicional->valor);
                $xml->endElement();
            }
        }

        $xml->endElement(); // fin elemento infoAdicional
        $xml->endElement(); // Fin retencion
        $xml->endDocument();
        $pathXml = EmpresasData::getByRuc($_SESSION['ruc'])->path_xml;

        $xmls = $xml->outputMemory(TRUE);
        $xmlsimple = simplexml_load_string($xmls);
//        $xml_file = $xmlsimple->asXML($pathXml.'/sinfirma/'.'Ret'.$docum.'.xml');
//        if (!file_exists("xml/" . $_SESSION['ruc'] . "")) {
//            mkdir("xml/" . $_SESSION['ruc'] . "/sinfirma", 0777, true);
//            mkdir("xml/" . $_SESSION['ruc'] . "/pdf", 0777, true);
//            mkdir("xml/" . $_SESSION['ruc'] . "/autorizados", 0777, true);
//        }
//        if (file_exists('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml')) {
//            unlink('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml');
//        }
//        $xml_file = $xmlsimple->asXML('xml/'.$_SESSION['ruc'].'/sinfirma/' . 'Ret' . $docum . '.xml');

        return $xmlsimple;
    }

    // GetXML
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

    // clave acceso
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
            $i ++;
            $i = $i % 6;
            $cantidad --;
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