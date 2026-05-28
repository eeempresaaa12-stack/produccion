<?php
// Iniciar sesión
session_start();
// Importar proteger.php
require_once("auth/proteger.php");
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
<title>Control de Producción</title>
    <!-- Ícono y estilos -->
    <link rel="icon" type="image/png" href="assets/img/logo-plastypetco.png">
    <link rel="stylesheet" href="assets/css/main.css">

</head>

<body class="index-body">
    <!-- Contenedor principal -->
    <div class="glass-container">

        <!-- Título -->
        <h1>Control de Producción</h1>

        <!-- Tarjetas de módulos — redirige según rol del usuario -->
        <div class="areas">
     
            <!-- Módulo Sellado -->
            <a href="<?php echo ($_SESSION['rol'] == 'admin')
                ? 'modules/sellado/views/dashboard.php'
                : 'modules/sellado/views/lista.php';
            ?>" class="area-card">
                <h2>Producción de Sellado</h2>
                <p>Control de paquetes producidos</p>
            </a>

            <!-- Módulo Rollos -->
            <a href="<?php
                echo ($_SESSION['rol'] == 'admin')
                    ? 'modules/rollo/views/dashboard.php'
                    : 'modules/rollo/views/lista.php';
            ?>" class="area-card">
                <h2>Producción de Rollos</h2>
                <p>Control de rollos producidos</p>
            </a>

            <!-- Módulo Máquina Plana -->
            <a href="<?php
                echo ($_SESSION['rol'] == 'admin')
                    ? 'modules/plana/views/dashboard.php'
                    : 'modules/plana/views/lista.php';
            ?>" class="area-card">
                <h2>Producción Máquina Plana</h2>
                <p>Control producción máquina plana</p>
            </a>

        </div>

    </div>

</body>
</html>


