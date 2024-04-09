<?php

class Lb
{
    public $get;
    public $post;
    public $request;
    public $cookie;
    public $session;

    public function __construct()
    {
    }

    public function loadModule($module)
    {
        if (!isset($_GET['module'])) {
            Module::setModule($module);
            include "core/modules/" . $module . "/autoload.php"; // carga el path de los modelos
            include "core/modules/" . $module . "/superboot.php";
            include "core/modules/" . $module . "/init.php";
        } else {
            Module::setModule($_GET['module']);
            if (Module::isValid()) {
                include "core/modules/" . $_GET['module'] . "/init.php";
            } else {
                Module::Error();
            }
        }
    }
}

?>