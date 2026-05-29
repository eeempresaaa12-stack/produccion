<?php
/** @var mysqli $conexion */
// Iniciar la sesión si no hay una activa
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Importar conexion.php
require_once("../../includes/conexion.php");

// Datos del formulario
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

// Validar campos vacíos
if(empty($usuario) || empty($contrasena)){
    header("Location: /CONTROL_PRODUCCION/auth/views/login.php?error=1");
    exit;
}

// Buscar usuario en la base de datos
$sql = "SELECT * FROM USUARIOS
        WHERE usuario = '$usuario'
        LIMIT 1";
$res = mysqli_query($conexion, $sql);

// Redirigir al Login si el usuario no existe
IF(!$res || mysqli_num_rows($res) === 0){
    header("Location: /CONTROL_PRODUCCION/auth/views/login.php?error=1");
    exit;
}

// Obtener datos del usuario
$row = mysqli_fetch_assoc($res);

// Validar la contraseña
if($contrasena != $row['contrasena']){
    header("Location: /CONTROL_PRODUCCION/auth/views/login.php?error=1");
    exit;
}

// Crear sesión con datos del usuario
$_SESSION['usuario'] = $row['usuario'];
$_SESSION['rol'] = $row['rol'];
$_SESSION['id_usuario'] = $row['id_usuario'];

// Redirigir al Index
header("Location: /CONTROL_PRODUCCION/index.php");
exit;