<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>E-Signatura</title>
    <link rel="stylesheet" href="css/layout/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="css/layout/feather/feather.css">
    <link rel="stylesheet" href="css/layout/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/layout/flag-icon-css/css/flag-icon.min.css"/>
    <link rel="stylesheet" href="css/layout/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/layout/style.css">
    <link rel="shortcut icon" href="images/favicon.png"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div id="root"></div>
<div class="container-scroller">
    <?php
    $view_login = (Session::getUID() == "") ? true : false;
    if (!$view_login) {
        $menu_actual = isset($_GET['view'])?$_GET['view']:'';        
        ?>
        <input type="hidden" id="emp_imd_codigo" value="<?php echo $_SESSION["idm_codigo"]?>">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="index.html">E-Signatura</a>
                <a class="navbar-brand brand-logo-mini" href="index.html"><img src="images/logo-mini.svg"
                                                                               alt="logo"/></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item dropdown mr-4 d-lg-flex d-none">
                        <a class="nav-link count-indicatord-flex align-item s-center justify-content-center" href="#">
                            <i class="icon-grid"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown d-flex mr-4">
                        <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center"
                           id="notificationDropdown" href="#" data-toggle="dropdown">
                            <i class="icon-head"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                             aria-labelledby="notificationDropdown">
                            <p class="mb-0 font-weight-normal float-left dropdown-header">Configuraciones</p>
                            <a class="dropdown-item preview-item">
                                <i class="icon-head"></i> Perfil
                            </a>
                            <a class="dropdown-item preview-item" href="logout.php">
                                <i class="icon-inbox"></i> Salir
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                        data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>
        <div class="page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <?php
                    if (isset($_SESSION["user_id"])) {
                        $u = UserData::getById(Session::getUID());
                        $perfil = UserData::getById($_SESSION['user_id'])->usr_perfil;
                        if (isset($_SESSION) && !empty($_SESSION)) {
                            $menus = MenuData::getAllForPerfil($perfil);
                            if (count($menus) > 0) {
                                foreach ($menus as $menu) {
                                    if ($menu->usr_perfil == $perfil) {
                                        if ($perfil == $menu->usr_perfil) {
                                            $menu_traduccion = MenuViewData::getByIdiomaAndMenuId($_SESSION['idm_id'], $menu->men_id);

                                            if (is_null($menu_traduccion)) {
                                                $menu_nombre = $menu->men_nombre;
                                            } else {
                                                $menu_nombre = $menu_traduccion->mnv_nombre;
                                            }
                                            
                                            $menu_active = ($menu->men_view == $menu_actual)?'active':'';
                                            
                                            echo '<li class="nav-item '. $menu_active .'"><a class="nav-link" title="' . $menu->men_nombre . '" href="./?view=' . $menu->men_view . '"><i class="icon-box menu-icon"></i><span class="menu-title">' . $menu_nombre . '</span></a>';
                                        }
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </ul>
            </nav>
            <div class="main-panel">
                <div class="content-wrapper">
                    <?php
                    View::load("login");
                    ?>
                </div>
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © BienCompany 2023</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center"></span>
                    </div>
                </footer>
            </div>
        </div>
        <?php
    } else {
        View::load("login");
    }
    ?>
</div>
</div>

<script src="js/vendor.bundle.base.js"></script>
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

<script crossorigin src="https://unpkg.com/react@16/umd/react.development.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@16/umd/react-dom.development.js"></script>
<script src="js/react/main.js?v=20230826"></script>
</body>
</html>