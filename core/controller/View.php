<?php

class View
{
    /**
     * @function load
     * @brief la funcion load carga una vista correspondiente a un modulo
     **/
    public static function load($view)
    {

        if (!isset($_GET['view'])) {
            include "core/modules/" . Module::$module . "/view/" . $view . "/widget-default.php";
        } else {
            if (View::isValid()) {
                if (self::validaSesion()) {
                    $user = UserData::getById($_SESSION['user_id'])->usr_perfil;
                    $menu = MenuData::getByIdPerfil($_GET['view'],$user);
                    if (count($menu) > 0 || $_GET['view'] == "home") {
                        include "core/modules/" . Module::$module . "/view/" . $_GET['view'] . "/widget-default.php";
                    }else{
                        include "core/modules/" . Module::$module . "/view/404/widget-default.php";
                    }
                }
                else {
                    if ($_GET['view'] == "processLoginLP") {
                        include "core/modules/" . Module::$module . "/view/" . $_GET['view'] . "/widget-default.php";
                    }else{
                        self::login("login");
                    }
                }
            } else {
                View::Error("<b>404 NOT FOUND</b> View <b>" . $_GET['view'] . "</b> folder  !!");
            }
        }
    }
//    public static function getViewNameAccess($vista,$perfil){
//        $menus = MenuData::getAll();
//        foreach ($menus as $menu){
//            $array[] = $menu;
//        }
//        switch ($perfil){
//            case 1:
//                array_search($array,$vista);
//                break;
//            case 2:
//                break;
//            case 3:
//                break;
//            default:
//                break;
//        }
//    }

    public static function login($login)
    {
        if (!isset($_GET['view'])) {
            include "core/modules/" . Module::$module . "/view/" . $login . "/widget-default.php";
        }
    }

    /**
     * @function isValid
     * @brief valida la existencia de una vista
     **/
    public static function isValid()
    {
        $valid = false;
        if (isset($_GET["view"])) {
            if (file_exists($file = "core/modules/" . Module::$module . "/view/" . $_GET['view'] . "/widget-default.php")) {
                $valid = true;
            }
        }
        return $valid;
    }

    public static function Error($message)
    {
        print $message;
    }

    public static function validaSesion()
    {
        return isset($_SESSION['user_id']);
    }
}


?>