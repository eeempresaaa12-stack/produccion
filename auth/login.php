<?php
// Iniciar la sesión si no hay una activa
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

// Redirigir al Index si yá inició sesión
if(isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión</title>
    <!-- Ícono y estilos -->
    <link rel="icon" type="image/png" href="../assets/img/logo-plastypetco.png">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>
    <!-- Contenedor del login -->
    <div class="login-box">

        <!-- Logo -->
        <img src="/CONTROL_PRODUCCION/assets/img/logo-plastypetco.png" alt="Logo Plastypetco">
        
        <!-- Mensaje de error -->
        <h1>Iniciar Sesión</h1>
        <?php if(isset($_GET['error'])){ ?>
        <div class="error">
            Usuario o contraseña incorrectos
        </div>
        <?php } ?>

        <!-- Formulario de autenticación -->
        <form action="validar.php" method="POST">

            <!-- Campo usuario -->
            <div class="grupo">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>

            <!-- Campo contraseña -->
            <div class="grupo">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required>
            </div>

            <!-- Botón ingresar -->
            <button class="btn" type="submit">
                Ingresar
            </button>

        </form>

    </div>
</body>
</html>