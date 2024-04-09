<?php
if (isset($_POST)){
  $user = UserData::getById($_SESSION['user_id']);
  $pass = sha1(md5($_POST['password']));
  if ($pass == $user->password){
    $upPass = new UserData();
    $upPass->password = sha1(md5($_POST['newpassword']));
    $upPass->id = $_SESSION['user_id'];
    $up = $upPass->updatePassword();
    if ($up[0] != false){
      $msj = "1-Contraseña actualizada correctamente";
    }else{
      $msj = "0-Fallo la actualización de la contraseña";
    }
  }else{
    $msj = "0-Contraseña actual no coincide";
  }
  echo $msj;
}
?>