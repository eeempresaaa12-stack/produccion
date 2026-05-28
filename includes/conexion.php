<?php
// Mostrar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Variables de la base de datos
$host     = "switchyard.proxy.rlwy.net";
$usuario  = "root";
$password = "EagzBrYIJHawioQrQhpqbjYAxXFhMwUU";
$database = "CONTROL_PRODUCCION";
$puerto   = 22573;

// Conectar a la base de datos
$conexion = mysqli_connect($host, $usuario, $password, $database, $puerto);

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Establecer codificación de caracteres
mysqli_set_charset($conexion, "utf8mb4");
?>