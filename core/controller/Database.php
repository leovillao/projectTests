<?php

class Database
{
    static public $db;
    static public $con;
    static public $dbase;

    public $hostConect;
    public $userConect;
    public $passConect;
    public $database;

    public function __construct()
    {
        $this->userConect = "devbiencompany_aurora";
        $this->passConect = "2bl?7[H^-m+d";
        $this->hostConect = "184.168.20.139";
        $this->database = "devbiencompany_e-signatura";
    }

    public function connect()
    {
        $con = new mysqli($this->hostConect, $this->userConect, $this->passConect, $this->database);
        $con->set_charset("utf8");
        return $con;
    }

    public static function getCon()
    {
        if (self::$con == null && self::$db == null) {
            self::$db = new Database();
            self::$con = self::$db->connect();
        }
        return self::$con;
    }

}

