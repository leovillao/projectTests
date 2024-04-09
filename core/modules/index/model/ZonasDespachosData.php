<?php

class ZonasDespachosData

{
    public static $Conx = "";

    public static $tablename = "cc_cliente_despacho";


    public function __construct()
    {
        $this->cdid = "";
        $this->ceid = "";
        $this->cdname = "";
        $this->cdaddress1 = "";
        $this->cdfono = "";
        $this->cdreferencia = "";
        $this->cdaddress2 = "";
        $this->pais_id = "";
        $this->prov_id = "";
        $this->city_id = "";
        $this->cdencargado = "";
        $this->cdemail = "";
        $this->user_id = "";
        $this->cdestado = "";
        $this->cdcreate_at = "NOW()";
    }

    public static function CerrarTransaccion($conx) {

        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->CloseTransaction();

    }

    public static function CancelarTransaccion($conx) {

        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->BackTransaction();

    }

    public static function AbrirTransaccion() {

        $ObjExe = new Executor();
        $ObjExe->OpenTransaction();
        self::$Conx = $ObjExe::$conx2;
        return $ObjExe::$transac;
    }

    public function add($Conx)
    {
        $sql = "INSERT INTO " . self::$tablename . "(`cdname`, `cdaddress1`,`ceid`, `cdfono`, `cdreferencia`, `cdaddress2`, `pais_id`, `prov_id`, `city_id`, `cdencargado`, `cdemail`, `user_id`, `cdestado`, `cdcreate_at`) VALUES ($this->cdname,$this->cdaddress1,$this->ceid,$this->cdfono,$this->cdreferencia,$this->cdaddress2,$this->pais_id,$this->prov_id,$this->city_id,$this->cdencargado,$this->cdemail,$this->user_id,$this->cdestado,$this->cdcreate_at)";
        return Executor::doit_T($sql,$Conx);
//        return $sql;
    }


    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where cdid=$id";
        return Executor::doit($sql);
    }


    public function update($Conx)
    {
        $sql = "update " . self::$tablename . " set ceid = $this->ceid,cdname = $this->cdname,cdaddress1 = $this->cdaddress1,cdfono = $this->cdfono,cdreferencia = $this->cdreferencia,cdaddress2 = $this->cdaddress2,pais_id = $this->pais_id,prov_id = $this->prov_id,city_id = $this->city_id,cdencargado = $this->cdencargado,cdemail = $this->cdemail,cdestado = $this->cdestado where cdid=$this->cdid";
        return Executor::doit_T($sql,$Conx);
    }


    public static function updateIsPrincipal($ceid,$Conx)
    {
        $sql = "update " . self::$tablename . " set is_principal = 0 where ceid = $ceid";
        return Executor::doit_T($sql,$Conx);
    }

    public static function updatePrincipal($cdid,$Conx)
    {
        $sql = "update " . self::$tablename . " set is_principal = 1 where cdid = $cdid";
        return Executor::doit_T($sql,$Conx);
    }


    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where cdid=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ZonasDespachosData());
    }


    public static function getByCod($cod)
    {
        $sql = "select name from " . self::$tablename . " where cod=$cod";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ZonasDespachosData());
    }


    public static function getAllId($id)
    {
        $sql = "select * from " . self::$tablename . " where ceid = $id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonasDespachosData());
    }


    public static function getLike($q)
    {
        $sql = "select * from " . self::$tablename . " where title like '%$q%' or email like '%$q%'";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ZonasDespachosData());
    }


    public static function getByCeId($id)
    {
        $sql = "select * from " . self::$tablename . " where ceid=$id and is_principal = 1";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ZonasDespachosData());
    }

}
