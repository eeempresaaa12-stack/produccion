<?php
/** @var mysqli $conexion */

require_once("../conexion.php");
header('Content-Type: application/json');

$sql = "SELECT 
            r.nombre_referencia,
            SUM(p.total_plana) total,
            SUM(p.bultos_plana) bultos
        FROM PRODUCCION_PLANA p
        LEFT JOIN REFERENCIAS r ON p.id_referencia = r.id_referencia
        GROUP BY p.id_referencia
        ORDER BY total DESC
        LIMIT 10";

$res = mysqli_query($conexion,$sql);

$referencias = [];
$totales = [];
$bultos = [];

while($row = mysqli_fetch_assoc($res)){
    $referencias[] = $row['nombre_referencia'];
    $totales[] = $row['total'];
    $bultos[] = $row['bultos'];
}

echo json_encode([
    "referencias"=>$referencias,
    "totales"=>$totales,
    "bultos"=>$bultos
]);