<?php

class RangoIpData
{
    public static $Conx = "";
    public static $tablename = "sgn_rangosip";
    public $rip_id;
    public $pai_id;
    public $pai_nombre;
    public $rip_rangoini;
    public $rip_rangofin;

    public static function CerrarTransaccion($conx)
    {
      $ObjExe = new Executor();
      $ObjExe::$conx2 = $conx;
      return $ObjExe->CloseTransaction();
    }

    public static function CancelarTransaccion($conx)
    {
      $ObjExe = new Executor();
      $ObjExe::$conx2 = $conx;
      return $ObjExe->BackTransaction();
    }

    public static function AbrirTransaccion()
    {
      $ObjExe = new Executor();
      $ObjExe->OpenTransaction();
      self::$Conx = $ObjExe::$conx2;
      return $ObjExe::$transac;
    }

    public function add()
    {
      $sql = "insert into " . self::$tablename . " (pai_id, rip_rangoini, rip_rangofin) value ($this->pai_id, $this->rip_rangoini, $this->rip_rangofin)";
      return Executor::doit($sql);
    }

    public function add_t($Conx)
    {
      $sql = "insert into " . self::$tablename . " (pai_id, rip_rangoini, rip_rangofin) value ($this->pai_id, $this->rip_rangoini, $this->rip_rangofin)";
      return Executor::doit_T($sql, $Conx);
    }

    public static function getAll()
    {
      $sql = "select a.*, b.pai_nombre from " . self::$tablename . " a INNER JOIN sgn_pais b ON b.pai_id = a.pai_id";
      $query = Executor::doit($sql);
      return Model::many($query[0], new RangoIpData());
    }

    public static function delById($id)
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$id";
      return Executor::doit($sql);
    }

    public function update()
    {
      $sql = "update " . self::$tablename . " set pai_id=\"$this->pai_id\" where pai_id=$this->pai_id";
      return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
      $sql = "update " . self::$tablename . " set pai_id=$this->pai_id, rip_rangoini=$this->rip_rangoini, rip_rangofin=$this->rip_rangofin where rip_id=$this->rip_id";
      return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$this->rip_id";
      return Executor::doit($sql);
    }

    public function del_t($Conx)
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$this->rip_id";
      return Executor::doit($sql, $Conx);
    }

    public static function getById($id)
    {
      $sql = "select * from " . self::$tablename . " where rip_id=$id";
      $query = Executor::doit($sql);
      return Model::one($query[0], new RangoIpData());
    }
}
