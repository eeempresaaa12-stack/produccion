<?php
/** @var mysqli $conexion */

require_once("/../../../includes/conexion.php");

require_once("/../models/produccionModel.php");

$id = $_GET['id'];

$fila = obtenerProduccionPorId($conexion, $id);

$maquinas = obtenerMaquinas($conexion);

$turnos = obtenerTurnos($conexion);

$referencias = obtenerReferencias($conexion);

$colores = obtenerColores($conexion);
