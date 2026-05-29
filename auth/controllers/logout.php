<?php
// Iniciar la sesión si no hay una activa
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Borrar la sesión
session_destroy();

// Redirigir al Login
header("Location: /CONTROL_PRODUCCION/auth/views/login.php");
exit;