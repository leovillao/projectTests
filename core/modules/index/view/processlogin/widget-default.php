<?php
// define('LBROOT',getcwd()); // LegoBox Root ... the server root
// include("core/controller/Database.php");
/*error_reporting(0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);*/
// ini_set('session.save_path', '/tmp/session/ea-php56/');
if (isset($_POST['ruc']) && !empty($_POST['ruc'])) {

  $hostname = 'localhost';
  $database = 'smarttag_admin';
  $username = 'smarttag_administrador';
  $password = '#qJ{-){u&Wf1';
  $con = mysqli_connect($hostname, $username, $password, $database);
  if (!$con) {
    die("Falló la conexión a MySQL: " . mysqli_error());
  }
  $select = "select * from business where ruc = '" . $_POST['ruc'] . "' and is_active = 1 and is_test = 0";
  $result = mysqli_query($con, $select);
  $tot = mysqli_num_rows($result);
  if ($tot != 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $_SESSION['base'] = $row['db_datos'];
      $_SESSION['server'] = $row['server'];
      $_SESSION['userhost'] = $row['userhost'];
      $_SESSION['passhost'] = $row['passhot'];
      $ruc_em = $row['ruc'];
    }
    if (Session::getUID() == "") {
      $user = $_POST['mail'];
      $pass = sha1(md5($_POST['password']));
      $base = new Database();
      $con = $base->connect($_SESSION['base'],$_SESSION['server'],$_SESSION['userhost'],$_SESSION['passhost']);
      $sql = "select * from user where (username= \"" . $user . "\") and password= \"" . $pass . "\" and is_active=1";
      $query = $con->query($sql);
      $found = false;
      $userid = '';
      while ($r = $query->fetch_array()) {
        $found = true;
        $userid = $r['id'];
        $name = $r['name'];
        $ruc = $r['em_ruc'];
        $sucursal = $r['sucursal_id'];
      }
      if ($found == true) {
        if ($ruc_em == $ruc) {
          session_start();
          $logoFooter = EmpresasData::getByRuc($ruc);
          $_SESSION['logoFooter'] = $logoFooter->em_logo_footer_fact;
          $_SESSION['user_id'] = $userid;
          $_SESSION['razonSocial'] = $logoFooter->em_nombre;
          $_SESSION['ruc'] = $ruc;
          $_SESSION['sucursal'] = $sucursal;
          print "Cargando ... $name";
          print "<script>window.location='./?view=home';</script>";
        } else {
          print "<script>window.location='./?view=login';</script>";
          unset($_SESSION['base']);
        }
      } else {
        print "<script>window.location='./?view=login';</script>";
      }
      echo '1';
    } else {
      print "<script>window.location='./?view=home';</script>";
    }
  } else {
    print "<script>window.location='./?view=login';</script>";
  }
} else {
  print "<script>window.location='./?view=login';</script>";
}
