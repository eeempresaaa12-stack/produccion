<?php
/** @var mysqli $conexion */
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

require_once("../includes/conexion.php");

/* DATOS DEL FORMULARIO */
$usuario = trim($_POST['usuario'] ?? '');
$contrasena = trim($_POST['contrasena'] ?? '');

/* VALIDAR VACIOS */
if(empty($usuario) || empty($contrasena)){
    header("Location: login.php?error=1");
    exit;
}

/* BUSCAR USUARIO */
$sql = "SELECT * FROM USUARIOS
        WHERE usuario = '$usuario'
        LIMIT 1";
$res = mysqli_query($conexion, $sql);

/* SI NO EXISTE */
IF(!$res || mysqli_num_rows($res) === 0){
    header("Location: login.php?error=1");
    exit;
}

/* OBTENER USUARIO */
$row = mysqli_fetch_assoc($res);

/* VALIDAR CONTRASEÑA */
if($contrasena != $row['contrasena']){
    header("Location: login.php?error=1");
    exit;
}

/* CREAR SESION */
$_SESSION['usuario'] = $row['usuario'];
$_SESSION['rol'] = $row['rol'];
$_SESSION['id_usuario'] = $row['id_usuario'];

/* ENTRAR */
header("Location: ../index.php");
exit;