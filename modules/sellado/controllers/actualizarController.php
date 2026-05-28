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
    'fecha' => $_POST['fecha_paq'],

    'id_operario' => $_POST['id_operario'],

    'id_maquina' => $_POST['id_maquina'],

    'id_referencia' => $_POST['id_referencia'],

    'id_color' => $_POST['id_color'],

    'id_turno' => $_POST['id_turno'],

    'paquetes' => $_POST['paquetes_paq'],

    'observaciones' => $_POST['observaciones_paq']
];

// Actualizar registro 
actualizarProduccion($conexion, $id, $datos);

// Redirigir al Lista
header("Location: ../views/lista.php");
exit;