<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE YEAR(fecha_paq) = $anio";

$res = mysqli_query($conexion,$sql);
$row = mysqli_fetch_assoc($res);

echo json_encode([
    "total" => $row['total'] ?? 0
]);