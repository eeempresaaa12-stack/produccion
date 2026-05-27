<?php

session_start();

/* SI NO HAY SESION */
if(!isset($_SESSION['usuario'])){
    header("Location: /CONTROL_PRODUCCION/auth/login.php");
    exit;
}