<?php
if (isset($_POST['usuario']) && !empty($_POST['usuario'])) {
    $usuario = $_POST['usuario'];
    $pass = sha1(md5($_POST['password']));
    $user = UserData::getFromUsers($usuario, $pass);
//    var_dump($user);
    if (!is_null($user)) {

        /**$found = true;
         * $userid = $r['id'];
         * $name = $r['name'];
         * $ruc = $r['em_ruc'];
         * $sucursal = $r['sucursal_id'];*/
        session_start();
//            $logoFooter = EmpresasData::getByRuc($ruc);
//            $_SESSION['logoFooter'] = $logoFooter->em_logo_footer_fact;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['razonSocial'] = $user->em_razon;
        $_SESSION['ruc'] = $user->em_ruc;
        $_SESSION['sucursal'] = $user->sucursal_id;
//            print "Bienv ... $name";
//            print "<script>window.location='index.php?view=home';</script>";
//        echo "vista de home";
        Module::loadLayout();

    } else {
        echo "vista de home2";

//        print "<script>window.location='index.php?view=home';</script>";
    }

} else {
    echo "vista de login1";

//    print "<script>window.location='index.php?view=login';</script>";
}