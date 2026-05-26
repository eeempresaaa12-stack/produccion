<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

require_once("../models/produccionModel.php");

$id = $_POST['id'];

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

actualizarProduccion($conexion, $id, $datos);

header("Location: ../views/lista.php");
exit;