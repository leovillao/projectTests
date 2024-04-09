<?php

class FData
{
    public static function fuentesPdf()
    {
        $array = array("titulo" => 8, "cabecera" => 8, "detalle" => 7);
        return $array;
    }

    /** FUNCIONES PARA VALIDAR STOCK*/
    public static function getSaldoForProduct($bodega, $product, $fecha)
    {
        $producto = ProductData::getByItcodigo($product);
        $stock = SaldosbodData::getSaldoProductoBodega($bodega, $producto->itid, $fecha);
        $r = 0;
        if ($stock->stock > 0) {
            $r = $stock;
        }
        return $r;
    }

    public static function validaFechaHistorico($fechaDoc, $d)
    {
        $mesH = $_SESSION['himes'];
        $anioH = $_SESSION['hianio'];
        switch ($d) {
            case "D": // detalle de la tabla detalle
                $htable = "hi_operacion_detalle";
                $table = "in_operacion21_detalle";
                break;
            case "F": // diferido de la tabla detalle
                $htable = "hi_operacion_diferido";
                $table = "in_operacion21_diferido";
                break;
            case "C": // cabecera de la tabla operacion
                $htable = "hi_operacion";
                $table = "in_operacion21";
                break;
        }
        $tablaH = $table;
        $fecha = $fechaDoc;
        $fechaComoEntero = strtotime($fecha);
        $mes = date("m", $fechaComoEntero);
        $anio = date("Y", $fechaComoEntero);
        if (!is_null($mesH) && !is_null($anioH)) {
            if ($mes <= $mesH && $anio <= $anioH) {
                $tablaH = $htable;
            }
        }
        return $tablaH;
    }

    public static function getSaldoAllBodegas($product, $fecha)
    {
        $producto = ProductData::getByItcodigo($product);
        $stock = SaldosbodData::getSaldoProductos($producto->itid, $fecha);
        if (empty($stock)) {
            $r = '';
        } else {
            $r = $stock;
        }
        return $r;
    }

    public static function getSaldoAllBodegas1($product, $fecha)
    {
        $producto = ProductData::getByItcodigo($product);
        $stock = SaldosbodData::getSaldoProductos($producto->itid, $fecha);
        if (empty($stock)) {
            $r = '';
        } else {
            $r = $stock;
        }
        return $r;
    }

    public static function getSaldoAllBodegasDiario($product, $fecha)
    {
        $producto = ProductData::getByItcodigo($product);
        $stock = SaldodiarioData::getProductSalDiarioForEgreso($producto->itid, $fecha);
        if (empty($stock)) {
            $r = 0.00;
        } else {
            $r = $stock->saldo;
        }
        return $r;
    }

    public static function getSaldoFuturo($bodega, $product, $fecha, $cantidad)
    {
        $producto = ProductData::getByItcodigo($product);
        $saldo = SaldosbodData::getSaldoFuturo($bodega, $producto->itid, $fecha, $cantidad);
        return $saldo;
    }

    public static function formatoNumero($numero)
    {
        $decimal = ConfigurationData::getByShortName('conf_num_decimal_inv')->cgdatoi;
        return number_format($numero, $decimal, ',', '');
    }

    public static function formatoNumeroReportsInventario($numero)
    {
        $decimal = ConfigurationData::getByShortName('num_decimales_inv')->cgdatoi;;
        return number_format($numero, $decimal, '.', ',');
    }

    public static function formatoNumeroReportes($numero)
    {
        return number_format($numero, 2, '.', ',');
    }

    public static function formatoNumeroBD($numero)
    {
        $decimal = ConfigurationData::getByShortName('conf_num_decimal_inv')->cgdatoi;
        return number_format($numero, $decimal, '.', '');
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
        $multiplos = [2, 3, 4, 5, 6, 7];
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
    }

    function calDiasVencidos($saldo, $fecha)
    {
        $dias = 0;
        if ($saldo > 0) {
            $date2 = date_create(date('Y-m-d'));
            $date1 = date_create($fecha);
            $diff = date_diff($date1, $date2);
            $dias = substr($diff->format("%R%a"), 1);
        }
        return $dias;
    }

    function getArray($array, $desde, $hasta)
    {
//        $foundItems = array();
        foreach ($array as $item) {
            if ((self::calDiasVencidos($item->desaldo, $item->defecha) >= $desde) && (self::calDiasVencidos($item->desaldo, $item->defecha) <= $hasta)) {
                $foundItems[] = $item;
            }
        }
        return $foundItems;
    }

    static function getNameReporte($idPunto)
    {
        $idReporte = SecuenciaData::getById($idPunto); // se obtiene el id del reporte
        return FormatosgenData::getById($idReporte->idformato)->fopage; // nombre del reporte para la impresion de la factura
    }

    static function formatoMinusculas($string)
    {
        return ucwords(strtolower($string));
    }

    static function getUltimoDiaMesCorriente()
    {
        $dateH = EmpresasData::getAllEmp();
        //a;os no bisiestos
        $arrayNb = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        // a;os bisiestos
        $arraB = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $newFecha = "";
        if (self::getTipoAnio() == 1) { // 1 es bisiesto , o no es bisiesto
            $newFecha = "'" . $dateH->hianio . "-" . $dateH->himes . "-" . $arraB[intval($dateH->himes)] . "'";
        } else {
            $newFecha = $dateH->hianio . "-" . $dateH->himes . "-" . $arrayNb[intval($dateH->himes)];
        }
        return $newFecha;
    }

    static function getTipoAnio()
    {
        $int = 1; // uno es a;o bisiesto
        if (is_float(date("Y") / 4)) {
            $int = 0; // no es a;io bisiesto
        }
        return $int;
    }

}
