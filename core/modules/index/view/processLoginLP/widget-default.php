<?php
if (isset($_POST)) {
    if (!empty($_POST['usuario'])) {
        if (!empty($_POST['password'])) {
            $usuario = $_POST['usuario'];
            $pass = sha1(md5($_POST['password']));
            $user = UserData::getFromUsers($usuario, $pass);
            if (!is_null($user)) {
                $login_error = '';
                $login = true;

                if ($user->usr_rangohorario == 'S') {
                    date_default_timezone_set('America/Guayaquil');
                    $hora = (new DateTime())->format('H:i');
                    if (!($user->usr_rangodesde <= $hora && $user->usr_rangohasta >= $hora)) {
                        $login = false;
                        print "<script>window.location='index.php';</script>";
                        $_SESSION['error'] = "Solo puede acceder en los horarios".$user->usr_rangodesde.' a '.$user->usr_rangohasta;
                    }
                }

                if ($user->usr_accesoxdia == 'S') {
                    date_default_timezone_set('America/Guayaquil');
                    $numeroDia = date('N');
                    
                    $usr_dias1_7 = $user->usr_dias1_7;
                    $condicion_dia = substr($usr_dias1_7, $numeroDia-1, 1);
                    
                    if ($condicion_dia == 'N') {
                        $acceso_dias = array();
                        $dias = array("lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo");

                        for ($i = 0; $i < strlen($usr_dias1_7); $i++) {
                            if ($usr_dias1_7[$i] === "S") {
                                $acceso_dias[] = $dias[$i];
                            }
                        }

                        $acceso_dias = implode(', ', $acceso_dias);
                        $login = false;
                        print "<script>window.location='index.php';</script>";
                        $_SESSION['error'] = "Solo puede acceder en los días: ".$acceso_dias;
                    }
                }

                if ($user->usr_controlpais == 'S') {
                    $ip_actual = $_SERVER['REMOTE_ADDR'];
                    $usr_paisespermitidos = $user->usr_paisespermitidos;

                    $response = file_get_contents("http://ipinfo.io/{$ip_actual}/json");
                    $data = json_decode($response, true);

                    $login_control_pais = false;

                    if (isset($data['country'])) {
                        $pais_codigo_actual = $data['country'];

                        $paises_id = explode(',', $usr_paisespermitidos);

                        $array_paises = array();

                        foreach ($paises_id as $pais_id) {
                            $pais = PaisData::getById($pais_id);
                            if(!is_null($pais)) {
                                $array_paises[] = $pais->pai_nombre;
                                if ($pais->pai_codigo == $pais_codigo_actual) {
                                    $login_control_pais = true;
                                    break;
                                }
                            }
                        }

                        if (!$login_control_pais) {
                            $login = false;
                            $array_paises = implode(', ', $array_paises);
                            print "<script>window.location='index.php';</script>";
                            $_SESSION['error'] = "Solo puede acceder en los países: ".$array_paises;
                        }
                    }
                    else {
                        $login = false;
                        print "<script>window.location='index.php';</script>";
                        $_SESSION['error'] = "Error al validar IP: ".$ip_actual;
                    }
                }
                
                if ($login) {
                    unset($_SESSION['error']);
                    $_SESSION['sesion'] = true;
                    $_SESSION['user_id'] = $user->usr_id;
                    $_SESSION['user_name'] = $user->usr_nombre;
                    $_SESSION['emp_id'] = $user->emp_id;
                    $_SESSION['idm_id'] = $user->idm_id;
                    $_SESSION['idm_codigo'] = $user->idm_codigo;
                    $_SESSION['razonSocial'] = $user->emp_nombre;
                    print "<script>window.location='./?view=home';</script>";
                }
                
            } else {
                print "<script>window.location='index.php';</script>";
                $_SESSION['error'] = "Usuario incorrecto";
            }
        } else {
            print "<script>window.location='index.php';</script>";
            $_SESSION['error'] = "Debe ingresar password";
        }
    } else {
        $_SESSION['error'] = "Debe ingresar usuario";
        print "<script>window.location='index.php';</script>";
    }
}else{
    $_SESSION['error'] = "Debe ingresar credendiales";
    print "<script>window.location='index.php';</script>";
}
