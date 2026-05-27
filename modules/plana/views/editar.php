<?php
/** @var array $fila */
/** @var mysqli_result $maquinas */
/** @var mysqli_result $turnos */
/** @var mysqli_result $operarios */
/** @var mysqli_result $referencias */

require_once("../controllers/editarController.php");
include("../../../templates/header.php");
?>

<div class="container">

<h2 class="titulo-vista">Editar Producción Máquina Plana</h2>

<div class="card">

<form action="controllers/actualizarController.php" method="POST">

<input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

<label>Fecha</label>
<input type="datetime-local" name="fecha_plana" value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_plana'])); ?>" required>

<label>Máquina</label>
<select name="id_maquina" required>
<?php while($m = mysqli_fetch_assoc($maquinas)): ?>
<option value="<?php echo $m['id_maquina']; ?>" <?php if($fila['id_maquina'] == $m['id_maquina']) echo 'selected'; ?>><?php echo $m['nombre_maquina']; ?></option>
<?php endwhile; ?>
</select>

<label>Turno</label>
<select name="id_turno" required>
<?php while($t = mysqli_fetch_assoc($turnos)): ?>
<option value="<?php echo $t['id_turno']; ?>" <?php if($fila['id_turno'] == $t['id_turno']) echo 'selected'; ?>><?php echo $t['nombre_turno']; ?></option>
<?php endwhile; ?>
</select>

<label>Operario</label>
<select name="id_operario" required>
<?php while($o = mysqli_fetch_assoc($operarios)): ?>
<option value="<?php echo $o['id_operario']; ?>" <?php if($fila['id_operario'] == $o['id_operario']) echo 'selected'; ?>><?php echo $o['nombre']; ?></option>
<?php endwhile; ?>
</select>

<label>Referencia</label>
<select name="id_referencia" required>
<?php while($r = mysqli_fetch_assoc($referencias)): ?>
<option value="<?php echo $r['id_referencia']; ?>" <?php if($fila['id_referencia'] == $r['id_referencia']) echo 'selected'; ?>><?php echo $r['nombre_referencia']; ?></option>
<?php endwhile; ?>
</select>

<label>Peso (kg)</label>
<input type="number" name="peso_plana" value="<?php echo $fila['peso_plana']; ?>" required>

<label>Bultos</label>
<input type="number" name="bultos_plana" value="<?php echo $fila['bultos_plana']; ?>">

<label>Retal (kg)</label>
<input type="number" name="retal_plana" value="<?php echo $fila['retal_plana']; ?>">

<label>Total (kg)</label>
<input type="number" name="total_plana" value="<?php echo $fila['total_plana']; ?>" required>

<br><br>

<button class="btn">Actualizar</button>

</form>

</div>

<div class="btn-group">

<a class="btn" href="lista.php">Volver al historial</a>
<a class="btn" href="dashboard.php">Volver al dashboard</a>

</div>

</div>

<?php include("../../../templates/footer.php"); ?>