<?php
/** @var mysqli $conexion */

$soloAdmin = true;
require_once("../../../auth/proteger.php");
require_once("../../../includes/conexion.php");

/* FILTRO */
$filtros = [
    "anio" => $_GET['anio'] ?? date('Y')
];
$anio = $filtros['anio'];

$sql = "SELECT 
            MONTH(fecha_plana) mes, 
            SUM(total_plana) totales
        FROM PRODUCCION_PLANA
        WHERE YEAR(fecha_plana) = $anio
        GROUP BY MONTH(fecha_plana)
        ORDER BY MONTH(fecha_plana) ASC";

$res = mysqli_query($conexion, $sql);

$meses = [];
$totales = [];

while($row = mysqli_fetch_assoc($res)){
    $meses[] = $row['mes'];
    $totales[] = $row['totales'];
}

header('Content-Type: application/json');
echo json_encode([
    'meses' => $meses,
    'totales' => $totales
]);
