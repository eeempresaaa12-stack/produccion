<?php
/** @var array $fila */
/** @var mysqli_result $operarios */
/** @var mysqli_result $maquinas */
/** @var mysqli_result $referencias */
/** @var mysqli_result $colores */
/** @var mysqli_result $turnos */

require_once("../../../auth/proteger.php");
require_once("../controllers/dashboardController.php");
include("../../../templates/header.php");
?>

<div class="container">

<h2 class="titulo-vista">Editar Producción Sellado</h2>

<div class="card">

<form action="../controllers/actualizarController.php" method="POST">

<input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

<!-- FECHA -->
<label>Fecha</label>

<input 
    type="datetime-local"
    name="fecha_paq"
    value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_paq'])); ?>">

<!-- OPERARIO -->
<label>Operario</label>

<select name="id_operario">

<?php while($op = mysqli_fetch_assoc($operarios)): ?>

<option 
    value="<?php echo $op['id_operario']; ?>"
    <?php echo ($op['id_operario'] == $fila['id_operario']) ? 'selected' : ''; ?>>
    <?php echo $op['nombre']; ?>
</option>

<?php endwhile; ?>

</select>

<!-- MAQUINA -->
<label>Máquina</label>
<select name="id_maquina">
<?php while($m = mysqli_fetch_assoc($maquinas)): ?>

<option
    value="<?php echo $m['id_maquina']; ?>"
    <?php echo ($m['id_maquina'] == $fila['id_maquina']) ? 'selected' : ''; ?>
>
    <?php echo $m['nombre_maquina']; ?>
</option>

<?php endwhile; ?>
</select>

<!-- REFERENCIA -->
<label>Referencia</label>
<select name="id_referencia">
<?php while($r = mysqli_fetch_assoc($referencias)): ?>

<option
    value="<?php echo $r['id_referencia']; ?>"
    <?php echo ($r['id_referencia'] == $fila['id_referencia']) ? 'selected' : ''; ?>
>
    <?php echo $r['nombre_referencia']; ?>
</option>

<?php endwhile; ?>
</select>

<!-- COLOR -->
<label>Color</label>
<select name="id_color">
<?php while($c = mysqli_fetch_assoc($colores)): ?>

<option
    value="<?php echo $c['id_color']; ?>"
    <?php echo ($c['id_color'] == $fila['id_color']) ? 'selected' : ''; ?>>
    <?php echo $c['nombre_color']; ?>
</option>

<?php endwhile; ?>
</select>

<!-- TURNO -->
<label>Turno</label>
<select name="id_turno">
<?php while($t = mysqli_fetch_assoc($turnos)): ?>

<option
    value="<?php echo $t['id_turno']; ?>"
    <?php echo ($t['id_turno'] == $fila['id_turno']) ? 'selected' : ''; ?>>
    <?php echo $t['nombre_turno']; ?>
</option>

<?php endwhile; ?>
</select>

<!-- PAQUETES -->
<label>Paquetes</label>
<input
    type="number"
    name="paquetes_paq"
    value="<?php echo $fila['paquetes_paq']; ?>">

<!-- OBSERVACIONES -->
<label>Observaciones</label>

<textarea name="observaciones_paq"><?php echo $fila['observaciones_paq']; ?></textarea>

<br><br>

<button class="btn" type="submit">Actualizar</button>

</form>

</div>

<div class="btn-group">
    <a class="btn" href="../controllers/listaController.php">Volver al historial</a>
    <a class="btn" href="../controllers/dashboardController.php">Volver al dashboard</a>
</div>
</div>

<?php include("../../templates/footer.php"); ?>