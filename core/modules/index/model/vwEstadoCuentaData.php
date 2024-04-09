<?php
class vwEstadoCuentaData {
    public static $tablename = "vw_cxc_estcta";

    public function __construct(){
        $this->categoria = "";
        $this->factor = "";
        $this->suid = "";
        $this->tipoid = "";
        $this->iddeuda = "";
        $this->idcobro = "";
        $this->fecha = "";
        $this->ceid = "";
        $this->decuota = "";
        $this->fi_id = "";
        $this->derefer = "";
        $this->valor = "";
        $this->tipodeuda = "";
        $this->tipocobro = "";
        $this->observa = "";
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

// partiendo de que ya tenemos creado un objecto vwEstadoCuentaData previamente utilizamos el contexto
    public function update(){
        $sql = "update ".self::$tablename." set name=\"$this->name\",lastname=\"$this->lastname\",username=\"$this->username\",password=\"$this->password\",is_active=$this->is_active,is_admin=$this->is_admin where id=$this->id";
        Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "select * from ".self::$tablename." where id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0],new vwEstadoCuentaData());
    }

    public static function getAllFecha($where){
        $sql = "select * from ".self::$tablename . " $where";
        $query = Executor::doit($sql);
        return Model::many($query[0],new vwEstadoCuentaData());
//        return $sql;
    }

    public static function getLike($q){
        $sql = "select * from ".self::$tablename." where title like '%$q%' or content like '%$q%'";
        $query = Executor::doit($sql);
        return Model::many($query[0],new vwEstadoCuentaData());
    }
}
?>
