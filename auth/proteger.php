<?php

if(session_status() === PHP_SESSION_NONE){
    session_set_cookie_params(28800);
    session_start();
}

/* SIN LOGIN */
if(!isset($_SESSION['usuario'])){
    header("Location: /CONTROL_PRODUCCION/auth/login.php");
    exit;
}

/* SOLO ADMIN */
if(isset($soloAdmin) && $soloAdmin === true){
    if($_SESSION['rol'] != 'admin'){
        header("Location: /CONTROL_PRODUCCION/index.php");
        exit;
    }
}