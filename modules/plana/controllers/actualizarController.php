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

// Actualizar registro 
actualizarProduccion($conexion, $id, $datos);

// Redirigir al Lista
header("Location: ../views/lista.php");
exit;
