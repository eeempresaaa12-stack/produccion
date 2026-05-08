<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$id = $_POST['id'];
$fecha = $_POST['fecha_plana'];
$id_maquina = $_POST['id_maquina'];
$id_turno = $_POST['id_turno'];
$id_operario = $_POST['id_operario'];
$id_referencia = $_POST['id_referencia'];
$peso = $_POST['peso_plana'];
$bultos = $_POST['bultos_plana'];
$retal = $_POST['retal_plana'];
$total = $_POST['total_plana'];

$sql = "UPDATE PRODUCCION_PLANA SET
fecha_plana = '$fecha',
id_maquina = '$id_maquina',
id_turno = '$id_turno',
id_operario = '$id_operario',
id_referencia = '$id_referencia',
peso_plana = '$peso',
bultos_plana = '$bultos',
retal_plana = '$retal',
total_plana = '$total'
WHERE id = $id";

mysqli_query($conexion,$sql);

header("Location: lista.php");
exit;