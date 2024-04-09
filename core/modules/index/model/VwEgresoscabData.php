<?php

class VwEgresoscabData {
  public static $tablename = "vw_egresosCab";

  public function __construct() {
    $this->opid = "";
    $this->em_ruc = "";
    $this->opfecha = "";
    $this->toid = "";
    $this->opsentido = "";
    $this->boid = "";
    $this->opnumdoc = "";
    $this->optiporel = "";
    $this->opidrel = "";
    $this->opid_transfer = "";
    $this->boid_transfer = "";
    $this->opcomenta = "";
    $this->opestado = "";
    $this->user_id = "";
    $this->user_update = "";
    $this->user_anula = "";
    $this->opcreate_at = "";
    $this->opupdate_at = "";
    $this->opanula_at = "";
    $this->trid = "";
    $this->ceid = "";
    $this->peestado = "";
    $this->pefecha = "";
  }

  public static function getAll() {
    $sql = "select * from " . self::$tablename . " ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new VwEgresoscabData());
  }

  public static function getAllFechaCliente($fecha, $cliente) {
    $sql = "select * from " . self::$tablename . " where pefecha <= \"$fecha\" and peestado = 1 and ceid = $cliente ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new VwEgresoscabData());
  }
}

?>
