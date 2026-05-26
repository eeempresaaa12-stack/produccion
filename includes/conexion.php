<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host     = "switchyard.proxy.rlwy.net";
$usuario  = "root";
$password = "EagzBrYIJHawioQrQhpqbjYAxXFhMwUU";
$database = "CONTROL_PRODUCCION";
$puerto   = 22573;

$conexion = mysqli_connect($host, $usuario, $password, $database, $puerto);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

?>