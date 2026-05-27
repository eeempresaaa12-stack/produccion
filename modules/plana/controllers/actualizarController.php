<?php
/** @var mysqli $conexion */

require_once("../../../includes/conexion.php");
require_once("../models/produccionModel.php");

$id = $_POST['id'];

$datos = [
    'fecha' => $_POST['fecha_plana'],

    'id_maquina' => $_POST['id_maquina'],

    'id_turno' => $_POST['id_turno'],

    'id_operario' => $_POST['id_operario'],

    'id_referencia' => $_POST['id_referencia'],

    'peso' => $_POST['peso_plana'],

    'bultos' => $_POST['bultos_plana'],

    'retal' => $_POST['retal_plana'],
    
    'total' => $_POST['total_plana']
];

actualizarProduccion($conexion, $id, $datos);

header("Location: ../views/lista.php");
exit;
