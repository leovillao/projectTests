<?php
class VwinfventasData {
  public static $tablename = "vw_infoDocum_v";

  public function __construct(){
    $this->fi_docum = "";
    $this->fi_codestab = "";
    $this->fi_ptoemi = "";
    $this->fi_fechadoc = "";
    $this->fi_er_name = "";
    $this->fi_er_comercial = "";
    $this->box_id = "";
    $this->sucursal_id = "";
    $this->veid = "";
    $this->fi_subtotal = "";
    $this->fi_ivasi = "";
    $this->fi_ivano = "";
    $this->fi_desc = "";
    $this->fi_iva = "";
    $this->fi_neto = "";
    $this->fi_estado = "";
    $this->city_id = "";
    $this->prov_id = "";
    $this->pais_id = "";
    $this->pais = "";
    $this->ciudad = "";
    $this->provincia = "";
  }

  public static function getAll(){
    $sql = "select * from ".self::$tablename." ";
    $query = Executor::doit($sql);
    return Model::many($query[0],new VwinfventasData());
  }

 public static function getDataDefault($where){
    $sql = "SELECT * FROM ".self::$tablename." $where ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new VwinfventasData());
//     return $sql;
  }

 public static function getDataDefaultAnulados($where){
    $sql = "SELECT * FROM ".self::$tablename." $where ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new VwinfventasData());
  }

 public static function getDataDefaultNoAnulados($where){
    $sql = "SELECT * FROM ".self::$tablename." $where  ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new VwinfventasData());
  }
}

?>
