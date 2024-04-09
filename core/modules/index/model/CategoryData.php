<?php

class CategoryData
{
//    Variabla que almacena la transaccion
    public static $Conx = "";
//    nombre de la tabla
    public static $tablename = "sgn_categorias";
    public $cat_id;
    public $emp_id;
    public $cat_nombre;
    public $cat_createat = "NOW()";

//  propiedades de la base de datos / modelo
    public function __construct()
    {
//        $this->cat_id = "";
//        $this->cat_nombre = "";
//        $this->cat_createat = "NOW()";
    }

    /*
    desc : Cierra la transaccion sql y ejecuta la consulta sobre la tabla / base de datos.
    param : Recibe la variable de transaccion.
    return : devuelve variable boleano con esta de trasnaccion , verdadero o falso
    desarrollado : Leonardo Villao
    Fecha : 6/7/2023
    */
    public static function CerrarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->CloseTransaction();
    }

    /*
    desc : Cancela la transaccion sql y no permite la ejecucion de la consulta sobre la tabla / base de datos(ROLLBACK).
    param : Recibe la variable de transaccion.
    return : devuelve variable boleano con esta de trasnaccion , verdadero o falso
    desarrollado : Leonardo Villao
    Fecha : 6/7/2023
    */
    public static function CancelarTransaccion($conx)
    {
        $ObjExe = new Executor();
        $ObjExe::$conx2 = $conx;
        // $ObjExe->CerrarTransaction();
        return $ObjExe->BackTransaction();
    }

    /*
    desc : Abre la transaccion sql para ejecutar las consultas sobre la base de datos.
    param : no recibe parametro.
    return : devuelve variable boleano con esta de trasnaccion , verdadero o falso
    desarrollado : Leonardo Villao
    Fecha : 6/7/2023
    */
    public static function AbrirTransaccion()
    {
        $ObjExe = new Executor();
        $ObjExe->OpenTransaction();
        self::$Conx = $ObjExe::$conx2;
        return $ObjExe::$transac;
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (cat_nombre, emp_id, cat_createat) value ($this->cat_nombre, $this->emp_id, $this->cat_createat)";
        return Executor::doit($sql);
    }

    /*
    desc : Realiza la grabacion del registro en la tabla , con transaccion sobre la base de datos.
    param : recibe la variable de conexion para ejecutar la consulta
    return : estado de la transaccion , verdadero o falso.
    desarrollado : Leonardo Villao
    Fecha : 7/7/2023
    */
    public function add_t($Conx)
    {
        $sql = "insert into " . self::$tablename . " (cat_nombre, emp_id, cat_createat) value ($this->cat_nombre, $this->emp_id, $this->cat_createat)";
        return Executor::doit_T($sql, $Conx);
    }

    /*
    desc : Realiza consulta sobre la tabla / bd , para mostrar todos los registros.
    param : no recibe parametro
    return : devuelve todos los registros de la tabla.
    desarrollado : Leonardo Villao
    Fecha : 7/7/2023
    */
    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new CategoryData());
    }

    public static function getAllByEmpId($emp_id)
    {
        $sql = "select * from " . self::$tablename . " where emp_id=$emp_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new CategoryData());
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where ctid=$id";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set cat_nombre=\"$this->cat_nombre\" where cat_id=$this->cat_id";
        return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set cat_nombre=$this->cat_nombre where cat_id=$this->cat_id";
        return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where cat_id=$this->cat_id";
        return Executor::doit($sql);
    }

    /*
    desc : Realiza consulta sobre la tabla / bd , para eliminar un registro.
    param : Recibe la variable de la transaccion.
    return : devuelve esta de la consulta , verdadero o falso.
    desarrollado : Leonardo Villao
    Fecha : 7/7/2023
    */
    public function del_t($Conx)
    {
        $sql = "delete from " . self::$tablename . " where cat_id=$this->cat_id";
        return Executor::doit($sql, $Conx);
    }

    /*
    desc : Realiza consulta sobre la tabla / bd , para llamar un registro por el id.
    param : Recibe el id del registro de la fila a mostrar / modificar.
    return : devuelve esta de la consulta , verdadero o falso.
    desarrollado : Leonardo Villao
    Fecha : 7/7/2023
    */
    public static function getById($id)
    {
        $sql = "select * from " . self::$tablename . " where cat_id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new CategoryData());
    }

    public static function getByCod($cod)
    {
        $sql = "select name from " . self::$tablename . " where cod=$cod";
        $query = Executor::doit($sql);
        return Model::one($query[0], new CategoryData());
    }

    public static function getAllCatSCat()
    {
        $sql = "SELECT a.ctid,a.ctname,b.ct2id,b.ct2name,b.ct_id FROM " . self::$tablename . " a left join in_category2 b on a.ctid = b.ct_id WHERE 1";
        $query = Executor::doit($sql);
        return Model::many($query[0], new CategoryData());
    }

    public static function getAllCategory()
    {
        $sql = "SELECT a.ctid,a.ctname , (select count(ct2id) from in_category2 b where b.ct_id = a.ctid) as totalsubcat from " . self::$tablename . " a";
        $query = Executor::doit($sql);
        return Model::many($query[0], new CategoryData());
    }
}
