<?php 
/** @var mysqli $conexion */

include("../includes/header.php");
require_once("../conexion.php");

$id = $_GET['id'];

/* TRAER REGISTRO */
$sql = "SELECT * FROM PRODUCCION_PAQUETES WHERE id = $id";
$res = mysqli_query($conexion,$sql);
$fila = mysqli_fetch_assoc($res);
?>

<div class="container">

<h2 class="titulo-vista">Editar Producción Paquetes</h2>

<div class="card">

<form action="actualizar.php" method="POST">

<input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

<!-- FECHA -->
<label>Fecha</label>
<input type="datetime-local" name="fecha_paq"
value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_paq'])); ?>">

<!-- OPERARIO -->
<label>Operario</label>
<select name="id_operario">

<?php
$res = mysqli_query($conexion,"SELECT * FROM OPERARIOS");
while($op = mysqli_fetch_assoc($res)){
$selected = ($op['id_operario'] == $fila['id_operario']) ? "selected" : "";
?>

<option value="<?php echo $op['id_operario']; ?>" <?php echo $selected; ?>>
<?php echo $op['nombre']; ?>
</option>

<?php } ?>
</select>

<!-- MAQUINA -->
<label>Máquina</label>
<select name="id_maquina">

<?php
$res = mysqli_query($conexion,"SELECT * FROM MAQUINAS");
while($m = mysqli_fetch_assoc($res)){
$selected = ($m['id_maquina'] == $fila['id_maquina']) ? "selected" : "";
?>

<option value="<?php echo $m['id_maquina']; ?>" <?php echo $selected; ?>>
<?php echo $m['nombre_maquina']; ?>
</option>

<?php } ?>
</select>

<!-- REFERENCIA -->
<label>Referencia</label>
<select name="id_referencia">

<?php
$res = mysqli_query($conexion,"SELECT * FROM REFERENCIAS");
while($r = mysqli_fetch_assoc($res)){
$selected = ($r['id_referencia'] == $fila['id_referencia']) ? "selected" : "";
?>

<option value="<?php echo $r['id_referencia']; ?>" <?php echo $selected; ?>>
<?php echo $r['nombre_referencia']; ?>
</option>

<?php } ?>
</select>

<!-- COLOR -->
<label>Color</label>
<select name="id_color">

<?php
$res = mysqli_query($conexion,"SELECT * FROM COLORES");
while($c = mysqli_fetch_assoc($res)){
$selected = ($c['id_color'] == $fila['id_color']) ? "selected" : "";
?>

<option value="<?php echo $c['id_color']; ?>" <?php echo $selected; ?>>
<?php echo $c['nombre_color']; ?>
</option>

<?php } ?>
</select>

<!-- TURNO -->
<label>Turno</label>
<select name="id_turno">

<?php
$res = mysqli_query($conexion,"SELECT * FROM TURNOS");
while($t = mysqli_fetch_assoc($res)){
$selected = ($t['id_turno'] == $fila['id_turno']) ? "selected" : "";
?>

<option value="<?php echo $t['id_turno']; ?>" <?php echo $selected; ?>>
<?php echo $t['nombre_turno']; ?>
</option>

<?php } ?>
</select>

<!-- PAQUETES -->
<label>Paquetes</label>
<input type="number" name="paquetes_paq" value="<?php echo $fila['paquetes_paq']; ?>">

<!-- OBSERVACIONES -->
<label>Observaciones</label>
<textarea name="observaciones_paq"><?php echo $fila['observaciones_paq']; ?></textarea>

<br><br>

<button class="btn" type="submit">Actualizar</button>

</form>

</div>

<div class="btn-group">

<a class="btn" href="lista.php">Volver al historial</a>

<a class="btn" href="dashboard.php">Volver al dashboard</a>

</div>

</div>

<?php include("../includes/footer.php"); ?>
