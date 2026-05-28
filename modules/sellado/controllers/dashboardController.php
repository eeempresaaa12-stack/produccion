<?php
/** @var mysqli $conexion */

// Importar conexion.php
require_once("../../../includes/conexion.php");
// Importar dashboardModel.php
require_once("../models/dashboardModel.php");

// Catálogos de meses y semanas
$meses = [
    1 => 'Enero',    2 => 'Febrero',   3 => 'Marzo',
    4 => 'Abril',    5 => 'Mayo',      6 => 'Junio',
    7 => 'Julio',    8 => 'Agosto',    9 => 'Septiembre',
    10 => 'Octubre', 11 => 'Noviembre',12 => 'Diciembre'
];
$semanas = [
    1 => 'Semana 1', 2 => 'Semana 2', 3 => 'Semana 3',
    4 => 'Semana 4', 5 => 'Semana 5'
];

// Fechas de referencia
$semana_actual = date('W', strtotime('this week'));
$mes_actual = date('n');
$mes_anterior = date('n', strtotime('-1 month'));

// Total histórico de producción
$total = obtenerTotalHistorico($conexion);

// Producción de la semana y del mes actual
$semana = obtenerProduccionSemana($conexion);
$mes = obtenerProduccionMes($conexion);

// Top operario mes actual y anterior
$top = obtenerTopOperario($conexion);
$top_ant = obtenerTopOperarioAnterior($conexion);

// Totales mes actual y anterior
$act = obtenerTotalMesActual($conexion);
$ant = obtenerTotalMesAnterior($conexion);

// Mejor y peor día del mes actual y anterior
$dias_mes = obtenerMejorPeorDiaMes($conexion);
$mejor_dia = $dias_mes['mejor'];
$peor_dia = $dias_mes['peor'];
$dias_ant = obtenerMejorPeorDiaAnterior($conexion);
$mejor_dia_ant = $dias_ant['mejor'];
$peor_dia_ant = $dias_ant['peor'];

// Filtros de fecha para tablas
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
// Tablas de producción por fecha y operario
$res_tabla_fecha = obtenerTablaFechas($conexion,$desde,$hasta);
$res_tabla_operario = obtenerTablaOperarios($conexion,$desde,$hasta);

// Diferencia y porcentaje de variación entre meses
$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;

// Promedio diario mes actual y anterior
$total_dias_mes = date('t');
$promedio = ($act > 0) ? ($act / $total_dias_mes) : 0;
$total_dias_mes_ant = date('t', strtotime('-1 month'));
$promedio_ant = ($ant > 0) ? ($ant / $total_dias_mes_ant) : 0;

// Última fecha de importación
$ultima_fecha = obtenerUltimaImportacion($conexion);