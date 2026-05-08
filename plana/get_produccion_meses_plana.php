<?php
/** @var mysqli $conexion */

require_once("../conexion.php");
header('Content-Type: application/json');

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT 
    MONTH(fecha_plana) mes,
    SUM(total_plana) total
FROM PRODUCCION_PLANA
WHERE YEAR(fecha_plana) = '$anio'
GROUP BY MONTH(fecha_plana)
ORDER BY mes";

$res = mysqli_query($conexion,$sql);

$meses = [];
$totales = [];

while($row = mysqli_fetch_assoc($res)){
    $meses[] = (int)$row['mes'];
    $totales[] = (float)$row['total'];
}

echo json_encode([
    "meses"=>$meses,
    "totales"=>$totales
]);