<?php

class MenuData
{

    public static $Conx = "";

    public static $tablename = "sgn_menu";
    public $men_id;
    public $men_descripcion;
    public $men_nombre;
    public $men_view;
    public $usr_perfil;

    public function __construct()
    {
    }

    public function add_t($conx)
    {
        $sql = "insert into " . self::$tablename . " (INSERT INTO `sgn_menu`(`men_id`, `men_descripcion`, `men_nombre`, `men_view`) VALUES ($this->sgn_menu,$this->men_descripcion,$this->men_nombre,$this->men_view))";
        return Executor::doit_T($sql, $conx);
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getAllActive()
    {
        $sql = "select * from " . self::$tablename . " where mnactivo = 1 order by mnorden asc ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where mnid = $id and mnactivo = 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuData());
    }

    public static function getByOrden($id)
    {
        $sql = "select * from " . self::$tablename . " where mnorden = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuData());
    }

    public static function getByIds($id)
    {
        $sql = "select * from " . self::$tablename . " where mnid = $id order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getAllN($pfid)
    {
        $sql = "select * from " . self::$tablename . " where mnnodo = 'N'  and mnactivo = 1 and mnid in (select mnid from gen_accesos where pfid = $pfid) order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getByIdPerfil($menu, $id)
    {
        $sql = "select * from " . self::$tablename . " where men_view = \"$menu\" and  usr_perfil = $id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getAllNodo()
    {
        $sql = "select * from " . self::$tablename . " where mnnodo=\"N\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getAllForPerfil($perfil)
    {
        $sql = "select * from " . self::$tablename . " where usr_perfil=$perfil";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuData());
    }

    public static function getByPage($page)
    {
        $sql = "select * from " . self::$tablename . " where mnpage=\"$page\" ";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuData());
    }


}
