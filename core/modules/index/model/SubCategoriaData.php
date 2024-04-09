<?php

class SubCategoriaData
{
    public static $Conx = "";

    public static $tablename = "sgn_subcategorias";
    public $sbc_id;
    public $cat_id;
    public $cat_nombre;
    public $emp_id;
    public $sbc_nombre;
    public $sbc_createat = "NOW()";

    public function __construct()
    {

    }

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
        $sql = "insert into " . self::$tablename . " (sbc_nombre, cat_id, emp_id, sbc_createat) value ($this->sbc_nombre, $this->cat_id, $this->emp_id, $this->sbc_createat)";
        return Executor::doit($sql);
    }

    public function add_t($Conx)
    {
        $sql = "insert into " . self::$tablename . " (sbc_nombre, cat_id, emp_id, sbc_createat) value ($this->sbc_nombre, $this->cat_id, $this->emp_id, $this->sbc_createat)";
        return Executor::doit_T($sql, $Conx);
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " a INNER JOIN sgn_categorias b ON b.cat_id=a.cat_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SubCategoriaData());
    }

    public static function getAllByEmpId($emp_id)
    {
        $sql = "select a.*, b.cat_nombre from " . self::$tablename . " a INNER JOIN sgn_categorias b ON b.cat_id=a.cat_id where a.emp_id=$emp_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SubCategoriaData());
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where ctid=$id";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set sbc_nombre=\"$this->sbc_nombre\", cat_id=\"$this->cat_id\" where sbc_id=$this->sbc_id";
        return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set sbc_nombre=$this->sbc_nombre, cat_id=$this->cat_id where sbc_id=$this->sbc_id";
        return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where sbc_id=$this->sbc_id";
        return Executor::doit($sql);
    }

    public function del_t($Conx)
    {
        $sql = "delete from " . self::$tablename . " where sbc_id=$this->sbc_id";
        return Executor::doit($sql, $Conx);
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where sbc_id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new SubCategoriaData());
    }

    public static function getByCod($cod)
    {
        $sql = "select name from " . self::$tablename . " where cod=$cod";
        $query = Executor::doit($sql);
        return Model::one($query[0], new SubCategoriaData());
    }

    public static function getAllCategory()
    {
        $sql = "SELECT a.ctid,a.ctname , (select count(ct2id) from in_category2 b where b.ct_id = a.ctid) as totalsubcat from " . self::$tablename . " a";
        $query = Executor::doit($sql);
        return Model::many($query[0], new SubCategoriaData());
    }
}
