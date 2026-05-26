<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "DELETE FROM PRODUCCION_ROLLO WHERE id = $id";
    mysqli_query($conexion, $sql);
}

header("Location: ../views/lista.php");
exit;
