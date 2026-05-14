<?php
$host      = getenv('DB_HOST');
$usuario   = getenv('DB_USER');
$password  = getenv('DB_PASS');
$database  = getenv('DB_NAME');
$puerto    = getenv('DB_PORT');

$conexion = mysqli_connect($host, $usuario, $password, $database, $puerto);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>