<?php

class UserData
{
    public static $Conx = "";

    public static $tablename = "sgn_usuarios";
    public $usr_id;
    public $emp_id;
    public $emp_nombre;
    public $usr_perfil;
    public $usr_nombre;
    public $usr_user;
    public $usr_psw;
    public $usr_email;
    public $usr_numcel;
    public $usr_caducapsw;
    public $usr_periodo;
    public $usr_ultimoUpdate;
    public $usr_accesoxdia;
    public $usr_dias1_7;
    public $usr_rangohorario;
    public $usr_rangodesde;
    public $usr_rangohasta;
    public $usr_controlpais;
    public $usr_paisespermitidos;
    public $usr_id_create;
    public $usr_createat = "NOW()";
    public $usr_id_update;
    public $usr_updateat = "NOW()";
    public $usr_estado;
    public $is_oneSesion;

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
        $sql = "insert into " . self::$tablename . " (name,lastname,username,pfid,reid,veid,password,em_ruc,created_at,kind,is_admin,sucursal_id) value (\"$this->name\",\"$this->lastname\",\"$this->username\",$this->pfid,$this->reid,$this->veid,\"$this->password\",\"$this->em_ruc\",$this->created_at,$this->kind,$this->is_admin,$this->sucursal_id)";
        return Executor::doit($sql);
    }

    public function add_t($Conx)
    {
        $sql = "insert into " . self::$tablename . " (emp_id, usr_perfil, usr_nombre, usr_user, usr_psw, usr_email, usr_numcel,
            usr_caducapsw, usr_periodo, usr_ultimoUpdate, usr_accesoxdia, usr_dias1_7, usr_rangohorario, usr_rangodesde, usr_rangohasta,
            usr_controlpais, usr_paisespermitidos, usr_id_create, usr_createat, usr_id_update, usr_updateat, usr_estado) value 
            ($this->emp_id, $this->usr_perfil, $this->usr_nombre, $this->usr_user, $this->usr_psw, $this->usr_email, $this->usr_numcel,
            $this->usr_caducapsw, $this->usr_periodo, $this->usr_ultimoUpdate, $this->usr_accesoxdia, $this->usr_dias1_7, $this->usr_rangohorario,
            $this->usr_rangodesde, $this->usr_rangohasta, $this->usr_controlpais, $this->usr_paisespermitidos, $this->usr_id_create, $this->usr_createat,
            $this->usr_id_update, $this->usr_updateat, $this->usr_estado)";

        return Executor::doit_T($sql, $Conx);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set emp_id=$this->emp_id, usr_perfil=$this->usr_perfil, usr_nombre=$this->usr_nombre,
        usr_user=$this->usr_user, usr_psw=$this->usr_psw, usr_email=$this->usr_email, usr_numcel=$this->usr_numcel, usr_caducapsw=$this->usr_caducapsw,
        usr_periodo=$this->usr_periodo, usr_ultimoUpdate=$this->usr_ultimoUpdate, usr_accesoxdia=$this->usr_accesoxdia, usr_dias1_7=$this->usr_dias1_7,
        usr_rangohorario=$this->usr_rangohorario, usr_rangodesde=$this->usr_rangodesde, usr_rangohasta=$this->usr_rangohasta, usr_controlpais=$this->usr_controlpais,
        usr_paisespermitidos=$this->usr_paisespermitidos, usr_id_update=$this->usr_id_update, usr_updateat=$this->usr_updateat, usr_estado=$this->usr_estado
        where usr_id=$this->usr_id";
        return Executor::doit_T($sql, $Conx);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set name=\"$this->name\",sucursal_id=$this->sucursal_id,lastname=\"$this->lastname\",username=\"$this->username\",password=\"$this->password\",is_active=$this->is_active,is_admin=$this->is_admin,kind=$this->kind,pfid=$this->pfid,reid=$this->reid,veid=$this->veid,id_cd01FC=$this->id_cd01FC,id_cd07RE=$this->id_cd07RE,id_cd04NC=$this->id_cd04NC,id_cd05ND=$this->id_cd05ND,id_cd06GR=$this->id_cd06GR,id_cd03LC=$this->id_cd03LC where usr_id=$this->usr_id";
        return Executor::doit($sql);
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where usr_id=$id";
        Executor::doit($sql);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where usr_id=$this->usr_id";
        Executor::doit($sql);
    }

    public function del_t($Conx)
    {
        $sql = "delete from " . self::$tablename . " where usr_id=$this->usr_id";
        return Executor::doit($sql, $Conx);
    }

    public static function getByPerfil($id)
    {
        $sql = "select * from " . self::$tablename . " where pfid=$id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where usr_id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new UserData());
    }

    public function updatePassword()
    {
        $sql = "update " . self::$tablename . " set password=\"$this->password\" where usr_id=$this->id";
        return Executor::doit($sql);
    }

    public static function getAllActive()
    {
        $sql = "select * from " . self::$tablename . " where is_active = 1 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . "";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getAllByEmpId($emp_id)
    {
        $sql = "select * from " . self::$tablename . " where emp_id=$emp_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getAllActiveVen()
    {
        $sql = "select * from " . self::$tablename . " where is_active = 1 and pfid = 2 order by created_at desc";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getLike($q)
    {
        $sql = "select * from " . self::$tablename . " where title like '%$q%' or content like '%$q%'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new UserData());
    }

    public static function getCD07ById($id)
    {
        $sql = "select id_cd07RE from " . self::$tablename . " where usr_id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new UserData());
    }

    // actualizacion julio 29-07-2022
    public function update_passwd()
    {
        $sql = "update " . self::$tablename . " set password=\"$this->password\" where usr_id=$this->usr_id";
        return Executor::doit($sql);
    }

    // actualizacion julio 29-07-2022
    public function updatePassSesion()
    {
        $sql = "update " . self::$tablename . " set password=\"$this->password\" , is_oneSesion = 0 where usr_id=$this->usr_id";
        return Executor::doit($sql);
    }

    public static function getFromUsers($usuario, $password)
    {
        $sql = "select a.*, emp.emp_nombre, emp.idm_id, idm.idm_codigo from " . self::$tablename . " a
                inner join sgn_empresas emp on a.emp_id = emp.emp_id 
                inner join sgn_idiomas idm on idm.idm_id = emp.idm_id 
                where usr_user=\"$usuario\" and usr_psw = \"$password\"";
        $query = Executor::doit($sql);
        return Model::one($query[0], new UserData());
    }
}

?>
