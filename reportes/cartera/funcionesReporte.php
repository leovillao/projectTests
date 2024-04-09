<?php

class ReporteCobros
{
    public function __construct()
    {
        $this->fuente = 10;
        $this->fuenteTexto = 9;
        $this->fuenteTitulo = 11;
    }

    public static function getCabecera($desde, $hasta)
    {
        $pagosCab = CobroscabData::getByAllFechas($desde, $hasta);
        foreach ($pagosCab as $pagoCab) {
            $ar_idsCab[] = $pagoCab->coid;
        }
        return $ar_idsCab;
    }

    static function getCabeceraUser($desde, $hasta, $user)
    {
        $pagosCab = CobroscabData::getByAllFechasUser($desde, $hasta, $user);
        foreach ($pagosCab as $pagoCab) {
            $ar_idsCab[] = $pagoCab->coid;
        }
        return $ar_idsCab;
    }

    /* ===== PDF ===== */
    public static function getDetalleFormasPago($idsCabs, $usuario)
    {
        $detalleFormasCobros = CobrosdetData::getAllCobrosFormasDet(implode(',', $idsCabs));
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $pdf->Ln(1);
        $user = UserData::getById($usuario);
        $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
        $pdf->SetFont('Arial', '', 8);
//    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
        $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(190, 5, 'REPORTE RESUMIDO', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
        $pdf->Cell(50, 6, 'FORMA DE PAGO', 'T,B,L,R', 0, 'C', 0, 1);
        $pdf->Cell(50, 6, '', 'T,B', 0, 'C', 0, 1);
        $pdf->Cell(40, 6, 'VALOR', 'T,B,L,R', 1, 'C', 0, 1);
//        $pdf->MultiCell(40,10,implode(',', $idsCabs),1,'C');
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        foreach ($detalleFormasCobros as $pago) {

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
            $pdf->Cell(50, 6, utf8_decode(FormasData::getById($pago->cfid)->cfname), 'T', 0, 'L', 0, 1);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(50, 6, '', 'T', 0, 'C', 0, 1);
            $pdf->Cell(40, 6, "$ " . self::formatNumero($pago->fcvalor), 'T,B', 1, 'R', 0, 1);
            $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(100, 6, 'Total ' . utf8_decode(FormasData::getById($pago->cfid)->cfname), '', 0, 'R', 0, 1);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 6, "$ " . self::formatNumero($pago->fcvalor), 'T,B', 1, 'R', 0, 1);
            $pdf->Ln(5);
            $totalResumido = $totalResumido + $pago->fcvalor;

        }
        $pdf->Ln();
        $pdf->Cell(125, 5, 'Total de Documentos :', 0, 0, 'R', 0, 1);
        $pdf->Cell(40, 5, "$ " . self::formatNumero($totalResumido), 'T', 0, 'R', 0, 1);
        $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
        $pdfile = $pdf->Output();
        return $pdfile;
    } // primera opcion RESUMIDO

    public static function getDetalleFormasCobros($detData, $usuario)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getById($usuario);
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdfile = $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(90, 5, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->Cell(190, 5, 'REPORTE DE COBROS DETALLADO', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(1, 5, '', 'T,B,L', 0, 'L', 0, 1);
        $pdf->Cell(30, 5, 'Factura', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(70, 5, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(15, 5, 'Refer', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, 'Fecha', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, 'Fecha/Depo', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, 'Valor', 'T,B,L,R', 1, 'L', 0, 1);
        $ttTotal = 0;
        $pdf->SetFillColor(192, 192, 192);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(191, 6, 'VENDEDOR : ' . utf8_decode($usuarios->name), 'T,B,R,L', 1, 'L', 1, 1);
        foreach ($formasPagos as $formasPago) {
            $pdf->SetFillColor(185, 185, 185);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(191, 6, utf8_decode($formasPago->cfname), 'T,B,R,L', 1, 'L', 1, 1);
            $totalFP = 0;
            foreach ($detData as $detDat) {
                if ($detDat->formapago == $formasPago->cfid && $detDat->usuario == $usuarios->id) {
//            fcfecdep
                    $pdf->SetFont('Arial', '', 7);
                    $pdf->Cell(1, 5, '', 'T,B,L', 0, 'L', 0, 1);
                    $pdf->Cell(30, 5, $detDat->observacion, 'T,B,L,R', 0, 'L', 0, 1);
                    $pdf->Cell(70, 5, $detDat->cliente, 'T,B,L,R', 0, 'L', 0, 1);
                    $pdf->Cell(15, 5, $detDat->docRefer, 'T,B,L,R', 0, 'L', 0, 1);
                    $fechaP = ($detDat->formapago == 8) ? $detDat->fechadepo : '';
                    $pdf->Cell(25, 5, $detDat->fecha, 'T,B,L,R', 0, 'C', 0, 1);
                    $pdf->Cell(25, 5, $fechaP, 'T,B,L,R', 0, 'C', 0, 1);
                    $pdf->Cell(25, 5, '$ ' . self::formatNumero($detDat->valor), 'T,B,L,R', 1, 'R', 0, 1);
                    $totalFP = $totalFP + $detDat->valor;
                }
            }
            $pdf->SetFillColor(187, 187, 187);
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(166, 5, 'Total ' . utf8_decode($formasPago->cfname) . ' :', 'T,B,L,R', 0, 'R', 1, 1);
            $pdf->Cell(25, 5, '$ ' . self::formatNumero($totalFP), 'T,B,L,R', 1, 'R', 1, 1);
            $pdf->Ln(10);
            $ttTotal = $ttTotal + $totalFP;
        }
//    }
        $pdf->SetFillColor(149, 149, 149);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(166, 5, 'Total :', 'T,B,L,R', 0, 'R', 1, 1);
        $pdf->Cell(25, 5, '$ ' . self::formatNumero($ttTotal), 'T,B,L,R', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdf->Ln();
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleFormasCobrosAdmin($detData, $usuario, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $usuad = 0;
        /**VARIABLE QUE ALMACENA EL USUARIO PARA REALIZAR LOS QUIEBRES **/
        $fp = 0;
        /** VARIABL  QUE ALAMCENA LA FORMA DE PAGO PARA REALIZAR LOS QUIEBRES **/
        $totalUsuario = -1;
        $totalFP = -1;
        $ttTotal = 0;
        $t = 0;
        $nmfp = "";
        $us = "";
        $formaName = "";
        $controw = 0;
        foreach ($detData as $detDat) {
            if ($controw == 0) {
                $pdf->AddPage();
            }
            if ($detDat->usuario != $usuad) {
                if ($totalUsuario > 0) {
                    $pdf->SetFillColor(187, 187, 187);
                    $pdf->Cell(50, 6, "", '', 0, 'L', 1, 1);
                    $pdf->Cell(100, 6, "SUBTOTAL " . utf8_decode($us) . " : ", '', 0, 'R', 1, 1);
                    $pdf->Cell(40, 6, " $ " . self::formatNumero($totalUsuario), '', 1, 'R', 1, 1);
                    $totalUsuario = 0;
                    $controw++;

                }
                /** SE VALIDA EL USUSUARIO */
                $pdf->SetFont('Arial', 'B', 8);
                $pdf->Cell(5, 6, '', '', 0, 'L', 0, 1);
                $pdf->Cell(185, 6, 'VENDEDOR : ' . utf8_decode($detDat->user), '', 1, 'L', 0, 1);
                $usuad = $detDat->usuario;
                $us = $detDat->user;
                $controw++;

            }

            if ($detDat->idetiqueta != $idetiqueta) {
                $pdf->SetFont('Arial', 'B', 9); // titulos
                if ($totalEtq != 0) {
                    $pdf->Cell(50, 6, "", '', 0, 'R', 0, 0);
                    $pdf->Cell(140, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($totalEtq)), '', 1, 'R', 0, 0);
                    $totalEtq = 0;
                    $controw++;
                }
                $idetiqueta = $detDat->idetiqueta;
                $pdf->SetFont('Arial', 'B', 9); // titulos
                $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
                $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $detDat->etiqueta), '', 1, 'L', 0, 0);
                $controw++;
            }
            $etiqueta = $detDat->etiqueta;


            if ($detDat->formaid != $fp) {
                /** SE VALIDA EL USUSUARIO */
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . strtoupper(utf8_decode($formaName)) . " : $ " . self::formatNumero($totalFP), '', 1, 'R', 1, 1);
                    $controw++;

                }
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(25, 6, "", '', 0, 'L', 1, 1);
                $pdf->Cell(165, 6, "Forma : " . utf8_decode($detDat->nombreformapago), '', 1, 'L', 1, 1);
                $totalFP = 0;
                $controw++;

                $fp = $detDat->formaid;
                $formaName = $detDat->nombreformapago;
            }

            $totalFP = $totalFP + $detDat->valorCCFormas;
            $totalUsuario = $totalUsuario + $detDat->valorCCFormas;

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(18, 5, $detDat->fechaCCobros, '', 0, 'L', 0, 1);
            $pdf->Cell(15, 5, $detDat->idCobro, '', 0, 'C', 0, 1);
            $pdf->Cell(87, 5, $detDat->codigo . ' - ' . substr(utf8_decode(ucwords(strtolower($detDat->cliente))), 0, 50), '', 0, 'L', 0, 1);
            $banco = "";

            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $pdf->Cell(30, 5, $banco, '', 0, 'L', 0, 1);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }
            $pdf->Cell(15, 5, $numDoc, '', 0, 'R', 0, 1);
            $pdf->Cell(25, 5, '$ ' . self::formatNumero($detDat->valorCCFormas), '', 1, 'R', 0, 1);
            $ttTotal += $detDat->valorCCFormas;
            $totalEtq += $detDat->valorCCFormas;

            $controw++;
            if ($controw >= 40) {
                $controw = 0;
            }
        }

        /** SE MUESTRA EL VALOR DE LA ULTIMA FP */
        if ($totalFP >= 0) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(190, 6, "SUBTOTAL " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), '', 1, 'R', 1, 1);
        }
        if ($totalEtq != 0) {
            $pdf->SetFont('Arial', 'B', 9);

            $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
            $pdf->Cell(150, 6, utf8_decode("TOTAL : " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($totalEtq)), '', 1, 'R', 0, 0);
            $totalvalor = 0;
            $totalAplicado = 0;
            $totalSaldo = 0;
            $controw++;
        }
        if ($totalUsuario >= 0) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(190, 6, "SUBTOTAL " . utf8_decode($detDat->user) . ": $ " . self::formatNumero($totalUsuario), '', 1, 'R', 1, 1);
        }
        $pdf->Cell(190, 5, 'TOTAL : $ ' . self::formatNumero($ttTotal), '', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleFormasCobrosNotAdmin($detData, $usuario, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFont('Arial', '', 8);
        $usuad = 0;
        /**VARIABLE QUE ALMACENA EL USUARIO PARA REALIZAR LOS QUIEBRES **/
        $fp = 0;
        /** VARIABL  QUE ALAMCENA LA FORMA DE PAGO PARA REALIZAR LOS QUIEBRES **/
        $totalUsuario = -1;
        $totalFP = -1;
        $ttTotal = 0;
        $t = 0;
        $nmfp = "";
        $us = "";
        $formaName = "";
        foreach ($detData as $detDat) {
            if (empty($formaName)) {
                $formaName = $detDat->nombreformapago;
            }
            if ($detDat->usuario != $usuad) {
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), '', 1, 'R', 1, 1);
                    $totalFP = 0;
                    $nmfp = "";
                }
                /** SE VALIDA EL USUSUARIO */
                $pdf->SetFillColor(255, 255, 255);
                $pdf->Cell(25, 6, '', '', 0, 'L', 1, 1);
                $pdf->Cell(165, 6, 'VENDEDOR : ' . utf8_decode($detDat->user), '', 1, 'L', 1, 1);
                $usuad = $detDat->usuario;
                $us = $detDat->user;

            }
            if ($detDat->formaid != $fp) {
                /** SE VALIDA EL USUSUARIO */
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), '', 1, 'R', 1, 1);
                }
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(25, 6, '', '', 0, 'L', 1, 1);
                $pdf->Cell(165, 6, utf8_decode($detDat->nombreformapago), '', 1, 'L', 1, 1);
                $totalFP = 0;
                $fp = $detDat->formaid;
                $formaName = $detDat->nombreformapago;
            }
            $totalFP = $totalFP + $detDat->totalCCobros;
            $totalUsuario = $totalUsuario + $detDat->totalCCobros;
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(30, 5, $detDat->idCobro, '', 0, 'L', 0, 1);
            $pdf->Cell(53, 5, substr($detDat->cliente, 0, 28), '', 0, 'L', 0, 1);
            $banco = "";
            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $pdf->Cell(42, 5, $banco, 'T,B,L,R', 0, 'L', 0, 1);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }
            $pdf->Cell(22, 5, $numDoc, '', 0, 'R', 0, 1);
            $pdf->Cell(18, 5, $detDat->fechaCCobros, '', 0, 'C', 0, 1);
            $pdf->Cell(25, 5, '$ ' . self::formatNumero($detDat->totalCCobros), '', 1, 'R', 0, 1);
            $ttTotal += $detDat->totalCCobros;
        }
        /** SE MUESTRA EL VALOR DE LA ULTIMA FP */
        if ($totalFP >= 0) {
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), '', 1, 'R', 1, 1);
        }
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(190, 5, 'TOTAL : $ ' . self::formatNumero($ttTotal), '', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleDocumentosAdmin($detDatas, $usuario)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        foreach ($detDatas as $data) {
            if ($data->idetiqueta != $idetiqueta) {
                $pdf->SetFont('Arial', 'B', 9); // titulos
                if ($total != 0) {
                    $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
                    $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($total)), '', 1, 'R', 0, 0);
                    $total = 0;
                    $controw++;
                }
                $idetiqueta = $data->idetiqueta;
                $pdf->SetFont('Arial', 'B', 9); // titulos
                $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
                $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $data->etiqueta), '', 1, 'L', 0, 0);
                $controw++;
            }
            $etiqueta = $data->etiqueta;
            $pdf->SetFont('Arial', '', 7); // titulos
            $pdf->Cell(15, 5, $data->fecha, '', 0, 'L', 0, 1);
            $pdf->Cell(20, 5, substr($data->tcdescrip, 0, 15), '', 0, 'L', 0, 1);
            $pdf->Cell(9, 5, $data->coid, '', 0, 'C', 0, 1);
            $pdf->Cell(22, 5, $data->numNcr, '', 0, 'L', 0, 1);
            $pdf->Cell(15, 5, $data->tdnombre, '', 0, 'L', 0, 1);
            $pdf->Cell(22, 5, $data->numFactura, '', 0, 'L', 0, 1);
            $pdf->Cell(60, 5, $data->codigo . ' - ' . substr(utf8_decode(ucwords(strtolower($data->cliente))), 0, 55), '', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, '$ ' . self::formatNumero($data->valor), '', 1, 'R', 0, 1);
            $total += $data->valor;
            $totalg += $data->valor;
            $i++;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        if ($total != 0) {
            $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
            $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($total)), '', 1, 'R', 0, 0);
            $total = 0;
        }
        if ($totalg != 0) {
            $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
            $pdf->Cell(150, 6, utf8_decode("Total General :  $ " . FData::formatoNumeroReportes($totalg)), '', 1, 'R', 0, 0);
            $total = 0;
        }
        $pdf->Ln(1);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleDocumentosNotAdmin($detDatas, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        foreach ($detDatas as $data) {
            if ($data->idetiqueta != $idetiqueta) {
                $pdf->SetFont('Arial', 'B', 9); // titulos
                if ($total != 0) {
                    $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
                    $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($total)), '', 1, 'R', 0, 0);
                    $total = 0;
                    $controw++;
                }
                $idetiqueta = $data->idetiqueta;
                $pdf->SetFont('Arial', 'B', 9); // titulos
                $pdf->Cell(15, 6, "", '', 0, 'L', 0, 0);
                $pdf->Cell(50, 6, utf8_decode("Etiqueta : " . $data->etiqueta), '', 1, 'L', 0, 0);
                $controw++;
            }
            $etiqueta = $data->etiqueta;
            $pdf->Cell(10, 5, $i + 1, '', 0, 'L', 0, 1);
            $pdf->Cell(20, 5, $data->fecha, '', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, $data->documento, '', 0, 'L', 0, 1);
            $pdf->Cell(100, 5, $data->codigo . ' - ' . substr(utf8_decode(ucwords(strtolower($data->cliente))), 0, 55), '', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, '$ ' . self::formatNumero($data->valor), '', 1, 'R', 0, 1);
            $total += $data->valor;
            $totalg += $data->valor;

            $i++;
        }
        $pdf->SetFont('Arial', 'B', 9); // titulos
        if ($total != 0) {
            $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
            $pdf->Cell(150, 6, utf8_decode("Subtotal " . $etiqueta . " :  $ " . FData::formatoNumeroReportes($total)), '', 1, 'R', 0, 0);
            $total = 0;
        }
        if ($totalg != 0) {
            $pdf->Cell(40, 6, "", '', 0, 'R', 0, 0);
            $pdf->Cell(150, 6, utf8_decode("Total General :  $ " . FData::formatoNumeroReportes($totalg)), '', 1, 'R', 0, 0);
            $total = 0;
        }
        $pdfile = $pdf->Output();

        return $pdfile;
    }

    /* ===== EXCEL ===== */
    public static function getDetalleFormasPagoExcel($idsCabs, $usuario)
    {
        $detalleFormasCobros = CobrosdetData::getAllCobrosFormasDet(implode(',', $idsCabs));
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $spread = new Spreadsheet();
        $spread
            ->getProperties()
            ->setCreator("SmartTag-Bi")
            ->setTitle('SmartTag-Bi')
            ->setSubject('Reporte de Cartera')
            ->setDescription('Reporte de Cartera')
            ->setKeywords('Informe de Cartera')
            ->setCategory('Excel');
        $hoja = $spread->getActiveSheet();
        $spread->getDefaultStyle()->getFont()->setName('Arial');
        $spread->getDefaultStyle()->getFont()->setSize(8);
        $spread->getActiveSheet()->getColumnDimension('A')->setWidth(17);
        $spread->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $spread->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spread->getActiveSheet()->getStyle('A4');
        $spread->getActiveSheet()->mergeCells('A2:H2');
        $spread->getActiveSheet()->mergeCells('G1:J1');
        $spread->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $spread->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
        $spread->getActiveSheet()->getStyle('A3:K3')->getFont()->setBold(true)->setSize(10);
        $spread->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spread->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
//    $spread->getActiveSheet()->getStyle('C')
//        ->getNumberFormat()
//        ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DMYSLASH);
        $hoja->setTitle("Informe de Saldos"); // Titulo de la pagina
// TITULO DE LA PAGINA
        $hoja->setCellValueByColumnAndRow(1, 1, EmpresasData::getByRuc($_SESSION['ruc'])->em_nombre);
        $hoja->setCellValueByColumnAndRow(1, 2, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta']);
        $hoja->setCellValueByColumnAndRow(7, 1, "Usuario : " . UserData::getById($_SESSION['user_id'])->name);
        /** ======================================*/
        $hoja->setCellValueByColumnAndRow(1, 3, 'FORMA DE PAGO');
        $hoja->setCellValueByColumnAndRow(2, 3, '');
        $hoja->setCellValueByColumnAndRow(3, 3, 'TOTAL');
        $i = 4;
        $o = 0;
        $totalDocumento = 0;
        $contadorDocs = 0;
        $totalResumido = 0;
        foreach ($detalleFormasCobros as $pago) {
            $hoja->setCellValueByColumnAndRow(1, $i, $o + 1);
            $hoja->setCellValueByColumnAndRow(2, $i, utf8_decode(FormasData::getById($pago->cfid)->cfname));
            $hoja->setCellValueByColumnAndRow(3, $i, '');
            $hoja->setCellValueByColumnAndRow(4, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
            $hoja->setCellValueByColumnAndRow(5, $i,);
            $hoja->setCellValueByColumnAndRow(6, $i, 'Total ' . utf8_decode(FormasData::getById($pago->cfid)->cfname));
            $hoja->setCellValueByColumnAndRow(7, $i, "$ " . number_format($pago->fcvalor, 2, '.', ','));
            $i++;
            $o++;
            $totalResumido = $totalResumido + $pago->fcvalor;

        }
        $hoja->setCellValueByColumnAndRow(2, $i, "Total de Documentos :$ " . number_format($totalResumido, 2, ',', '.'));

        $fileName = "InformeCarteraCobros.xlsx";
# Crear un "escritor"
        $writer = new Xlsx($spread);
# Le pasamos la ruta de guardado
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $excel = $writer->save('php://output');
        return $excel;
    }

    public static function getDetalleFormasCobrosAdminExcel($detData, $usuario, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(5);
        $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
        $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 5, 'DETALLADO POR FORMAS', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(18, 5, 'Fecha', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(18, 5, 'Idcobro', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(65, 5, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(42, 5, 'Banco', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(22, 5, '#Doc', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, 'Valor', 'T,B,L,R', 1, 'L', 0, 1);
        $usuad = 0;
        /**VARIABLE QUE ALMACENA EL USUARIO PARA REALIZAR LOS QUIEBRES **/
        $fp = 0;
        /** VARIABL  QUE ALAMCENA LA FORMA DE PAGO PARA REALIZAR LOS QUIEBRES **/
        $totalUsuario = -1;
        $totalFP = -1;
        $ttTotal = 0;
        $t = 0;
        $nmfp = "";
        $us = "";
        $formaName = "";
        foreach ($detData as $detDat) {
            if (empty($formaName)) {
                $formaName = $detDat->nombreformapago;
            }
            if ($detDat->usuario != $usuad) {
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
                    $totalFP = 0;
                    $nmfp = "";
                }
                if ($totalUsuario > 0) {
                    $pdf->SetFillColor(185, 185, 185);
                    $pdf->Cell(50, 6, "", 'T,B,L', 0, 'L', 1, 1);
                    $pdf->Cell(100, 6, "SUBTOTAL " . utf8_decode($us) . " : ", 'T,B', 0, 'R', 1, 1);
                    $pdf->Cell(40, 6, " $ " . self::formatNumero($totalUsuario), 'T,B,R', 1, 'R', 1, 1);
                    $totalUsuario = 0;
                }
                /** SE VALIDA EL USUSUARIO */
                $pdf->SetFillColor(192, 192, 192);
                $pdf->Cell(190, 6, 'VENDEDOR : ' . utf8_decode($detDat->user), 'T,B,R,L', 1, 'L', 1, 1);
                $usuad = $detDat->usuario;
                $us = $detDat->user;

            }
            if ($detDat->formaid != $fp) {
                /** SE VALIDA EL USUSUARIO */
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
                }
                $pdf->SetFillColor(185, 185, 185);
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(190, 6, utf8_decode($detDat->nombreformapago), 'T,B,R,L', 1, 'L', 1, 1);
                $totalFP = 0;
                $fp = $detDat->formaid;
                $formaName = $detDat->nombreformapago;
            }
            $totalFP = $totalFP + $detDat->valorCCFormas;
            $totalUsuario = $totalUsuario + $detDat->valorCCFormas;
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(18, 5, $detDat->fechaCCobros, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(18, 5, $detDat->idCobro, 'T,B,L,R', 0, 'C', 0, 1);
            $pdf->Cell(65, 5, $detDat->codigo . ' - ' . ucwords(strtolower($detDat->cliente)), 'T,B,L,R', 0, 'L', 0, 1);
            $banco = "";
            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $pdf->Cell(42, 5, $banco, 'T,B,L,R', 0, 'L', 0, 1);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }
            $pdf->Cell(22, 5, $numDoc, 'T,B,L,R', 0, 'R', 0, 1);
            $pdf->Cell(25, 5, '$ ' . self::formatNumero($detDat->valorCCFormas), 'T,B,L,R', 1, 'R', 0, 1);
            $ttTotal += $detDat->valorCCFormas;
        }
        /** SE MUESTRA EL VALOR DE LA ULTIMA FP */
        if ($totalFP >= 0) {
            $pdf->SetFillColor(185, 185, 185);
            $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
        }
        if ($totalUsuario >= 0) {
            $pdf->SetFillColor(187, 187, 187);
            $pdf->Cell(50, 6, "", 'T,B,L', 0, 'L', 1, 1);
            $pdf->Cell(100, 6, "SUBTOTAL " . utf8_decode($detDat->user) . " : ", 'T,B', 0, 'R', 1, 1);
            $pdf->Cell(40, 6, " $ " . self::formatNumero($totalUsuario), 'T,B,R', 1, 'R', 1, 1);
        }
        $pdf->SetFillColor(221, 206, 102);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(165, 5, 'Total :', 'T,B,L,R', 0, 'R', 1, 1);
        $pdf->Cell(25, 5, '$ ' . self::formatNumero($ttTotal), 'T,B,L,R', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleFormasCobrosNotAdminExcel($detData, $usuario, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(5);
        $pdf->Cell(98, 5, 'VENDEDOR : ' . UserData::getById($usuario)->name, 0, 1, 'L', 0, 1);
        $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 5, 'DETALLADO POR FORMAS', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(30, 5, 'Idcobro', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(53, 5, 'Cliente', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(42, 5, 'Banco', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(22, 5, '#Doc', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(18, 5, 'Fecha', 'T,B,L,R', 0, 'L', 0, 1);
        $pdf->Cell(25, 5, 'Valor', 'T,B,L,R', 1, 'L', 0, 1);
        $pdf->SetFont('Arial', '', 8);
        $usuad = 0;
        /**VARIABLE QUE ALMACENA EL USUARIO PARA REALIZAR LOS QUIEBRES **/
        $fp = 0;
        /** VARIABL  QUE ALAMCENA LA FORMA DE PAGO PARA REALIZAR LOS QUIEBRES **/
        $totalUsuario = -1;
        $totalFP = -1;
        $ttTotal = 0;
        $t = 0;
        $nmfp = "";
        $us = "";
        $formaName = "";
        foreach ($detData as $detDat) {
            if (empty($formaName)) {
                $formaName = $detDat->nombreformapago;
            }
            if ($detDat->usuario != $usuad) {
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
                    $totalFP = 0;
                    $nmfp = "";
                }
                /** SE VALIDA EL USUSUARIO */
                $pdf->SetFillColor(192, 192, 192);
                $pdf->Cell(190, 6, 'VENDEDOR : ' . utf8_decode($detDat->user), 'T,B,R,L', 1, 'L', 1, 1);
                $usuad = $detDat->usuario;
                $us = $detDat->user;

            }
            if ($detDat->formaid != $fp) {
                /** SE VALIDA EL USUSUARIO */
                if ($totalFP > 0) {
                    $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
                }
                $pdf->SetFillColor(230, 230, 0);
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(190, 6, utf8_decode($detDat->nombreformapago), 'T,B,R,L', 1, 'L', 1, 1);
                $totalFP = 0;
                $fp = $detDat->formaid;
                $formaName = $detDat->nombreformapago;
            }
            $totalFP = $totalFP + $detDat->totalCCobros;
            $totalUsuario = $totalUsuario + $detDat->totalCCobros;
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(30, 5, $detDat->idCobro, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(53, 5, $detDat->codigo . ' - ' . ucwords(strtolower($detDat->cliente)), 'T,B,L,R', 0, 'L', 0, 1);
            $banco = "";
            if (!empty($detDat->bancoCliente)) {
                $banco = $detDat->bancoCliente;
            }
            if (!empty($detDat->bancoPropio)) {
                $banco = $detDat->bancoPropio;
            }
            $pdf->Cell(42, 5, $banco, 'T,B,L,R', 0, 'L', 0, 1);
            $numDoc = "";
            if (!empty($detDat->numerodoc)) {
                $numDoc = $detDat->numerodoc;
            }
            if (!empty($detDat->numctapro)) {
                $numDoc = $detDat->numctapro;
            }
            $pdf->Cell(22, 5, $numDoc, 'T,B,L,R', 0, 'R', 0, 1);
            $pdf->Cell(18, 5, $detDat->fechaCCobros, 'T,B,L,R', 0, 'C', 0, 1);
            $pdf->Cell(25, 5, '$ ' . self::formatNumero($detDat->totalCCobros), 'T,B,L,R', 1, 'R', 0, 1);
            $ttTotal += $detDat->totalCCobros;
        }
        /** SE MUESTRA EL VALOR DE LA ULTIMA FP */
        if ($totalFP >= 0) {
            $pdf->SetFillColor(230, 230, 21);
            $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
        }
        /*if ($totalUsuario >= 0) {
          $pdf->SetFillColor(192, 180, 180);
          $pdf->Cell(50, 6, "", 'T,B,L', 0, 'L', 1, 1);
          $pdf->Cell(100, 6, "SUBTOTAL " . utf8_decode($detDat->user) . " : ", 'T,B', 0, 'R', 1, 1);
          $pdf->Cell(40, 6, " $ " . self::formatNumero($totalUsuario), 'T,B,R', 1, 'R', 1, 1);
        }*/
        $pdf->SetFillColor(185, 185, 185);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(165, 5, 'Total :', 'T,B,L,R', 0, 'R', 1, 1);
        $pdf->Cell(25, 5, '$ ' . self::formatNumero($ttTotal), 'T,B,L,R', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleDocumentosAdminExcel($detDatas, $usuario, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(5);
        $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
        $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 5, 'DETALLADO POR FORMAS', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(192, 180, 180);
        $pdf->Cell(10, 6, '', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(20, 6, 'Fecha', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(30, 6, 'Documento', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(100, 6, 'Cliente', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(30, 6, 'Valor', 'T,B,L,R', 1, 'C', 1, 1);
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        foreach ($detDatas as $data) {
            $pdf->Cell(10, 5, $i + 1, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(20, 5, $data->fecha, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, $data->documento, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(100, 5, $data->codigo . ' - ' . ucwords(strtolower($data->cliente)), 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, '$ ' . self::formatNumero($data->valor), 'T,B,L,R', 1, 'R', 0, 1);
            $total = $total + $data->valor;
            $i++;
        }
        $pdf->Cell(20, 5, '', 'T,B,L', 0, 'L', 1, 1);
        $pdf->Cell(30, 5, '', 'T,B', 0, 'L', 1, 1);
        $pdf->Cell(110, 5, 'Total : $ ', 'T,B', 0, 'R', 1, 1);
        $pdf->Cell(30, 5, '$ ' . self::formatNumero($total), 'T,B,L,R', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    public static function getDetalleDocumentosNotAdminExcel($detDatas, $desde, $hasta)
    {
        $formasPagos = FormasData::getAllActive();
        $usuarios = UserData::getAllActive();
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(5);
        $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
        $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
        $pdf->Ln(3);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(190, 5, 'DETALLADO POR DOCUMENTOS', 0, 1, 'C', 0, 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(187, 187, 187);
        $pdf->Cell(10, 6, '', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(20, 6, 'Fecha', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(30, 6, 'Documento', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(100, 6, 'Cliente', 'T,B,L,R', 0, 'C', 1, 1);
        $pdf->Cell(30, 6, 'Valor', 'T,B,L,R', 1, 'C', 1, 1);
        $pdf->SetFont('Arial', '', 8);
        $i = 0;
        foreach ($detDatas as $data) {
            $pdf->Cell(10, 5, $i + 1, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(20, 5, $data->fecha, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, $data->documento, 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(100, 5, $data->codigo . ' - ' . ucwords(strtolower($data->cliente)), 'T,B,L,R', 0, 'L', 0, 1);
            $pdf->Cell(30, 5, self::formatNumero($data->valor), 'T,B,L,R', 1, 'R', 0, 1);
            $total = $total + $data->valor;
            $i++;
        }
        $pdf->Cell(20, 5, '', 'T,B,L', 0, 'L', 1, 1);
        $pdf->Cell(30, 5, '', 'T,B', 0, 'L', 1, 1);
        $pdf->Cell(110, 5, 'Total : $ ', 'T,B', 0, 'R', 1, 1);
        $pdf->Cell(30, 5, self::formatNumero($total), 'T,B,L,R', 1, 'R', 1, 1);
        $pdf->Ln(3);
        $pdfile = $pdf->Output();
        return $pdfile;
    }

    /** ===== RESCATA LA INFORMACION DE LOS COBROS PARA LA VISUALIZACION POR DETALLE ***/

    /** TIPO DE INFORME DETALLE FORMA DE PAGO */
    public static function getDetInfoCobros($post)
    {
        $where = 'WHERE t1.coactivo = 1 and DATE (t1.cofecha) >= "' . $post['desde'] . '" and DATE (t1.cofecha) <= "' . $post['hasta'] . '"';
        if ($post['cliente'] != 0) {
            $where .= " and t6.ceid = " . $post['cliente'] . " ";
        }
        if ($post['etiquetac'] != 0) {
            $where .= ' and t6.setq_id = ' . $post['etiquetac'] . " ";
        }
        if ($post['sucursal'] != 0) {
            $where .= ' and t1.sucursal_id = ' . $post['sucursal'] . " ";
        }
        $where .= " ORDER BY t2.cfid,t1.user_id,t6.setq_id,t1.cofecha, t2.cfid, idCobro ASC";

        $detData = CobroscabData::getDataForReport($where);

        return $detData;
    }

    /** TIPO DE  INFORME DETALLE POR DOCUMENTO */
    public static function getDetInfoDocumentos($post)
    {
        $where = 'where DATE(a.cofecha) >= "' . $post['desde'] . '" and DATE (a.cofecha) <= "' . $post['hasta'] . '" ';
        if ($post['cliente'] != 0) {
            $where .= " and c.ceid = " . $post['cliente'] . " ";
        }
        if ($post['etiquetac'] != 0) {
            $where .= ' and c.setq_id = ' . $post['etiquetac'] . " ";
        }
        if ($post['sucursal'] != 0) {
            $where .= ' and a.sucursal_id = ' . $post['sucursal'] . " ";
        }
        $where .= " and a.coactivo = 1 order by f.id_etiq,date(a.cofecha),a.coid asc";

        $detData = CobroscabData::getDataForReportDocumentos($where);
        return $detData;
    }

    /** TIPO DE  INFORME DETALLE POR DOCUMENTO */
    public static function getDetInfoDocumentosNotAdmin($post)
    {
        $where = 'where DATE(a.cofecha) >= "' . $post['desde'] . '" and DATE (a.cofecha) <= "' . $post['hasta'] . '" ';
        if ($post['cliente'] != 0) {
            $where .= " and c.ceid = " . $post['cliente'] . " ";
        }
        if ($post['etiquetac'] != 0) {
            $where .= ' and c.setq_id = ' . $post['etiquetac'] . " ";
        }
        if ($post['sucursal'] != 0) {
            $where .= ' and a.sucursal_id = ' . $post['sucursal'] . " ";
        }
        $where .= " and a.user_id = " . $_SESSION['user_id'] . "  order by f.id_etiq,date(a.cofecha) asc";
        $detData = CobroscabData::getDataForReportDocumentosUser($where);
        return $detData;
    }

    public static function getDetInfoCobrosNotAdmin($desde, $hasta, $user)
    {
        $detData = CobroscabData::getDataForReportNotAdmin($desde, $hasta, $user);
        return $detData;
    }

    public static function formatNumero($numero)
    {
        return number_format($numero, '2', '.', ',');
    }
}

?>
