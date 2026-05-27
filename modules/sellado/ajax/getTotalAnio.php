<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

/* FILTRO */
$filtros = [
    "anio" => $_GET['anio'] ?? date('Y')
];
$anio = $filtros['anio'];

$sql = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE YEAR(fecha_paq) = $anio";

$res = mysqli_query($conexion,$sql);
$row = mysqli_fetch_assoc($res);

header('Content-Type: application/json');
echo json_encode([
    "total" => $row['total'] ?? 0
]);