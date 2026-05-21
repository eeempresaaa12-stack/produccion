<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$tipo = $_GET['tipo'] ?? 'semana';
$mes = $_GET['mes'] ?? date('m');
$semana = $_GET['semana'] ?? '';

if($tipo === "anio"){
    $sql = "SELECT 
                CONCAT('Sem ', WEEK(fecha_plana, 1)) fecha,
                SUM(total_plana) total,
                SUM(retal_plana) retal
            FROM PRODUCCION_PLANA
            WHERE YEAR(fecha_plana)=YEAR(CURDATE())
            GROUP BY WEEK(fecha_plana, 1), CONCAT('Sem ', WEEK(fecha_plana, 1))
            ORDER BY WEEK(fecha_plana, 1) ASC";
}else{
    if($semana != ""){
        $inicio = (($semana - 1) * 7) + 1;
        $fin = $semana * 7;

        $where = "WHERE MONTH(fecha_plana) = $mes
                AND DAY(fecha_plana) BETWEEN $inicio AND $fin
                AND YEAR(fecha_plana)=YEAR(CURDATE())";
    }else{
        $where = "WHERE MONTH(fecha_plana) = $mes
                AND YEAR(fecha_plana)=YEAR(CURDATE())";
    }
    $sql = "SELECT 
                DATE(fecha_plana) fecha,
                SUM(total_plana) total,
                SUM(retal_plana) retal
            FROM PRODUCCION_PLANA
            $where
            GROUP BY DATE(fecha_plana)";
}

$res = mysqli_query($conexion,$sql);

$fechas = [];
$totales = [];
$retales = [];

while($row = mysqli_fetch_assoc($res)){
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
    $retales[] = $row['retal'];
}

if($tipo == "anio"){
    $sql2 = "SELECT 
                o.nombre,
                SUM(p.bultos_plana) total
            FROM PRODUCCION_PLANA p
            LEFT JOIN OPERARIOS o 
                ON p.id_operario=o.id_operario
            WHERE YEAR(p.fecha_plana)=YEAR(CURDATE())
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 10";
}else{
    if($semana != ""){
        $inicio = (($semana - 1) * 7) + 1;
        $fin = $semana * 7;

        $where2 = "WHERE MONTH(p.fecha_plana) = $mes
                AND DAY(p.fecha_plana) BETWEEN $inicio AND $fin
                AND YEAR(p.fecha_plana)=YEAR(CURDATE())";
    }else{
        $where2 = "WHERE MONTH(p.fecha_plana) = $mes
                AND YEAR(p.fecha_plana)=YEAR(CURDATE())";
    }
    $sql2 = "SELECT 
                o.nombre,
                SUM(p.bultos_plana) total
            FROM PRODUCCION_PLANA p
            LEFT JOIN OPERARIOS o 
                ON p.id_operario=o.id_operario
            $where2
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 10";
}

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