<?php
/** @var mysqli $conexion */

$soloAdmin = true;
require_once("../../../auth/proteger.php");
require_once("../../../includes/conexion.php");

/* FILTROS */
$filtros = [
    "tipo" => $_GET['tipo'] ?? 'mes',
    "mes" => $_GET['mes'] ?? date('m'),
    "semana" => $_GET['semana'] ?? ''
];
$tipo = $filtros['tipo'];
$mes = $filtros['mes'];
$semana = $filtros['semana'];

if($tipo == "semana"){
    if($semana == ""){
        $sql = "SELECT DATE(fecha_paq) fecha, SUM(paquetes_paq) total
                FROM PRODUCCION_PAQUETES
                WHERE MONTH(fecha_paq) = $mes
                AND YEAR(fecha_paq) = YEAR(CURDATE())
                GROUP BY DATE(fecha_paq)";
    } else {
        $inicio = (($semana - 1) * 7) + 1;
        $fin = $semana * 7;

        $sql = "SELECT DATE(fecha_paq) fecha, SUM(paquetes_paq) total
                FROM PRODUCCION_PAQUETES
                WHERE MONTH(fecha_paq) = $mes
                AND DAY(fecha_paq) BETWEEN $inicio AND $fin
                AND YEAR(fecha_paq) = YEAR(CURDATE())
                GROUP BY DATE(fecha_paq)";
    }
} elseif($tipo === 'anio') {
    $sql = "SELECT CONCAT('Sem ', WEEK(fecha_paq, 1)) fecha, SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE YEAR(fecha_paq) = YEAR(CURDATE())
            GROUP BY WEEK(fecha_paq, 1), CONCAT('Sem ', WEEK(fecha_paq, 1))
            ORDER BY WEEK(fecha_paq, 1) ASC";
}else{
    $sql = "SELECT DATE(fecha_paq) fecha, SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq) = $mes
            AND YEAR(fecha_paq) = YEAR(CURDATE())
            GROUP BY DATE(fecha_paq)";
}

$res = mysqli_query($conexion,$sql);

$fechas = [];
$totales = [];

while($row = mysqli_fetch_assoc($res)){
    $fechas[] = $row['fecha'];
    $totales[] = $row['total'];
}

if($tipo == "semana"){

    if($semana == ""){

        $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
                 FROM PRODUCCION_PAQUETES p
                 LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
                 WHERE MONTH(p.fecha_paq) = $mes
                 AND YEAR(p.fecha_paq) = YEAR(CURDATE())
                 GROUP BY p.id_operario";

    } else {

        $inicio = (($semana - 1) * 7) + 1;
        $fin = $semana * 7;

        $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
                 FROM PRODUCCION_PAQUETES p
                 LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
                 WHERE MONTH(p.fecha_paq) = $mes
                 AND DAY(p.fecha_paq) BETWEEN $inicio AND $fin
                 AND YEAR(p.fecha_paq) = YEAR(CURDATE())
                 GROUP BY p.id_operario";
    }
}elseif($tipo === 'anio') {
    $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
             FROM PRODUCCION_PAQUETES p
             LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
             WHERE YEAR(p.fecha_paq) = YEAR(CURDATE())
             GROUP BY o.nombre
             ORDER BY total DESC";
}else{
    $sql2 = "SELECT o.nombre, SUM(p.paquetes_paq) total
             FROM PRODUCCION_PAQUETES p
             LEFT JOIN OPERARIOS o ON p.id_operario=o.id_operario
             WHERE MONTH(p.fecha_paq) = $mes
             AND YEAR(p.fecha_paq) = YEAR(CURDATE())
             GROUP BY p.id_operario";
}

$res2 = mysqli_query($conexion,$sql2);

$operarios = [];
$totales_op = [];

while($row = mysqli_fetch_assoc($res2)){
    $operarios[] = $row['nombre'];
    $totales_op[] = $row['total'];
}

header('Content-Type: application/json');
echo json_encode([
    "fechas"=>$fechas,
    "totales"=>$totales,
    "operarios"=>$operarios,
    "totales_operarios"=>$totales_op
]);

