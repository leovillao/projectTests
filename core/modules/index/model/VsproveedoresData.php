<?php
class VsproveedoresData {
    public static $tablename = "vsproveedores";

    public function __construct(){
        $this->name = "";
        $this->lastname = "";
        $this->username = "";
        $this->password = "";
        $this->em_ruc = "";
        $this->is_active = "0";
        $this->created_at = "NOW()";
    }

    public static function getAll(){
        $sql = "select * from ".self::$tablename." ";
        $query = Executor::doit($sql);
        return Model::many($query[0],new VsproveedoresData());
    }
}

?>
