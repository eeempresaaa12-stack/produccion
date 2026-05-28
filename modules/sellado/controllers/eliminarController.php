<?php
/** @var mysqli $conexion */

// Importar conexion.php
require_once("../../../includes/conexion.php");
// Importar produccionModel.php
require_once("../models/produccionModel.php");

// Obtener ID del registro a eliminar
$id = intval($_GET['id'] ?? 0);

// Eliminar registro
eliminarProduccion($conexion, $id);

// Redirigir al Lista
header("Location: ../views/lista.php");
exit;