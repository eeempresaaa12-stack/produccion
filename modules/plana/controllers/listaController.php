<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

/* FILTROS */
$busqueda = $_GET['buscar'] ?? '';
$fecha = $_GET['fecha'] ?? '';

$busqueda = mysqli_real_escape_string($conexion, $busqueda);
$fecha = mysqli_real_escape_string($conexion, $fecha);

/* PAGINACIÓN */
$limite = 10;
$pagina = $_GET['pagina'] ?? 1;
$inicio = ($pagina - 1) * $limite;

/* BASE */
$sql_base = "FROM PRODUCCION_PLANA p
LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
LEFT JOIN TURNOS t ON p.id_turno = t.id_turno
LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
LEFT JOIN REFERENCIAS r ON p.id_referencia = r.id_referencia
WHERE 1=1";

/* BUSCADOR */
if(!empty($busqueda)){
    $sql_base .= " AND (
        r.nombre_referencia LIKE '%$busqueda%' OR
        o.nombre LIKE '%$busqueda%' OR
        m.nombre_maquina LIKE '%$busqueda%' OR
        p.id LIKE '%$busqueda%' OR
        p.peso_plana LIKE '%$busqueda%' OR
        p.bultos_plana LIKE '%$busqueda%' OR
        p.retal_plana LIKE '%$busqueda%' OR
        p.total_plana LIKE '%$busqueda%'
    )";
}

/* FECHA */
if(!empty($fecha)){
    $sql_base .= " AND DATE(p.fecha_plana) = '$fecha'";
}

/* TOTAL */
$total_sql = "SELECT COUNT(*) as total $sql_base";
$total_resultado = mysqli_query($conexion, $total_sql);
$total_fila = mysqli_fetch_assoc($total_resultado);
$total_registros = $total_fila['total'];

$total_paginas = ceil($total_registros / $limite);

/* CONSULTA FINAL */
$sql = "SELECT p.*, 
    m.nombre_maquina, 
    t.nombre_turno, 
    o.nombre AS operario, 
    r.nombre_referencia 
$sql_base 
ORDER BY p.fecha_plana 
DESC LIMIT $inicio, $limite";

$resultado = mysqli_query($conexion, $sql);
