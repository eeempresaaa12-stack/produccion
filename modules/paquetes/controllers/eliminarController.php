<?php
/** @var mysqli $conexion */

require_once("../includes/conexion.php");

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "DELETE FROM PRODUCCION_PAQUETES WHERE id = $id";
    mysqli_query($conexion,$sql);
}

header("Location: lista.php");
exit;