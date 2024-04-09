<?php

class ReporteAnticipos {

  public static function getCabecera($desde, $hasta, $cliente) {
    if ($cliente != 0) {
      $anticiposCab = AnticipocabData::getByAllFechas($desde, $hasta, $cliente);
    } else {
      $anticiposCab = AnticipocabData::getByAllFechasAll($desde, $hasta);
    }
    foreach ($anticiposCab as $anticipoCab) {
      $ar_idsCab[] = $anticipoCab->anid;
    }
    return $ar_idsCab;
  }

  public static function getCabeceraUser($desde, $hasta, $user) {
    $anticiposCab = AnticipocabData::getByAllFechasUser($desde, $hasta, $user);
    foreach ($anticiposCab as $anticpoCab) {
      $ar_idsCab[] = $anticpoCab->coid;
    }
    return $ar_idsCab;
  }

  /* ===== REPORTE RESUMIDO POR CLIENTE ===== */
  public static function getDetallesAnticipo($idsCabs, $usuario, $cliente) {
    $detalleAnticipos = AnticipocabData::getAllForIdsTcid(implode(',', $idsCabs));
    $detallepagos = AnticipodetData::getByAnIds(implode(',', $idsCabs));
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $anticipos = AnticipoData::getAll();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(100, 5, $empresa->em_nombre, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(100, 4, "REPORTE DE ANTICIPOS", 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $user = UserData::getById($usuario);
    if ($user->pfid == 1) {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    } else {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 5, 'REPORTE DE ANTICIPOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(130, 5, 'CLIENTE : ' . PersonData::getById($cliente)->cename, 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(50, 6, 'TIPO ANTICIPO', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(50, 6, '', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(40, 6, 'VALOR', 'T,B,L,R', 1, 'C', 0, 1);
    $totalDocumento = 0;
    $contadorDocs = 0;
    $totalResumido = 0;
//    $pdf->MultiCell(100, 5,json_encode($detallepagos), 0, 'L', 0, 1);
//    $pdf->Cell(50, 6, , 'T,B', 0, 'C', 0, 1);
    foreach ($detallepagos as $anticipo) {
      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(25, 6, utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 1, 'L', 0, 1);
      $pdf->Cell(125, 6, 'Total ' . utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 0, 'R', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(40, 6, "$ " . number_format($anticipo->tafvalor, 2, '.', ','), 'T,B', 1, 'R', 0, 1);
      $pdf->Ln(5);
      $totalResumido += $anticipo->tafvalor;
    }
    /*foreach ($detalleAnticipos as $anticipos) {
      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(50, 6, utf8_decode(TipocobroData::getById($anticipos->tcid)->tcdescrip), 'T', 0, 'L', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(50, 6, '', 'T', 0, 'C', 0, 1);
      $pdf->Cell(40, 6, " ", 'T,B', 1, 'R', 0, 1);

      $formasAnticpos = AnticipodetData::getByAnIdAgr($anticipos->anid);
      foreach ($formasAnticpos as $formasAnticpo){
        $pdf->Cell(25, 6, "", '', 0, 'L', 0, 1);
        $pdf->Cell(50, 6, utf8_decode(FormasData::getById($formasAnticpo->cfid)->cfname), 'T', 0, 'L', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(50, 6, '', 'T', 0, 'C', 0, 1);
        $pdf->Cell(40, 6, "$ " . $formasAnticpo->tafvalor , 'T,B', 1, 'R', 0, 1);
      }
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(100, 6, 'Total ' . utf8_decode(TipocobroData::getById($anticipos->tcid)->tcdescrip), '', 0, 'R', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(40, 6, "$ " . number_format($anticipos->tanvalor, 2, '.', ','), 'T,B', 1, 'R', 0, 1);
      $pdf->Ln(5);
      $totalResumido = $totalResumido + $anticipos->tanvalor;
    } */
    $pdf->Ln();
    $pdf->Cell(125, 5, 'Total en Documentos :', 0, 0, 'R', 0, 1);
    $pdf->Cell(40, 5, "$ " . number_format($totalResumido, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
    $pdfile = $pdf->Output();
    return $pdfile;
  } /* EL REPORTE SE VISUALIZARA DE UN CLIENTE */
  /* ===== REPORTE RESUMIDO DE GENERAL ===== */
  public static function getDetallesAnticipoAllDocs($idsCabs, $usuario) {
    $detalleAnticipos = AnticipocabData::getAllForIdsTcid(implode(',', $idsCabs));
    $detallepagos = AnticipodetData::getByAnIds(implode(',', $idsCabs));
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $anticipos = AnticipoData::getAll();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(100, 5, $empresa->em_nombre, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(100, 4, "REPORTE DE ANTICIPOS", 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $user = UserData::getById($usuario);
    if ($user->pfid == 1) {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    } else {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 5, 'REPORTE DE ANTICIPOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(25, 5, '', 0, 0, 'L', 0, 0);
    $pdf->Cell(130, 5, 'RESUMIDO GENERAL ', 0, 1, 'L', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(50, 6, 'TIPO ANTICIPO', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(50, 6, '', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(40, 6, 'VALOR', 'T,B,L,R', 1, 'C', 0, 1);
    $totalDocumento = 0;
    $contadorDocs = 0;
    $totalResumido = 0;
    foreach ($detallepagos as $anticipo) {
      $pdf->SetFont('Arial', 'B', 11);
      $pdf->Cell(25, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(25, 6, utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 1, 'L', 0, 1);
      $pdf->Cell(125, 6, 'Total ' . utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 0, 'R', 0, 1);
      $pdf->SetFont('Arial', '', 11);
      $pdf->Cell(40, 6, "$ " . number_format($anticipo->tafvalor, 2, '.', ','), 'T,B', 1, 'R', 0, 1);
      $pdf->Ln(5);
      $totalResumido += $anticipo->tafvalor;
    }
    $pdf->Ln();
    $pdf->Cell(125, 5, 'Total en Documentos :', 0, 0, 'R', 0, 1);
    $pdf->Cell(40, 5, "$ " . number_format($totalResumido, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
    $pdfile = $pdf->Output();
    return $pdfile;
  } /* EL REPORTE SE VISUALIZARA DE UN CLIENTE */
  /* ===== REPORTE DETALLADO GENERAL ===== */
  public static function getDetallesAntDet($idsCabs, $usuario) {
    $detalleAnticipos = AnticipocabData::getAllForIdsDet(implode(',', $idsCabs));
//    $detallepagos = AnticipodetData::getByAnIds(implode(',', $idsCabs));
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $anticipos = AnticipoData::getAll();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(100, 5, $empresa->em_nombre, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(100, 4, "REPORTE DE ANTICIPOS", 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $user = UserData::getById($usuario);
    if ($user->pfid == 1) {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    } else {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 5, 'REPORTE DE ANTICIPOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(190, 5, 'DETALLE GENERAL ', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(5, 6, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(25, 6, 'FECHA', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(25, 6, 'FORMA', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(75, 6, 'CLIENTE', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'VALOR', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'APLICADO', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'SALDO', 'T,B,L,R', 1, 'C', 0, 1);
    $totalDocumento = 0;
    $contadorDocs = 0;
    $totalResumido = 0;
//    $pdf->MultiCell(100, 5,json_encode($detallepagos), 0, 'L', 0, 1);
    foreach ($detalleAnticipos as $anticipo) {
      $pdf->SetFont('Arial', '', 8);
      $pdf->Cell(5, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(25, 6, $anticipo->anfecha, '', 0, 'L', 0, 1);
      $pdf->Cell(25, 6, utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 0, 'L', 0, 1);
      $pdf->Cell(75, 6, utf8_decode($anticipo->cename), '', 0, 'L', 0, 1);
      $pdf->Cell(19, 6, $anticipo->anvalor, '', 0, 'R', 0, 1);
      $pdf->Cell(19, 6, $anticipo->anaplica, '', 0, 'R', 0, 1);
      $pdf->Cell(19, 6, $anticipo->ansaldo, '', 1, 'R', 0, 1);
      $totalanvalor += $anticipo->anvalor;
      $totalanaplica += $anticipo->anaplica;
      $totalansaldo += $anticipo->ansaldo;
    }
    $pdf->Ln();
    $pdf->Cell(130, 6, 'Total :', 0, 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalanvalor, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalanaplica, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalansaldo, 2, ',', '.'), 'T', 1, 'R', 0, 1);
//    $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
    $pdfile = $pdf->Output();
    return $pdfile;
  } /* REPORTE  */

  public static function getDetallesAntDetCliente($idsCabs, $usuario, $cliente) {
    $detalleAnticipos = AnticipocabData::getAllForIdsDet(implode(',', $idsCabs));
    $empresa = EmpresasData::getByRuc(UserData::getById($usuario)->em_ruc);
    $anticipos = AnticipoData::getAll();
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(100, 5, $empresa->em_nombre, 0, 1, 'L', 0, 1);
    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(100, 4, "REPORTE DE ANTICIPOS", 0, 1, 'L', 0, 1);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Ln(1);
    $user = UserData::getById($usuario);
    if ($user->pfid == 1) {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    } else {
      $pdf->Cell(98, 4, 'VENDEDOR : ' . strtoupper($user->name) . ' ' . strtoupper($user->lastname), 0, 1, 'L', 0, 1);
    }
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(98, 4, 'FECHA DE EMISION : ' . date('d-m-Y H:i:s'), 0, 1, 'L', 0, 1);
    $pdf->Cell(190, 7, 'RANGO DE FECHA : ' . $_POST['desde'] . ' HASTA ' . $_POST['hasta'], 0, 1, 'R', 0, 1);
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(190, 5, 'REPORTE DE ANTICIPOS', 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(190, 5, 'CLIENTE : ' . PersonData::getById($cliente)->cename, 0, 1, 'C', 0, 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(5, 6, '', 0, 0, 'L', 0, 1);
    $pdf->Cell(25, 6, 'FECHA', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(25, 6, 'FORMA', 'T,B,L,R', 0, 'C', 0, 1);
    $pdf->Cell(75, 6, 'CLIENTE', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'VALOR', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'APLICADO', 'T,B', 0, 'C', 0, 1);
    $pdf->Cell(20, 6, 'SALDO', 'T,B,L,R', 1, 'C', 0, 1);
    $totalDocumento = 0;
    $contadorDocs = 0;
    $totalResumido = 0;
//    $pdf->MultiCell(100, 5,json_encode($detallepagos), 0, 'L', 0, 1);
    foreach ($detalleAnticipos as $anticipo) {
      $pdf->SetFont('Arial', '', 8);
      $pdf->Cell(5, 6, '', 0, 0, 'L', 0, 1);
      $pdf->Cell(25, 6, $anticipo->anfecha, '', 0, 'L', 0, 1);
      $pdf->Cell(25, 6, utf8_decode(FormasData::getById($anticipo->cfid)->cfname), '', 0, 'L', 0, 1);
      $pdf->Cell(75, 6, utf8_decode($anticipo->cename), '', 0, 'L', 0, 1);
      $pdf->Cell(19, 6, $anticipo->anvalor, '', 0, 'R', 0, 1);
      $pdf->Cell(19, 6, $anticipo->anaplica, '', 0, 'R', 0, 1);
      $pdf->Cell(19, 6, $anticipo->ansaldo, '', 1, 'R', 0, 1);
      $totalanvalor += $anticipo->anvalor;
      $totalanaplica += $anticipo->anaplica;
      $totalansaldo += $anticipo->ansaldo;
    }
    $pdf->Ln();
    $pdf->Cell(130, 6, 'Total :', 0, 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalanvalor, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalanaplica, 2, ',', '.'), 'T', 0, 'R', 0, 1);
    $pdf->Cell(19, 5, "$ " . number_format($totalansaldo, 2, ',', '.'), 'T', 1, 'R', 0, 1);
//    $pdf->MultiCell(100, 5, '', 0, 'L', 0, 1);
    $pdfile = $pdf->Output();
    return $pdfile;
  } /* REPORTE  */
}

?>