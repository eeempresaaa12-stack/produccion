<?php
require_once("../conexion.php");

$tipo = $_GET['tipo'] ?? 'semana';

if($tipo == "semana"){

    $sql = "SELECT 
        DATE(fecha_roll) fecha,
        SUM(total_roll) total,
        SUM(retal_roll) retal
    FROM PRODUCCION_ROLLO
    WHERE YEARWEEK(fecha_roll,1)=YEARWEEK(CURDATE(),1)
    GROUP BY DATE(fecha_roll)";

} else {

    $sql = "SELECT 
        DATE(fecha_roll) fecha,
        SUM(total_roll) total,
        SUM(retal_roll) retal
    FROM PRODUCCION_ROLLO
    WHERE MONTH(fecha_roll)=MONTH(CURDATE())
    AND YEAR(fecha_roll)=YEAR(CURDATE())
    GROUP BY DATE(fecha_roll)";
}

$res = mysqli_query($conexion,$sql);

$fechas = [];
$totales = [];
$retales = [];


$operarios = [];
$totales_operarios = [];

while($row = mysqli_fetch_assoc($res)){
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
    $retales[] = $row['retal'];
}

/* MAQUINAS */
if($tipo == "semana"){
    $sql_maquinas = "SELECT m.nombre_maquina, SUM(r.total_roll) total
    FROM PRODUCCION_ROLLO r
    LEFT JOIN MAQUINAS m ON r.id_maquina = m.id_maquina
    WHERE YEARWEEK(r.fecha_roll,1)=YEARWEEK(CURDATE(),1)
    GROUP BY r.id_maquina";
}else{
    $sql_maquinas = "SELECT m.nombre_maquina, SUM(r.total_roll) total
    FROM PRODUCCION_ROLLO r
    LEFT JOIN MAQUINAS m ON r.id_maquina = m.id_maquina
    WHERE MONTH(r.fecha_roll)=MONTH(CURDATE())
    AND YEAR(r.fecha_roll)=YEAR(CURDATE())
    GROUP BY r.id_maquina";
}

$res2 = mysqli_query($conexion,$sql_maquinas);

while($row = mysqli_fetch_assoc($res2)){
    $operarios[] = $row['nombre_maquina'];
    $totales_operarios[] = $row['total'];
}

echo json_encode([
    "fechas"=>$fechas,
    "totales"=>$totales,
    "retales"=>$retales,
    "operarios"=>$operarios,
    "totales_operarios"=>$totales_operarios
]);