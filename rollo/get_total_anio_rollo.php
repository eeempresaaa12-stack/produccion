<?php
require_once("../conexion.php");

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT SUM(total_roll) total
FROM PRODUCCION_ROLLO
WHERE YEAR(fecha_roll) = $anio";

$res = mysqli_query($conexion,$sql);
$row = mysqli_fetch_assoc($res);

echo json_encode([
    "total"=>$row['total'] ?? 0
]);