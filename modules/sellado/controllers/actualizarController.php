<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");

require_once("../models/produccionModel.php");

$id = $_POST['id'];

$datos = [
    'fecha' => $_POST['fecha_paq'],

    'id_operario' => $_POST['id_operario'],

    'id_maquina' => $_POST['id_maquina'],

    'id_referencia' => $_POST['id_referencia'],

    'id_color' => $_POST['id_color'],

    'id_turno' => $_POST['id_turno'],

    'paquetes' => $_POST['paquetes_paq'],

    'observaciones' => $_POST['observaciones_paq']
];

actualizarProduccion($conexion, $id, $datos);

header("Location: ../views/lista.php");
exit;