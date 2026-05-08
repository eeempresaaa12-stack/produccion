<?php
/** @var mysqli $conexion */

require_once("../conexion.php");
header('Content-Type: application/json');

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT SUM(total_plana) total
FROM PRODUCCION_PLANA
WHERE YEAR(fecha_plana) = '$anio'";

$res = mysqli_query($conexion,$sql);

$row = mysqli_fetch_assoc($res);

echo json_encode([
    "total" => $row['total'] ?? 0
]);