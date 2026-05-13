<?php

$host     = "mysql.railway.internal";
$usuario  = "root";
$password = "EagzBrYIJHawioQrQhpqbjYAxXFhMwUU";
$database = "CONTROL_PRODUCCION";
$puerto   = 3306;

$conexion = mysqli_connect($host, $usuario, $password, $database, $puerto);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");

?>