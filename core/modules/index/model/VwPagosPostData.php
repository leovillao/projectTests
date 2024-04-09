<?php

class VwPagosPostData
{
//    public static $Conx="";
    public static $tablename = "vw_pagos_pos";

    public function __construct()
    {
        $this->puntero = "";
        $this->formaPago = "";
        $this->total = "";
        $this->plazo = "";
        $this->unidadTiempo = "";
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new VwPagosPostData());
    }

    public static function getAllByIdFiles($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new VwPagosPostData());
    }

    public static function getAllByIdFilesOne($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new VwPagosPostData());
    }

}
?>
