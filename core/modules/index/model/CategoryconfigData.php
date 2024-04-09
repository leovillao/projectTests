<?php

class CategoryconfigData {
  public static $tablename = "de_confi_catg";

  public function __construct() {
    $this->ccid = ""; // Dato standar
    $this->ccname = "";  // Dato standar
    $this->ccestado = "";  // Dato standar
    $this->created_at = "NOW()";  // Dato standar
  }

  public static function getAll() {
    $sql = "select * from " . self::$tablename . " ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new CategoryconfigData());
  }

  public static function getAllActive() {
    $sql = "select * from " . self::$tablename . " where ccestado = 1 ";
    $query = Executor::doit($sql);
    return Model::many($query[0], new CategoryconfigData());
  }


}