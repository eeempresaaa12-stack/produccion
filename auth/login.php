<?php
session_start();

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

    <link rel="icon" type="image/png" href="../assets/img/logo-plastypetco.png">
    <link rel="stylesheet" href="../assets/css/login.css">

</head>

<body>
    <div class="login-box">

        <img src="/CONTROL_PRODUCCION/assets/img/logo-plastypetco.png" alt="Logo Plastypetco">

        <h1>Iniciar Sesión</h1>
        <?php if(isset($_GET['error'])){ ?>
        <div class="error">
            Usuario o contraseña incorrectos
        </div>
        <?php } ?>

        <form action="validar.php" method="POST">

            <div class="grupo">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>

            <div class="grupo">
                <label>Contraseña</label>
                <input type="password" name="contrasena" required>
            </div>

            <button class="btn" type="submit">
                Ingresar
            </button>

        </form>

    </div>

</body>
</html>