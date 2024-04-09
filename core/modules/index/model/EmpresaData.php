<?php

class EmpresaData
{
    public static $Conx = "";
    public static $tablename = "sgn_empresas";
    public $emp_id;
    public $emp_nombre;
    public $emp_idfiscal;
    public $emp_contacto;
    public $emp_cont_cel;
    public $emp_cont_email;
    public $pai_id;
    public $emp_sign_url;
    public $emp_firma;
    public $emp_psw;
    public $idm_id;
    public $emp_estado;

    public static function CerrarTransaccion($conx)
    {
      $ObjExe = new Executor();
      $ObjExe::$conx2 = $conx;
      return $ObjExe->CloseTransaction();
    }

    public static function CancelarTransaccion($conx)
    {
      $ObjExe = new Executor();
      $ObjExe::$conx2 = $conx;
      return $ObjExe->BackTransaction();
    }

    public static function AbrirTransaccion()
    {
      $ObjExe = new Executor();
      $ObjExe->OpenTransaction();
      self::$Conx = $ObjExe::$conx2;
      return $ObjExe::$transac;
    }

    public function add()
    {
      $sql = "insert into " . self::$tablename . " (pai_id, rip_rangoini, rip_rangofin) value ($this->pai_id, $this->rip_rangoini, $this->rip_rangofin)";
      return Executor::doit($sql);
    }

    public function add_t($Conx)
    {
      $sql = "insert into " . self::$tablename . " (pai_id, rip_rangoini, rip_rangofin) value ($this->pai_id, $this->rip_rangoini, $this->rip_rangofin)";
      return Executor::doit_T($sql, $Conx);
    }

    public static function getAll()
    {
      $sql = "select a.*, b.pai_nombre from " . self::$tablename . " a INNER JOIN sgn_pais b ON b.pai_id = a.pai_id";
      $query = Executor::doit($sql);
      return Model::many($query[0], new EmpresaData());
    }

    public static function delById($id)
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$id";
      return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
      $sql = "update " . self::$tablename . " set emp_nombre=$this->emp_nombre, emp_idfiscal=$this->emp_idfiscal, emp_contacto=$this->emp_contacto,
              emp_cont_email=$this->emp_cont_email, emp_cont_cel=$this->emp_cont_cel, pai_id=$this->pai_id,  idm_id=$this->idm_id, emp_estado=$this->emp_estado
              where emp_id=$this->emp_id";

              // echo $sql;
      return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$this->rip_id";
      return Executor::doit($sql);
    }

    public function del_t($Conx)
    {
      $sql = "delete from " . self::$tablename . " where rip_id=$this->rip_id";
      return Executor::doit($sql, $Conx);
    }

    public static function getById($id)
    {
      $sql = "select * from " . self::$tablename . " where emp_id=$id";
      $query = Executor::doit($sql);
      return Model::one($query[0], new EmpresaData());
    }
}
