<?php
/** @var mysqli $conexion */

require_once("../conexion.php");

$id = $_POST['id'];

$fecha = $_POST['fecha_paq'];
$id_operario = $_POST['id_operario'];
$id_maquina = $_POST['id_maquina'];
$id_referencia = $_POST['id_referencia'];
$id_color = $_POST['id_color'];
$id_turno = $_POST['id_turno'];

$paquetes = $_POST['paquetes_paq'];
$observaciones = $_POST['observaciones_paq'];

$sql = "UPDATE PRODUCCION_PAQUETES SET

fecha_paq='$fecha',
id_operario='$id_operario',
id_maquina='$id_maquina',
id_referencia='$id_referencia',
id_color='$id_color',
id_turno='$id_turno',
paquetes_paq='$paquetes',
observaciones_paq='$observaciones'

WHERE id=$id";

mysqli_query($conexion,$sql);

header("Location: lista.php");
exit;