<?php

class MenuViewData
{

    public static $Conx = "";

    public static $tablename = "sgn_menu_view";
    public $mnv_id;
    public $idm_id;
    public $men_id;
    public $mnv_descripcion;
    public $mnv_nombre;

    public function __construct()
    {
    }

    public static function CerrarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->CloseTransaction();
    }

    public static function CancelarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->BackTransaction();
    }

    public static function AbrirTransaccion()
    {
        $ObjExe = new Executor();
        $ObjExe->OpenTransaction();
        self::$Conx = $ObjExe::$conx2;
        return $ObjExe::$transac;
    }

    public function add_t($Conx)
    {
        $sql = "insert into " . self::$tablename . " (idm_id, men_id, mnv_descripcion, mnv_nombre) value ($this->idm_id, $this->men_id, \"$this->mnv_descripcion\", \"$this->mnv_nombre\")";
        return Executor::doit_T($sql, $Conx);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set mnv_descripcion=$this->mnv_descripcion, mnv_nombre=$this->mnv_nombre where men_id=$this->men_id and idm_id=$this->idm_id";
        return Executor::doit_T($sql, $Conx);
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getAllActive()
    {
        $sql = "select * from " . self::$tablename . " where mnactivo = 1 order by mnorden asc ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where mnid = $id and mnactivo = 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuViewData());
    }

    public static function getByOrden($id)
    {
        $sql = "select * from " . self::$tablename . " where mnorden = \"$id\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuViewData());
    }

    public static function getByIds($id)
    {
        $sql = "select * from " . self::$tablename . " where mnid = $id order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getAllN($pfid)
    {
        $sql = "select * from " . self::$tablename . " where mnnodo = 'N'  and mnactivo = 1 and mnid in (select mnid from gen_accesos where pfid = $pfid) order by orden asc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getByIdPerfil($menu, $id)
    {
        $sql = "select * from " . self::$tablename . " where men_view = \"$menu\" and  usr_perfil = $id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getAllNodo()
    {
        $sql = "select * from " . self::$tablename . " where mnnodo=\"N\"";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getAllByIdioma($idm_id)
    {
        $sql = "select a.*, b.men_nombre from " . self::$tablename . " a INNER JOIN sgn_menu b ON b.men_id = a.men_id WHERE a.idm_id=$idm_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new MenuViewData());
    }

    public static function getByIdiomaAndMenuId($idm_id, $men_id)
    {
        $sql = "select * from " . self::$tablename . " where men_id=$men_id and idm_id=$idm_id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuViewData());
    }

    public static function getByPage($page)
    {
        $sql = "select * from " . self::$tablename . " where mnpage=\"$page\" ";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuViewData());
    }

}
