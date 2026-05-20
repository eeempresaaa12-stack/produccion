<?php
/** @var mysqli $conexion */

include("../includes/header.php");
require_once("../conexion.php");

$id = intval($_GET['id']);

/* REGISTRO */

$sql = "SELECT * FROM PRODUCCION_ROLLO WHERE id = $id";
$res = mysqli_query($conexion,$sql);
$fila = mysqli_fetch_assoc($res);

/* SELECTS */

$maquinas = mysqli_query($conexion,"SELECT * FROM MAQUINAS");
$turnos = mysqli_query($conexion,"SELECT * FROM TURNOS");
$referencias = mysqli_query($conexion,"SELECT * FROM REFERENCIAS");
$colores = mysqli_query($conexion,"SELECT * FROM COLORES");
?>

<div class="container">

<h2 class="titulo-vista">Editar Producción Rollo</h2>

<div class="card">

<form action="actualizar.php" method="POST">

<input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

<label>Fecha</label>
<input type="datetime-local" name="fecha_roll"
value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_roll'])); ?>">

<label>Máquina</label>
<select name="id_maquina">
<?php while($m = mysqli_fetch_assoc($maquinas)){ ?>
<option value="<?php echo $m['id_maquina']; ?>"
<?php if($fila['id_maquina'] == $m['id_maquina']) echo "selected"; ?>>
<?php echo $m['nombre_maquina']; ?>
</option>
<?php } ?>
</select>

<label>Turno</label>
<select name="id_turno">
<?php while($t = mysqli_fetch_assoc($turnos)){ ?>
<option value="<?php echo $t['id_turno']; ?>"
<?php if($fila['id_turno'] == $t['id_turno']) echo "selected"; ?>>
<?php echo $t['nombre_turno']; ?>
</option>
<?php } ?>
</select>

<label>Referencia</label>
<select name="id_referencia">
<?php while($r = mysqli_fetch_assoc($referencias)){ ?>
<option value="<?php echo $r['id_referencia']; ?>"
<?php if($fila['id_referencia'] == $r['id_referencia']) echo "selected"; ?>>
<?php echo $r['nombre_referencia']; ?>
</option>
<?php } ?>
</select>

<label>Color</label>
<select name="id_color">
<?php while($c = mysqli_fetch_assoc($colores)){ ?>
<option value="<?php echo $c['id_color']; ?>"
<?php if($fila['id_color'] == $c['id_color']) echo "selected"; ?>>
<?php echo $c['nombre_color']; ?>
</option>
<?php } ?>
</select>

<label>Peso (kg)</label>
<input type="number" name="peso_rollo" step="any" class="form-control" value="<?php echo $fila['peso_rollo']; ?>">

<label>Retal (kg)</label>
<input type="number" name="retal_roll" step="any" class="form-control" value="<?php echo $fila['retal_roll']; ?>">

<label>Total (kg)</label>
<input type="number" name="total_roll" step="any" class="form-control" value="<?php echo $fila['total_roll']; ?>">

<button class="btn">Actualizar</button>
<a class="btn" href="lista.php">Volver</a>

</form>

</div>

<div class="btn-group">

<a class="btn" href="lista.php">Volver al historial</a>

<a class="btn" href="dashboard.php">Volver al dashboard</a>

</div>

</div>

<?php include("../includes/footer.php"); ?>
