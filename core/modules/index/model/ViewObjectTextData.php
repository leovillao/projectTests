<?php

class ViewObjectTextData
{
//    Variabla que almacena la transaccion
    public static $Conx = "";
//    nombre de la tabla
    public static $tablename = "sgn_viewsobjectstext";
    public $vot_id;
    public $vwi_id;
    public $vwi_codigo;
    public $idm_id;
    
    public $vot_texto;

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
        $sql = "insert into " . self::$tablename . " (vwi_id, idm_id, vot_texto) value ($this->vwi_id, $this->idm_id, \"$this->vot_texto\")";
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
        $sql = "insert into " . self::$tablename . " (vwi_id, idm_id, vot_texto) value ($this->vwi_id, $this->idm_id, \"$this->vot_texto\")";

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
        $sql = "select a.*, b.men_nombre from " . self::$tablename . " a INNER JOIN sgn_menu b ON b.men_id = a.men_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ViewObjectTextData());
    }

    public static function getByIdiomaId($idm_id)
    {
        $sql = "select b.vwi_codigo, a.vot_texto from " . self::$tablename . " a INNER JOIN sgn_viewsobjects b ON b.vwi_id = a.vwi_id where a.idm_id=$idm_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new ViewObjectTextData());
    }

    public static function delById($id)
    {
        $sql = "delete from " . self::$tablename . " where ctid=$id";
        return Executor::doit($sql);
    }

    public function update()
    {
        $sql = "update " . self::$tablename . " set vot_texto=\"$this->vot_texto\" where idm_id=$this->vot_id";
        return Executor::doit($sql);
    }

    public function update_t($Conx)
    {
        $sql = "update " . self::$tablename . " set vot_texto=$this->vot_texto where vwi_id=$this->vwi_id and idm_id=$this->idm_id";
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
        return Model::one($query[0], new ViewObjectTextData());
    }

    public static function getByObjetIdAndIdiomaId($vwi_id, $idm_id)
    {
        $sql = "select * from " . self::$tablename . " where vwi_id=$vwi_id and idm_id=$idm_id";
        $query = Executor::doit($sql);
        return Model::one($query[0], new ViewObjectTextData());
    }
}
