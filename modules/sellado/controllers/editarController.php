<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

require_once("../models/produccionModel.php");

$id = $_GET['id'];

$fila = obtenerProduccionPorId($conexion, $id);

$operarios = obtenerOperarios($conexion);

$maquinas = obtenerMaquinas($conexion);

$referencias = obtenerReferencias($conexion);

$colores = obtenerColores($conexion);

$turnos = obtenerTurnos($conexion);