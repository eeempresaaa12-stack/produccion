<?php
/** @var array $fila */
/** @var mysqli_result $maquinas */
/** @var mysqli_result $turnos */
/** @var mysqli_result $referencias */
/** @var mysqli_result $colores */

// Importar proteger.php
require_once("../../../auth/proteger.php");
// Importar editarController.php
require_once("../controllers/editarController.php");
// Importar header.php
include("../../../templates/header.php");
?>

<!-- Contenedor Principal -->
<div class="container">
    <!-- Título -->
    <h2 class="titulo-vista">Editar Producción Rollo</h2>

        <!-- Tarjeta -->
        <div class="card">

            <!-- Formulario de edición -->
            <form action="../controllers/actualizarController.php" method="POST">
            <!-- ID del registro a actualizar -->
            <input type="hidden" name="id" value="<?php echo $fila['id']; ?>">

            <!-- Fecha -->
            <label>Fecha</label>
            <input 
                type="datetime-local" 
                name="fecha_roll"
                value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_roll'])); ?>">

            <!-- Máquina -->
            <label>Máquina</label>
            <select name="id_maquina">
                <?php while($m = mysqli_fetch_assoc($maquinas)): ?>
                    <option 
                        value="<?php echo $m['id_maquina']; ?>"
                        <?php if($fila['id_maquina'] == $m['id_maquina']) echo "selected"; ?>>
                        <?php echo $m['nombre_maquina']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Turno -->
            <label>Turno</label>
            <select name="id_turno">
                <?php while($t = mysqli_fetch_assoc($turnos)): ?>
                    <option 
                        value="<?php echo $t['id_turno']; ?>"
                        <?php if($fila['id_turno'] == $t['id_turno']) echo "selected"; ?>>
                        <?php echo $t['nombre_turno']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Referencia -->
            <label>Referencia</label>
            <select name="id_referencia">
                <?php while($r = mysqli_fetch_assoc($referencias)): ?>
                    <option 
                        value="<?php echo $r['id_referencia']; ?>"
                        <?php if($fila['id_referencia'] == $r['id_referencia']) echo "selected"; ?>>
                        <?php echo $r['nombre_referencia']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Color -->
            <label>Color</label>
            <select name="id_color">
                <?php while($c = mysqli_fetch_assoc($colores)): ?>
                    <option 
                        value="<?php echo $c['id_color']; ?>"
                        <?php if($fila['id_color'] == $c['id_color']) echo "selected"; ?>>
                        <?php echo $c['nombre_color']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Peso del Producido -->
            <label>Peso (kg)</label>
            <input 
                type="number"
                name="peso_rollo" 
                step="any" 
                class="form-control" 
                value="<?php echo $fila['peso_rollo']; ?>">

            <!-- Peso del Retal -->
            <label>Retal (kg)</label>
            <input 
                type="number" 
                name="retal_roll" 
                step="any" 
                class="form-control" 
                value="<?php echo $fila['retal_roll']; ?>">

            <!-- Peso Total -->
            <label>Total (kg)</label>
            <input 
                type="number" 
                name="total_roll" 
                step="any" 
                class="form-control" 
                value="<?php echo $fila['total_roll']; ?>">
        
            <br><br>

            <!-- Botón de Actualizar -->
            <button class="btn" type="submit">Actualizar</button>
            </form>

        </div>
    <!-- Botones de navegación -->
    <div class="btn-group">
        <a class="btn" href="lista.php">Volver al historial</a>
        <a class="btn" href="dashboard.php">Volver al dashboard</a>
    </div>
</div>

<?php 
// Importar footer.php
include("../../../templates/footer.php"); 
?>