<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");
require_once("../models/produccionModel.php");

$id = intval($_GET['id'] ?? 0);
eliminarProduccion($conexion, $id);

header("Location: ../views/lista.php");
exit;