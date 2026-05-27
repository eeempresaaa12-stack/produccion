<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");
require_once("../models/produccionModel.php");

$id = $_GET['id'];

$fila = obtenerRegistroPlanaPorId($conexion, $id);

$maquinas = obtenerMaquinasPlana($conexion);

$turnos = obtenerTurnosPlana($conexion);

$operarios = obtenerOperariosPlana($conexion);

$referencias = obtenerReferenciasPlana($conexion);