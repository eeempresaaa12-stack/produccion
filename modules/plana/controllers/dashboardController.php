<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");
require_once("../models/dashboardModel.php");

$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];
$semanas = [
    1 => 'Semana 1',
    2 => 'Semana 2',
    3 => 'Semana 3',
    4 => 'Semana 4',
    5 => 'Semana 5'
];
$semana_actual = date('W', strtotime('this week'));
$mes_actual = date('n');
$mes_anterior = date('n', strtotime('-1 month'));

/* TOTAL HISTORICO */
$total = obtenerTotalHistoricoPlana($conexion);

/* PRODUCCION SEMANA - MES */
$semana = obtenerProduccionSemanaPlana($conexion);
$mes = obtenerProduccionMesPlana($conexion);

/* TOP OPERARIO */
$top_operario = obtenerTopOperarioPlana($conexion);
$top_operario_ant = obtenerTopOperarioAnteriorPlana($conexion);

/* TOTAL MESES */
$act = obtenerTotalMesActualPlana($conexion);
$ant = obtenerTotalMesAnteriorPlana($conexion);

/* RESUMENES */
$resumen = obtenerResumenMesPlana($conexion);
$bruto = $resumen['bruto'];
$retal = $resumen['retal'];
$neto = $resumen['neto'];
$eficiencia = ($bruto > 0) ? (($neto / $bruto) * 100) : 0;
$resumen_ant = obtenerResumenMesAnteriorPlana($conexion);
$bruto_ant = $resumen_ant['bruto'];
$retal_ant = $resumen_ant['retal'];
$neto_ant = $resumen_ant['neto'];
$eficiencia_ant = ($bruto_ant > 0) ? (($neto_ant / $bruto_ant) * 100) : 0;

/* MEJORES Y PEORES DIAS */
$dias_mes = obtenerMejorPeorDiaMesPlana($conexion);
$mejor_dia = $dias_mes['mejor'];
$peor_dia = $dias_mes['peor'];
$dias_ant = obtenerMejorPeorDiaAnteriorPlana($conexion);
$mejor_dia_ant = $dias_ant['mejor'];
$peor_dia_ant = $dias_ant['peor'];

/* FILTROS */
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
/* TABLAS */
$res_tabla_fecha = obtenerTablaFechasPlana($conexion, $desde, $hasta);
$res_tabla_referencias = obtenerTablaReferenciasPlana($conexion, $desde, $hasta);
$res_tabla_maquina = obtenerTablaMaquinasPlana($conexion, $desde, $hasta);

/* CALCULOS */
$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;

/* IMPORTACION */
$ultima_fecha = obtenerUltimaImportacionPlana($conexion);