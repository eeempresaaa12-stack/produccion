<?php
session_start();

/* BORRAR SESION */
session_destroy();

/* VOLVER AL LOGIN */
header("Location: login.php");
exit;