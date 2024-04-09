<?php

class ZonaData
{

    public static $tablename = "cc_zonas";

    public function __construct()
    {
        $this->zoid = "";
        $this->zoname = "";
        $this->zoestado = "";
        $this->zocreated_at = "NOW()";
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . "(zoname, zoestado, zocreated_at) value ($this->zoname, $this->zoestado, $this->zocreated_at)";
        return Executor::doit($sql);
//        return $sql;
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where zoid=$id";
        return Executor::doit($sql);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where bcid=$this->id";
        Executor::doit($sql);
    }

// partiendo de que ya tenemos creado un objecto ZonaData previamente utilizamos el contexto
    public function update()
    {
        $sql = "update " . self::$tablename . " set zoname=$this->zoname ,zoestado=$this->zoestado where zoid=$this->zoid";
        return Executor::doit($sql);
        // return $sql;
    }
    public static function updateState($id)
    {
        $sql = "update " . self::$tablename . " set zoestado=0 where zoid=$id";
        return Executor::doit($sql);
        // return $sql;
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where zoid=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ZonaData());
    }

    public static function getByName($name)
    {
        $sql = "select * from " . self::$tablename . " where venombre=\"$name\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ZonaData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonaData());
    }

    public static function getAllPagination($paginacion, $cantmostrar) {
        $sql = "select * from " . self::$tablename . "  LIMIT " . (($paginacion - 1) * $cantmostrar) . " , " . $cantmostrar;
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonaData());
    }

    public static function getAllSearchName($parametro) {
        $sql = "select * from " . self::$tablename . " where name REGEXP \"$parametro\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonaData());
    }

    public static function getAllActive()
    {
        $sql = "select * from " . self::$tablename . " where zoestado = 1 ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonaData());
    }
}
