<?php
require_once "Database.php";
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$claveTemporal = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
$find = false;
$usuario = $_GET['usuario'];
// valida la existencia del usuario de acuerdo al ruc recibido desde el formulario de recuperacion de contrase;a
$sql = "SELECT * FROM sgn_usuarios where usr_user = '" . $usuario . "' ";
$sentencia = $pdo->prepare($sql);
$sentencia->execute();

if ($sentencia->rowCount() > 0) {
    while ($fila = $sentencia->fetch()) {
        $email = $fila['usr_email'];
        $name = $fila['usr_nombre'];
//        $lastname = $fila['lastname'];
    }
    $sqlUpdate = "UPDATE sgn_usuarios set usr_psw = '" . sha1(md5($claveTemporal)) . "' , is_oneSesion = 1 WHERE usr_user = '" . $usuario . "' ";
    $sentUpdate = $pdo->prepare($sqlUpdate);
    $sentUpdate->execute();

} else {
    $find = true;
    echo json_encode('0-Usuario incorrecto');
}

if ($find == false) {

    $mail = new PHPMailer(true);
    try {
        //Server settings
//    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
//    $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = 'pro.turbo-smtp.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = 'inventario@halconstock.com';                     //SMTP username
        $mail->Password = 'A44e5hCu';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('inventario@halconstock.com', 'E-signatura - Toma de Inventario Fisico');
        $mail->addAddress($email, $name);     //Add a recipient
//    $mail->addAddress('ellen@example.com');               //Name is optional
//    $mail->addReplyTo('info@example.com', 'Information');
//    $mail->addCC('cc@example.com');
//    $mail->addBCC('bcc@example.com');
        //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
//    $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Recuperacion de contrasena / plataforma E-signatura';
        $mail->Body = 'Su clave temporal para el ingreso a la plataforma de E-signatura , es :  <b>' . $claveTemporal . '</b>';
//        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo json_encode('1-Mensaje de recuperacion enviado al correo ' . $email . ' correctamente');
    } catch (Exception $e) {
        echo json_encode("0-Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}