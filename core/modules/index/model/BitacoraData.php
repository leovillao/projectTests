<?php

class BitacoraData
{
//    Varaible que almacena la transaccion
    public static $Conx = "";
//  Nombre de la tabla
    public static $tablename = "sgn_bitacora";
    public $biid;
    public $user_id;
    public $biaccion;
    public $bicreate_at = "NOW()";
    public $bipage;
    public $biciclo;

    public function __construct()
    {
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

    /*
    desc : Agrega un registro dentro de la tabla de bitacora.
    param : recibe la variable de transaccion sql, y las propiedades del modelo para realizar la insercion.
    return : devuelve variable boleano con esta de trasnaccion , verdadero o falso
    desarrollado : Leonardo Villao
    Fecha : 6/7/2023
    */
    public function add_t($Conx)
    {
        $sql = "insert into " . self::$tablename . " (user_id, biaccion, bicreate_at, bipage, biciclo) ";
        $sql .= "value ($this->user_id,$this->biaccion,$this->bicreate_at,$this->bipage,$this->biciclo)";
       return Executor::doit_T($sql, $Conx);
        // return $sql;
    }

    public function add()
    {
        $sql = "insert into " . self::$tablename . " (user_id, biaccion, bicreate_at, bipage, biciclo) ";
        $sql .= "value ($this->user_id,\"$this->biaccion\",$this->bicreate_at,\"$this->bipage\",$this->biciclo)";
        return Executor::doit_T($sql);
    }

    public static function getAll()
    {
        $sql = "select * from " . self::$tablename . " ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BitacoraData());
    }

    public static function getAllPaginasUser($pagina, $usuario)
    {
        $sql = "select * from " . self::$tablename . " where bipage = '" . $pagina . "' and user_id = " . $usuario;
        $query = Executor::doit($sql);
        return Model::many($query[0], new BitacoraData());
    }

    public static function getAllForWhere($where)
    {
        $sql = "select * from " . self::$tablename . " $where ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BitacoraData());
    }

    public static function getByUsuarioId($user_id)
    {
        $sql = "select * from " . self::$tablename . " where user_id=$user_id ";
        $query = Executor::doit($sql);
        return Model::many($query[0], new BitacoraData());
    }
}