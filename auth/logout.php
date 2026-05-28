<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

/* BORRAR SESION */
session_destroy();

/* VOLVER AL LOGIN */
header("Location: login.php");
exit;