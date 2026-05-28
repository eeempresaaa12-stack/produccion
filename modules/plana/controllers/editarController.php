<?php
/** @var mysqli $conexion */

// Importar conexion.php
require_once("../../../includes/conexion.php");
// Importar produccionModel.php
require_once("../models/produccionModel.php");

// Obtener ID del registro a editar
$id = $_GET['id'];
$fila = obtenerRegistroPlanaPorId($conexion, $id);

// Cargar catálogos para los selectores del formulario
$maquinas = obtenerMaquinasPlana($conexion);

$turnos = obtenerTurnosPlana($conexion);

$operarios = obtenerOperariosPlana($conexion);

$referencias = obtenerReferenciasPlana($conexion);