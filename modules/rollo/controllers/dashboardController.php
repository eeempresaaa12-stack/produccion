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
$total = obtenerTotalHistoricoRollo($conexion);

/* PRODUCCION SEMANA - MES */
$semana = obtenerProduccionSemanaRollo($conexion);
$mes = obtenerProduccionMesRollo($conexion);

/* TOP MAQUINA */
$top_maquina = obtenerTopMaquinaRollo($conexion);
$top_maquina_ant = obtenerTopMaquinaAnteriorRollo($conexion);

/* TOTAL MESES */
$act = obtenerTotalMesActualRollo($conexion);
$ant = obtenerTotalMesAnteriorRollo($conexion);

/* RESUMENES */
$resumen = obtenerResumenMesRollo($conexion);
$bruto = $resumen['bruto'];
$retal = $resumen['retal'];
$neto = $resumen['neto'];
$resumen_ant = obtenerResumenMesAnteriorRollo($conexion);
$bruto_ant = $resumen_ant['bruto'];
$retal_ant = $resumen_ant['retal'];
$neto_ant = $resumen_ant['neto'];

/* MEJORES Y PEORES DIAS */
$dias_mes = obtenerMejorPeorDiaMesRollo($conexion);
$mejor_dia = $dias_mes['mejor'];
$peor_dia = $dias_mes['peor'];
$dias_ant = obtenerMejorPeorDiaAnteriorRollo($conexion);
$mejor_dia_ant = $dias_ant['mejor'];
$peor_dia_ant = $dias_ant['peor'];

/* FILTROS */
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');
/* TABLAS */
$res_tabla_fecha = obtenerTablaFechasRollo($conexion,$desde,$hasta);
$res_tabla_maquina = obtenerTablaMaquinasRollo($conexion,$desde,$hasta);

/* CALCULOS */
$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;

/* EFICIENCIA */
$eficiencia     = ($bruto !== null && $bruto > 0) ? (($neto / $bruto) * 100) : null;
$eficiencia_ant = ($bruto_ant !== null && $bruto_ant > 0) ? (($neto_ant / $bruto_ant) * 100) : null;

/* IMPORTACION */
$ultima_fecha = obtenerUltimaImportacionRollo($conexion);