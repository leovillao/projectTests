<?php
//$this->userConect = "root";
//$this->passConect = "";
//$this->hostConect = "localhost";
//$this->database = "e-signatura";
$servidor = "localhost";
$usuario = "root";
$password = "";
try {
    $pdo = new PDO("mysql:host=$servidor;dbname=e-signatura", $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "0-" . $e->getMessage();
}