<?php
/** @var mysqli $conexion */

require_once("../conexion.php");
header('Content-Type: application/json');

$tipo = $_GET['tipo'] ?? 'semana';

$where = "";

if($tipo == "semana"){
    $where = "WHERE YEARWEEK(fecha_plana,1)=YEARWEEK(CURDATE(),1)";
}else{
    $where = "WHERE MONTH(fecha_plana)=MONTH(CURDATE()) 
              AND YEAR(fecha_plana)=YEAR(CURDATE())";
}

/* PRODUCCION DIARIA */

$sql = "SELECT 
    DATE(fecha_plana) fecha,
    SUM(total_plana) total,
    SUM(retal_plana) retal
FROM PRODUCCION_PLANA
$where
GROUP BY DATE(fecha_plana)";

$res = mysqli_query($conexion,$sql);

$fechas = [];
$totales = [];
$retales = [];

while($row = mysqli_fetch_assoc($res)){
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
    $retales[] = $row['retal'];
}

/* OPERARIOS */

$sql2 = "SELECT o.nombre, SUM(p.bultos_plana) total
FROM PRODUCCION_PLANA p
LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
$where
GROUP BY p.id_operario
ORDER BY total DESC
LIMIT 10";

$res2 = mysqli_query($conexion,$sql2);

$operarios = [];
$bultos = [];

while($row = mysqli_fetch_assoc($res2)){
    $operarios[] = $row['nombre'];
    $bultos[] = $row['total'];
}

echo json_encode([
    "fechas"=>$fechas,
    "totales"=>$totales,
    "retales"=>$retales,
    "operarios"=>$operarios,
    "bultos_operarios"=>$bultos
]);