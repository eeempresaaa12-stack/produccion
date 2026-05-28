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

header('Content-Type: application/json');
echo json_encode([
    "meses"=>$meses,
    "totales"=>$totales
]);