<?php
class VistaData {
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

    public function add(){
        $sql = "insert into ".self::$tablename." (name,lastname,username,password,created_at) ";
        $sql .= "value (\"$this->name\",\"$this->lastname\",\"$this->username\",\"$this->password\",$this->created_at)";
        Executor::doit($sql);
    }

    public static function delById($id){
        $sql = "delete from ".self::$tablename." where id=$id";
        Executor::doit($sql);
    }
    public function del(){
        $sql = "delete from ".self::$tablename." where id=$this->id";
        Executor::doit($sql);
    }

// partiendo de que ya tenemos creado un objecto VistaData previamente utilizamos el contexto
    public function update(){
        $sql = "update ".self::$tablename." set name=\"$this->name\",lastname=\"$this->lastname\",username=\"$this->username\",password=\"$this->password\",is_active=$this->is_active,is_admin=$this->is_admin where id=$this->id";
        Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "select * from ".self::$tablename." where id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0],new VistaData());
    }



    public static function getAll(){
        $sql = "select * from ".self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0],new VistaData());
    }


    public static function getLike($q){
        $sql = "select * from ".self::$tablename." where title like '%$q%' or content like '%$q%'";
        $query = Executor::doit($sql);
        return Model::many($query[0],new VistaData());
    }


    public static function getCD07ById($id){
        $sql = "select id_cd07RE from ".self::$tablename." where id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0],new VistaData());
    }




}

?>
