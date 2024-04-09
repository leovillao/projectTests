<?php

class vwVistaPagosPosData
{
    public static $tablename = "vw_cobrosPos_formas";

    public function __construct()
    {
        $this->valor = "";
        $this->cfname = "";
    }

    public static function getDatavistaBox($id)
    {
        $sql = "select cfname,valor from " . self::$tablename . " where box_id = $id ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new vwVistaPagosPosData());
    }
}

?>
