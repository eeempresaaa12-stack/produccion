<?php
// Iniciar la sesión si no hay una activa
if(session_status() === PHP_SESSION_NONE){
    session_set_cookie_params(28800); // Duración de la sesión: 8 horas
    session_start();
}

// Redirigir al Login si no hay sesión activa
if(!isset($_SESSION['usuario'])){
    header("Location: /CONTROL_PRODUCCION/auth/login.php");
    exit;
}

// Restringir acceso solo a administradores
if(isset($soloAdmin) && $soloAdmin === true){
    if($_SESSION['rol'] != 'admin'){
        header("Location: /CONTROL_PRODUCCION/index.php");
        exit;
    }
}