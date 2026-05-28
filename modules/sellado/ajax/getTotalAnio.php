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

// Total de paquetes producidos en el año
$sql = "SELECT SUM(paquetes_paq) total
        FROM PRODUCCION_PAQUETES
        WHERE YEAR(fecha_paq) = $anio";
$res = mysqli_query($conexion,$sql);
$row = mysqli_fetch_assoc($res);

// Devolver total como JSON
header('Content-Type: application/json');
echo json_encode([
    "total" => $row['total'] ?? 0
]);