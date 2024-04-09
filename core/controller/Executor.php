<?php

class Executor
{

    public static $transac;
    public static $conx2;
    public $Transaccion = true;
    public $conx = '';

    public function __construct()
    {

    }

    public static function doit($sql)
    {
        $con = Database::getCon();
        return array($con->query($sql), $con->insert_id, $con->error);
    }

    public static function doit_Procedure($stock)
    {
        $con = Database::getCon();
        return array($con->query($stock), $con->insert_id, $con->error);
    }

    public static function OpenTransaction()
    {
        $con = Database::getCon();
        $con->autocommit(false);
        self::$transac = $con->begin_Transaction();
        self::$conx2 = $con;
        //  mysqli_set_charset($sql, "utf8");
        return;
    }

    public static function CloseTransaction()
    {
        // $con = Database::getCon($_SESSION['base']);
        $conx3 = self::$conx2;
        // $conx3->commit();
        //  self::$transac = $con->begin_Transaction();
        //  self::$conx2 = $con;
        //  mysqli_set_charset($sql, "utf8");
        return $conx3->commit();
    }

    public static function BackTransaction()
    {
        // $con = Database::getCon($_SESSION['base']);
        $conx3 = self::$conx2;
        // $conx3->commit();
        //  self::$transac = $con->begin_Transaction();
        //  self::$conx2 = $con;
        //  mysqli_set_charset($sql, "utf8");
        return $conx3->rollback();
    }

    public static function doit_T($sql, $con)
    {
        //$con = Database::getCon($_SESSION['base']);
        //  mysqli_set_charset($sql, "utf8");
        return array($con->query($sql), $con->insert_id, $con->error);
    }
}

?>