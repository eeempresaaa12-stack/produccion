<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$id = intval($_GET['id']);

$sql = "DELETE FROM PRODUCCION_PLANA WHERE id=$id";

mysqli_query($conexion,$sql);

header("Location: lista.php");
exit;

?>