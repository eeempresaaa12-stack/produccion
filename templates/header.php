<?php
$rol = $_SESSION['rol'] ?? 'Sin rol';
?>
<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
<title>Control Producción</title>

    <link rel="icon" type="image/png" href="/CONTROL_PRODUCCION/assets/img/logo-plastypetco.png">    
    <link rel="stylesheet" href="/CONTROL_PRODUCCION/assets/css/main.css">

</head>

<body>

<div class="navbar">
    <div class="navbar-logo">
        <a href="/CONTROL_PRODUCCION/index.php"><img src="/CONTROL_PRODUCCION/assets/img/logo-plastypetco.png" alt="Logo Plastypetco"></a>
    </div>
    
    <h1>Control Producción</h1>

    <div class="cerrar-sesion">
        <h2>Rol: <?php echo $rol ?></h2>
        <a id="btnCerrar" href="/CONTROL_PRODUCCION/auth/logout.php">Cerrar Sesión</a>
    </div>
    
</div>

<div class="contenido">

