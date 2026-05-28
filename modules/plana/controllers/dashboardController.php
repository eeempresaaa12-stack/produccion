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
$total = obtenerTotalHistoricoPlana($conexion);

// Producción de la semana y del mes actual
$semana = obtenerProduccionSemanaPlana($conexion);
$mes = obtenerProduccionMesPlana($conexion);

// Top operario mes actual y anterior
$top_operario = obtenerTopOperarioPlana($conexion);
$top_operario_ant = obtenerTopOperarioAnteriorPlana($conexion);

// Totales mes actual y anterior
$act = obtenerTotalMesActualPlana($conexion);
$ant = obtenerTotalMesAnteriorPlana($conexion);

// Resumenes del mes actual y anterior
$resumen = obtenerResumenMesPlana($conexion);
$bruto = $resumen['bruto'];
$retal = $resumen['retal'];
$neto  = $resumen['neto'];
$eficiencia = ($bruto !== null && $bruto > 0) ? (($neto / $bruto) * 100) : null;
$resumen_ant = obtenerResumenMesAnteriorPlana($conexion);
$bruto_ant = $resumen_ant['bruto'];
$retal_ant = $resumen_ant['retal'];
$neto_ant  = $resumen_ant['neto'];
$eficiencia_ant = ($bruto_ant !== null && $bruto_ant > 0) ? (($neto_ant / $bruto_ant) * 100) : null;

// Mejor y peor día del mes actual y anterior
$dias_mes = obtenerMejorPeorDiaMesPlana($conexion);
$mejor_dia = $dias_mes['mejor'];
$peor_dia = $dias_mes['peor'];
$dias_ant = obtenerMejorPeorDiaAnteriorPlana($conexion);
$mejor_dia_ant = $dias_ant['mejor'];
$peor_dia_ant = $dias_ant['peor'];

// Filtros de fecha para tablas
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
// Tablas de producción por fecha, referencias y máquina
$res_tabla_fecha = obtenerTablaFechasPlana($conexion, $desde, $hasta);
$res_tabla_referencias = obtenerTablaReferenciasPlana($conexion, $desde, $hasta);
$res_tabla_maquina = obtenerTablaMaquinasPlana($conexion, $desde, $hasta);

// Diferencia y porcentaje de variación entre meses
$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;

// Última fecha de importación
$ultima_fecha = obtenerUltimaImportacionPlana($conexion);