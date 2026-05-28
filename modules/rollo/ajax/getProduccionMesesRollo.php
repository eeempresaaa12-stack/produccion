<?php
/** @var mysqli $conexion */

// Restringir acceso solo a administradores
$soloAdmin = true;
// Importar proteger.php
require_once("../../../auth/proteger.php");
// Importar conexion.php
require_once("../../../includes/conexion.php");

// Obtener filtro de año
$filtros = [
    "anio" => $_GET['anio'] ?? date('Y')
];
$anio = $filtros['anio'];

// Producción total agrupada por mes
$sql = "SELECT 
            MONTH(fecha_roll) mes,
            SUM(total_roll) total
        FROM PRODUCCION_ROLLO
        WHERE YEAR(fecha_roll) = $anio
        GROUP BY MONTH(fecha_roll)
        ORDER BY mes";
$res = mysqli_query($conexion,$sql);

// Recopilar meses y totales
$meses = [];
$totales = [];
while($row = mysqli_fetch_assoc($res)){
    $meses[] = $row['mes'];
    $totales[] = $row['total'];
}

// Devolver datos como JSON
header('Content-Type: application/json');
echo json_encode([
    "meses"=>$meses,
    "totales"=>$totales
]);