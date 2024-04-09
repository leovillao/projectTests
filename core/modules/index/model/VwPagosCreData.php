<?php

class VwPagosCreData
{
//    public static $Conx="";
    public static $tablename = "vw_pagosCre";

    public function __construct()
    {
        $this->puntero = "";
        $this->formaPago = "";
        $this->cliente = "";
        $this->total = "";
        $this->plazo = "";
        $this->unidadTiempo = "";
    }

    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new VwPagosCreData());
    }

    public static function getAllByIdFiles($id)
    {
        $sql = "SELECT a.* , b.cfcodSri as formaPago , em_dias as unidadTiempo FROM " . self::$tablename . " a LEFT JOIN cc_formas b on a.fpid = b.cfid join de_empresas where puntero = $id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new VwPagosCreData());
    }

    public static function getAllByIdFilesOne($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new VwPagosCreData());
    }

}

?>