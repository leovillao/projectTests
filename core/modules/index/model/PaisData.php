<?php

class PaisData
{
    public static $Conx = "";
    public static $tablename = "sgn_pais";
    public $pai_id;
    public $pai_codigo;
    public $pai_nombre;
    public $pai_prefijo;

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
      $sql = "insert into " . self::$tablename . " (pai_codigo, pai_nombre, pai_prefijo) value ($this->pai_codigo, $this->pai_nombre, $this->pai_prefijo)";
      return Executor::doit($sql);
    }

    public function add_t($Conx)
    {
      $sql = "insert into " . self::$tablename . " (pai_codigo, pai_nombre, pai_prefijo) value ($this->pai_codigo, $this->pai_nombre, $this->pai_prefijo)";
      return Executor::doit_T($sql, $Conx);
    }

    public static function getAll()
    {
      $sql = "select * from " . self::$tablename . " ";
      $query = Executor::doit($sql);
      return Model::many($query[0], new PaisData());
    }

    public static function delById($id)
    {
      $sql = "delete from " . self::$tablename . " where pai_id=$id";
      return Executor::doit($sql);
    }

    public function update()
    {
      $sql = "update " . self::$tablename . " set pai_codigo=\"$this->pai_codigo\", pai_nombre=\"$this->pai_nombre\" where pai_id=$this->pai_id";
      return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
      $sql = "update " . self::$tablename . " set pai_codigo=$this->pai_codigo, pai_nombre=$this->pai_nombre, pai_prefijo=$this->pai_prefijo where pai_id=$this->pai_id";
      return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
      $sql = "delete from " . self::$tablename . " where pai_id=$this->pai_id";
      return Executor::doit($sql);
    }

    public function del_t($Conx)
    {
      $sql = "delete from " . self::$tablename . " where pai_id=$this->pai_id";
      return Executor::doit($sql, $Conx);
    }

    public static function getById($id)
    {
      $sql = "select * from " . self::$tablename . " where pai_id=$id";
      $query = Executor::doit($sql);
      return Model::one($query[0], new PaisData());
    }
}
