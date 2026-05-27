<?php
/** @var mysqli $conexion */
require_once("../../../includes/conexion.php");
require_once("../models/produccionModel.php");

$maquinas = obtenerMaquinasPlana($conexion);
$turnos = obtenerTurnosPlana($conexion);
$operarios = obtenerOperariosPlana($conexion);
$referencias = obtenerReferenciasPlana($conexion);
