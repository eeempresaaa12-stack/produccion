<?php
/** @var mysqli $conexion */

// Restringir acceso solo a administradores
$soloAdmin = true;
// Importar proteger.php
require_once("../proteger.php");
// Importar conexion.php
require_once("../../includes/conexion.php");

// Obtener y limpiar datos del formulario
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');
$rol = trim($_POST['rol'] ?? '');

// Validar campos vacíos
if(
    empty($usuario) ||
    empty($contrasena) ||
    empty($rol)
){
    header("Location: ../views/crearUsuario.php");
    exit;
}

// Sanear datos antes de insertar
$usuario = mysqli_real_escape_string($conexion, $usuario);
$contrasena = mysqli_real_escape_string($conexion, $contrasena);
$rol = mysqli_real_escape_string($conexion, $rol);

// Insertar nuevo usuario
$sql = "INSERT INTO USUARIOS
            (usuario,
            contrasena,
            rol)
        VALUES
            ('$usuario',
            '$contrasena',
            '$rol')";

mysqli_query($conexion, $sql);

// Redirigir al Usuarios
header("Location: ../views/usuarios.php");
exit;