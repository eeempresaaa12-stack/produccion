<?php
/** @var mysqli $conexion */

// Importar conexion.php
require_once("../../../includes/conexion.php");
// Importar produccionModel.php
require_once("../models/produccionModel.php");

// Obtener ID del registro a actualizar
$id = $_POST['id'];

// Recopilar datos del formulario
$datos = [
    'fecha' => $_POST['fecha_roll'],

    'id_maquina' => $_POST['id_maquina'],

    'id_turno' => $_POST['id_turno'],

    'id_referencia' => $_POST['id_referencia'],

    'id_color' => $_POST['id_color'],

    'peso' => $_POST['peso_rollo'],

    'retal' => $_POST['retal_roll'],

    'total' => $_POST['total_roll']
];

// Actualizar registro 
actualizarProduccion($conexion, $id, $datos);

// Redirigir al Lista
header("Location: ../views/lista.php");
exit;