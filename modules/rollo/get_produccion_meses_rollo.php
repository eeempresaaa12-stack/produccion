<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT 
    MONTH(fecha_roll) mes,
    SUM(total_roll) total
FROM PRODUCCION_ROLLO
WHERE YEAR(fecha_roll) = $anio
GROUP BY MONTH(fecha_roll)";

$res = mysqli_query($conexion,$sql);

$meses = [];
$totales = [];

while($row = mysqli_fetch_assoc($res)){
    $meses[] = $row['mes'];
    $totales[] = $row['total'];
}

echo json_encode([
    "meses"=>$meses,
    "totales"=>$totales
]);