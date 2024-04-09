<?php

class EntidadesData {
  /* ENTIDADES DE LAS CUALES SE RECIBE EL PAGO CHEQUE DEPOSITO O TRANSFERENCIA*/
  public static $tablename = "de_entidades";

  public function __construct() {
    $this->enid = "";
    $this->ename = "";
    $this->is_active = "";
    $this->entipo = "";
    $this->created_at = "NOW()";
  }

  public function add() {
    $sql = "insert into " . self::$tablename . " (ename,is_active,entipo,created_at) ";
    $sql .= "value (\"$this->ename\",$this->is_active,$this->entipo,$this->created_at)";
    return Executor::doit($sql);
//    return $sql;
  }

  public static function delById($id) {
    $sql = "delete from " . self::$tablename . " where id=$id";
    Executor::doit($sql);
  }

  public function del() {
    $sql = "delete from " . self::$tablename . " where id=$this->id";
    Executor::doit($sql);
  }

// partiendo de que ya tenemos creado un objecto EntidadesData previamente utilizamos el contexto
  public function update() {
    $sql = "update " . self::$tablename . " set ename=\"$this->ename\" ,is_active=$this->is_active,entipo=$this->entipo where enid=$this->enid";
    return Executor::doit($sql);
  }

  public static function getById($id) {
    $sql = "select * from " . self::$tablename . " where enid=$id";
    $query = Executor::doit($sql);
    return Model::one($query[0], new EntidadesData());
  }

  public static function getByName($name) {
    $sql = "select * from " . self::$tablename . " where ename=\"$name\"";
    $query = Executor::doit($sql);
    return Model::one($query[0], new EntidadesData());
  }

  public static function getByIdCate($id) {
    $sql = "select * from " . self::$tablename . " where id = $id";
    $query = Executor::doit($sql);
    $found = null;
    $data = new EntidadesData();
    while ($r = $query[0]->fetch_array()) {
      $data->name = $r['name'];
      $data->orden = $r['orden'];
      $found = $data;
      break;
    }
    return $found;
  }

  public static function getAll() {
    $sql = "select * from " . self::$tablename;
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

  public static function getAllBancos() {
    $sql = "select * from " . self::$tablename . " where entipo = 1";
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

  public static function getAllTarjetas() {
    $sql = "select * from " . self::$tablename . " where entipo = 2";
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

  public static function getLike($q) {
    $sql = "select * from " . self::$tablename . " where name like '%$q%'";
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

  public static function getAllPagination($paginacion, $cantidadmostrar) {
    $sql = "select * from " . self::$tablename . "  LIMIT " . (($paginacion - 1) * $cantidadmostrar) . " , " . $cantidadmostrar;
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

  public static function getAllTipo(){
    $sql = "select * from " . self::$tablename . " where entipo=1";
    $query = Executor::doit($sql);
    return Model::many($query[0], new EntidadesData());
  }

}
?>