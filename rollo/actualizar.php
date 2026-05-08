<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$id = $_POST['id'];
$fecha = $_POST['fecha_roll'];
$id_maquina = $_POST['id_maquina'];
$id_turno = $_POST['id_turno'];
$id_referencia = $_POST['id_referencia'];
$id_color = $_POST['id_color'];
$peso = $_POST['peso_rollo'];
$retal = $_POST['retal_roll'];
$total = $_POST['total_roll'];

$sql = "UPDATE PRODUCCION_ROLLO SET
fecha_roll = '$fecha',
id_maquina = '$id_maquina',
id_turno = '$id_turno',
id_referencia = '$id_referencia',
id_color = '$id_color',
peso_rollo = '$peso',
retal_roll = '$retal',
total_roll = '$total'
WHERE id = $id";

mysqli_query($conexion,$sql);

header("Location: lista.php");
exit;