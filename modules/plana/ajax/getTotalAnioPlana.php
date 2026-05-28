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

$sql = "SELECT SUM(total_plana) total
        FROM PRODUCCION_PLANA
        WHERE YEAR(fecha_plana) = $anio";

$res = mysqli_query($conexion, $sql);
$row = mysqli_fetch_assoc($res);

header('Content-Type: application/json');
echo json_encode([
    'total' => $row['total'] ?? 0
]);
