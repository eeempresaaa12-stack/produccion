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
$pagina = max(1, intval($_GET['pagina'] ?? 1));
$inicio = ($pagina - 1) * $limite;

/* BASE */
$sql_base = "FROM PRODUCCION_ROLLO r

LEFT JOIN MAQUINAS m ON r.id_maquina = m.id_maquina
LEFT JOIN TURNOS t ON r.id_turno = t.id_turno
LEFT JOIN REFERENCIAS ref ON r.id_referencia = ref.id_referencia
LEFT JOIN COLORES c ON r.id_color = c.id_color

WHERE 1=1";

/* BUSCADOR */
if(!empty($busqueda)){
    $sql_base .= " AND (
        ref.nombre_referencia LIKE '%$busqueda%' OR
        m.nombre_maquina LIKE '%$busqueda%' OR
        c.nombre_color LIKE '%$busqueda%' OR
        r.id LIKE '%$busqueda%' OR
        r.peso_rollo LIKE '%$busqueda%' OR
        r.retal_roll LIKE '%$busqueda%' OR
        r.total_roll LIKE '%$busqueda%'
    )";
}

/* FECHA */
if(!empty($fecha)){
    $sql_base .= " AND DATE(r.fecha_roll) = '$fecha'";
}

/* TOTAL */
$total_sql = "SELECT COUNT(*) as total $sql_base";
$total_resultado = mysqli_query($conexion, $total_sql);
$total_fila = mysqli_fetch_assoc($total_resultado);
$total_registros = $total_fila['total'];

$total_paginas = ceil($total_registros / $limite);

/* CONSULTA FINAL */
$sql = "SELECT r.*, 
    m.nombre_maquina,
    t.nombre_turno,
    ref.nombre_referencia,
    c.nombre_color

$sql_base

ORDER BY r.fecha_roll DESC
LIMIT $inicio, $limite";

$resultado = mysqli_query($conexion, $sql);
