<?php
require_once("../conexion.php");

$anio = $_GET['anio'] ?? date('Y');

$sql = "SELECT 
            MONTH(fecha_paq) mes,
            SUM(paquetes_paq) total
        FROM PRODUCCION_PAQUETES
        WHERE YEAR(fecha_paq) = '$anio'
        GROUP BY MONTH(fecha_paq)
        ORDER BY mes";

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