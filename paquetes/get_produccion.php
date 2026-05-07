<?php
require_once("../conexion.php");

$tipo = $_GET['tipo'] ?? 'semana';

/* ===== PRODUCCION POR DIA ===== */

if($tipo == "semana"){
    $sql = "SELECT DATE(fecha_paq) fecha, SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE YEARWEEK(fecha_paq,1)=YEARWEEK(CURDATE(),1)
            GROUP BY DATE(fecha_paq)";
}else{
    $sql = "SELECT DATE(fecha_paq) fecha, SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE())
            AND YEAR(fecha_paq)=YEAR(CURDATE())
            GROUP BY DATE(fecha_paq)";
}

$res = mysqli_query($conexion,$sql);

$fechas = [];
$totales = [];

while($row = mysqli_fetch_assoc($res)){
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
}

/* ===== PRODUCCION POR OPERARIO ===== */

if($tipo == "semana"){
    $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
             FROM PRODUCCION_PAQUETES p
             LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
             WHERE YEARWEEK(p.fecha_paq,1)=YEARWEEK(CURDATE(),1)
             GROUP BY p.id_operario";
}else{
    $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
             FROM PRODUCCION_PAQUETES p
             LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
             WHERE MONTH(p.fecha_paq)=MONTH(CURDATE())
             AND YEAR(p.fecha_paq)=YEAR(CURDATE())
             GROUP BY p.id_operario";
}

$res2 = mysqli_query($conexion,$sql2);

$operarios = [];
$totales_op = [];

while($row = mysqli_fetch_assoc($res2)){
    $operarios[] = $row['nombre'];
    $totales_op[] = $row['total'];
}

echo json_encode([
    "fechas"=>$fechas,
    "totales"=>$totales,
    "operarios"=>$operarios,
    "totales_operarios"=>$totales_op
]);

