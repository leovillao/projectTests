<?php

class MenuReportData {

    public static $Conx = "";

    public static $tablename = "gen_reportes";

    public function __construct() {
        $this->repid = "";
        $this->mnid = "";
        $this->repname = "";
        $this->repdescrip = "";
        $this->repactivo = "";
        $this->reppage = "";
        $this->reporden = "";
    }

    public static function getByPage($page) {
        $sql = "select * from " . self::$tablename . " where reppage = '".$page."' ";
        $query = Executor::doit($sql);
        return Model::one($query[0], new MenuReportData());
    }
}
