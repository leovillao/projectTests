<?php

class vWFormasCobrosDetalleData
{
//    public static $Conx="";
    public static $tablename = "vw_formasPagoDetalles";

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
        return Model::one($query[0], new vWFormasCobrosDetalleData());
    }

    public static function getAllByIdFiles($id)
    {
        $sql = "select * from " . self::$tablename . " where puntero=$id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new vWFormasCobrosDetalleData());
    }

    public static function getAllFechas($where)
    {
        $sql = "select * from " . self::$tablename . " $where  ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new vWFormasCobrosDetalleData());
//        return $sql;
    }

}
?>
