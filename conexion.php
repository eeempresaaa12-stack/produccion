<?php

$host = "localhost";
$usuario = "root";
$password = "";
$database = "CONTROL_PRODUCCION";

$conexion = mysqli_connect($host,$usuario,$password,$database);
mysqli_set_charset($conexion,"utf8mb4");

if(!$conexion){
    die("Error de conexión");
}

?>