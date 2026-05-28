<?php
/** @var mysqli $conexion */

// Importar conexion.php
require_once("../../../includes/conexion.php");
// Importar produccionModel.php
require_once("../models/produccionModel.php");

// Obtener ID del registro a editar
$id = $_GET['id'];
$fila = obtenerProduccionPorId($conexion, $id);

// Cargar catálogos para los selectores del formulario
$operarios = obtenerOperarios($conexion);

$maquinas = obtenerMaquinas($conexion);

$referencias = obtenerReferencias($conexion);

$colores = obtenerColores($conexion);

$turnos = obtenerTurnos($conexion);