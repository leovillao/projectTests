<?php

class IdiomaData
{
//    Variabla que almacena la transaccion
    public static $Conx = "";
//    nombre de la tabla
    public static $tablename = "sgn_idiomas";
    public $idm_id;
    public $idm_nombre;
    public $idm_codigo;
    public $idm_estado;

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
        $sql = "insert into " . self::$tablename . " (idm_nombre, idm_codigo, idm_estado) value ($this->idm_nombre, $this->idm_codigo, $this->idm_estado)";
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
        $sql = "insert into " . self::$tablename . " (idm_nombre, idm_codigo, idm_estado) value ($this->idm_nombre, $this->idm_codigo, $this->idm_estado)";
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
        return Model::many($query[0], new IdiomaData());
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where ctid=$id";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set idm_nombre=\"$this->idm_nombre\" where idm_id=$this->idm_id";
        return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set idm_nombre=$this->idm_nombre, idm_codigo=$this->idm_codigo, idm_estado=$this->idm_estado where idm_id=$this->idm_id";
        return Executor::doit_T($sql, $Conx);
    }

    public function del()
    {
        $sql = "delete from " . self::$tablename . " where idm_id=$this->idm_id";
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
        $sql = "delete from " . self::$tablename . " where idm_id=$this->idm_id";
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
        $sql = "select * from " . self::$tablename . " where idm_id=$id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new IdiomaData());
    }
}
