<?php

class ReporteCobros {

  public static function getCabecera($desde, $hasta) {
    $pagosCab = CobroscabData::getByAllFechas($desde, $hasta);
    foreach ($pagosCab as $pagoCab) {
      $ar_idsCab[] = $pagoCab->coid;
    }
    return $ar_idsCab;
  }

  static function getCabeceraUser($desde, $hasta, $user) {
    $pagosCab = CobroscabData::getByAllFechasUser($desde, $hasta, $user);
    foreach ($pagosCab as $pagoCab) {
      $ar_idsCab[] = $pagoCab->coid;
    }
    return $ar_idsCab;
  }

  /* ===== FUNCIONES PARA LA VISUALIZACION DE COBROS GENERAL ===== */
  public static function getDetalleFormasPago($idsCabs, $usuario) {
    $detalleFormasCobros = CobrosdetData::getAllCobrosFormasDet(implode(',', $idsCabs));
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
//    $pdf->Cell(100, 5, $empresa->em_nombre, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
//    $pdf->SetFont('Arial', 'B', 11);
//    $pdf->Cell(100, 4, "REPORTE DE COBROS", 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $user = UserData::getById($usuario);
    $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 8);
//    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 5, 'REPORTE RESUMIDO', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(50, 6, 'FORMA DE PAGO', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(50, 6, '', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(40, 6, 'VALOR', 'T,B,L,R', 1, 'C', 0, 1);
    $totalDocumento = 0;
    $contadorDocs = 0;
    $totalResumido = 0;
    foreach ($detalleFormasCobros as $pago) {

      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(50, 6, utf8_decode(FormasData::getById($pago->cfid)->cfname), 'T', 0, 'L', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(50, 6, '', 'T', 0, 'C', 0, 1);
      $pdf->Cell(40, 6, "$ " . number_format($pago->fcvalor, 2, '.', ','), 'T,B', 1, 'R', 0, 1);
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(100, 6, 'Total ' . utf8_decode(FormasData::getById($pago->cfid)->cfname), '', 0, 'R', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(40, 6, "$ " . number_format($pago->fcvalor, 2, '.', ','), 'T,B', 1, 'R', 0, 1);
      $pdf->Ln(5);
      $totalResumido = $totalResumido + $pago->fcvalor;

    }
    $pdf->Ln();
    $pdf->Cell(125, 5, 'Total de Documentos :', 0, 0, 'R', 0, 1);
    $pdf->Cell(40, 5, "$ " . number_format($totalResumido, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
    $pdfile = $pdf->Output();
    return $pdfile;
  }

  /* ===== FUNCIONES PARA LA VISUALIZACION DE COBROS DETALLADOS ===== */
  public static function getDetalleFormasCobros($detData, $usuario) {
    $formasPagos = FormasData::getAllActive();
    $usuarios = UserData::getById($usuario);
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $pdf = new PDF();
    $pdfile = $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(90, 5, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', 15);
    $pdf->Cell(190, 5, 'REPORTE DE COBROS DETALLADO', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(1, 5, '', 'T,B,L', 0, 'L', 0, 1);
    $pdf->Cell(30, 5, 'FACTURA', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(70, 5, 'CLIENTE', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(15, 5, 'REFER', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(25, 5, 'FECHA', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(25, 5, 'FECHA/DEPO', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(25, 5, 'VALOR', 'T,B,L,R', 1, 'L', 0, 1);
    $ttTotal = 0;
    $pdf->SetFillColor(192, 192, 192);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(191, 6, 'VENDEDOR : ' . utf8_decode($usuarios->name), 'T,B,R,L', 1, 'L', 1, 1);
    foreach ($formasPagos as $formasPago) {
      $pdf->SetFillColor(230, 230, 0);
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
          $pdf->Cell(25, 5, '$ ' . number_format($detDat->valor, 2, '.', ','), 'T,B,L,R', 1, 'R', 0, 1);
          $totalFP = $totalFP + $detDat->valor;
        }
      }
      $pdf->SetFillColor(221, 206, 102);
      $pdf->SetFont('Arial', '', 10);
      $pdf->Cell(166, 5, 'Total ' . utf8_decode($formasPago->cfname) . ' :', 'T,B,L,R', 0, 'R', 1, 1);
      $pdf->Cell(25, 5, '$ ' . number_format($totalFP, 2, '.', ','), 'T,B,L,R', 1, 'R', 1, 1);
      $pdf->Ln(10);
      $ttTotal = $ttTotal + $totalFP;
    }
//    }
    $pdf->SetFillColor(221, 206, 102);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(166, 5, 'Total :', 'T,B,L,R', 0, 'R', 1, 1);
    $pdf->Cell(25, 5, '$ ' . number_format($ttTotal, 2, '.', ','), 'T,B,L,R', 1, 'R', 1, 1);
    $pdf->Ln(3);
    $pdf->Ln();
    $pdfile = $pdf->Output();
    return $pdfile;
  }

  public static function getDetalleFormasCobrosAdmin($detData, $usuario, $desde, $hasta) {
    $formasPagos = FormasData::getAllActive();
    $usuarios = UserData::getAllActive();
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(5);
    $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(190, 5, 'REPORTE DETALLADO', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(18, 5, 'FECHA', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(18, 5, 'IDCOBRO', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(65, 5, 'CLIENTE', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(42, 5, 'BANCO', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(22, 5, '# DOC', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(25, 5, 'VALOR', 'T,B,L,R', 1, 'L', 0, 1);
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
          $pdf->SetFillColor(192, 180, 180);
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
        $pdf->SetFillColor(230, 230, 0);
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
      $pdf->Cell(65, 5, substr($detDat->cliente, 0, 28), 'T,B,L,R', 0, 'L', 0, 1);
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
      $pdf->SetFillColor(230, 230, 21);
      $pdf->Cell(190, 6, "Subtotal " . utf8_decode($formaName) . " : $ " . self::formatNumero($totalFP), 'T,B,R,L', 1, 'R', 1, 1);
    }
    if ($totalUsuario >= 0) {
      $pdf->SetFillColor(192, 180, 180);
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

  public static function getDetalleDocumentosAdmin($detDatas, $usuario, $desde, $hasta) {
    $formasPagos = FormasData::getAllActive();
    $usuarios = UserData::getAllActive();
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(5);
    $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(190, 5, 'REPORTE DETALLADO POR DOCUMENTOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetFillColor(192, 180, 180);
    $pdf->Cell(10, 6, '', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(20, 6, 'FECHA', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(30, 6, 'DOCUMENTO', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(100, 6, 'CLIENTE', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(30, 6, 'VALOR', 'T,B,L,R', 1, 'C', 1, 1);
    $pdf->SetFont('Arial', '', 8);
    $i = 0;
    foreach ($detDatas as $data) {
      $pdf->Cell(10, 5, $i + 1, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(20, 5, $data->fecha, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, $data->documento, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(100, 5, $data->cliente, 'T,B,L,R', 0, 'L', 0, 1);
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

  public static function getDetalleDocumentosNotAdmin($detDatas, $desde, $hasta) {
    $formasPagos = FormasData::getAllActive();
    $usuarios = UserData::getAllActive();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(5);
    $pdf->Cell(98, 5, 'VENDEDOR : TODOS', 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(190, 5, 'REPORTE DETALLADO POR DOCUMENTOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetFillColor(192, 180, 180);
    $pdf->Cell(10, 6, '', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(20, 6, 'FECHA', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(30, 6, 'DOCUMENTO', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(100, 6, 'CLIENTE', 'T,B,L,R', 0, 'C', 1, 1);
    $pdf->Cell(30, 6, 'VALOR', 'T,B,L,R', 1, 'C', 1, 1);
    $pdf->SetFont('Arial', '', 8);
    $i = 0;
    foreach ($detDatas as $data) {
      $pdf->Cell(10, 5, $i + 1, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(20, 5, $data->fecha, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(30, 5, $data->documento, 'T,B,L,R', 0, 'L', 0, 1);
      $pdf->Cell(100, 5, $data->cliente, 'T,B,L,R', 0, 'L', 0, 1);
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

  public static function getDetalleFormasCobrosNotAdmin($detData, $usuario, $desde, $hasta) {
    $formasPagos = FormasData::getAllActive();
    $usuarios = UserData::getAllActive();
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Ln(5);
    $pdf->Cell(98, 5, 'VENDEDOR : ' . UserData::getById($usuario)->name, 0, 1, 'L', 0, 1);
    $pdf->Cell(90, 4, 'DESDE : ' . $desde . ' / HASTA : ' . $hasta, 0, 0, 'L', 0, 1);
    $pdf->Ln(2);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(190, 5, 'REPORTE DETALLADO', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(30, 5, 'IDCOBRO', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(53, 5, 'CLIENTE', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(42, 5, 'BANCO', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(22, 5, '# DOC', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(18, 5, 'FECHA', 'T,B,L,R', 0, 'L', 0, 1);
    $pdf->Cell(25, 5, 'VALOR', 'T,B,L,R', 1, 'L', 0, 1);
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
      $pdf->Cell(53, 5, substr($detDat->cliente, 0, 28), 'T,B,L,R', 0, 'L', 0, 1);
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
    $pdf->SetFillColor(221, 206, 102);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(165, 5, 'Total :', 'T,B,L,R', 0, 'R', 1, 1);
    $pdf->Cell(25, 5, '$ ' . self::formatNumero($ttTotal), 'T,B,L,R', 1, 'R', 1, 1);
    $pdf->Ln(3);
    $pdfile = $pdf->Output();
    return $pdfile;
  }

  /** ===== RESCATA LA INFORMACION DE LOS COBROS PARA LA VISUALIZACION POR DETALLE ***/
  /** TIPO DE INFORME DETALLE FORMA DE PAGO */
  public static function getDetInfoCobros($desde, $hasta) {
    $detData = CobroscabData::getDataForReport($desde, $hasta);
    return $detData;
  }

  /** TIPO DE  INFORME DETALLE POR DOCUMENTO */
  public static function getDetInfoDocumentos($desde, $hasta) {
    $detData = CobroscabData::getDataForReportDocumentos($desde, $hasta);
    return $detData;
  }

  /** TIPO DE  INFORME DETALLE POR DOCUMENTO */
  public static function getDetInfoDocumentosNotAdmin($desde, $hasta,$user) {
    $detData = CobroscabData::getDataForReportDocumentosUser($desde, $hasta,$user);
    return $detData;
  }

  public static function getDetInfoCobrosNotAdmin($desde, $hasta, $user) {
    $detData = CobroscabData::getDataForReportNotAdmin($desde, $hasta, $user);
    return $detData;
  }

  public static function formatNumero($numero) {
    return number_format($numero, '2', '.', ',');
  }

}

?>