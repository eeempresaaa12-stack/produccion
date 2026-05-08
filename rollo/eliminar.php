<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    mysqli_query($conexion,"DELETE FROM PRODUCCION_ROLLO WHERE id = $id");
}

header("Location: lista.php");
exit;