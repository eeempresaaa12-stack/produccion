<?php
/** @var array $fila */
/** @var mysqli_result $operarios */
/** @var mysqli_result $maquinas */
/** @var mysqli_result $referencias */
/** @var mysqli_result $colores */
/** @var mysqli_result $turnos */

// Importar proteger.php
require_once("../../../auth/proteger.php");
// Importar editarController.php
require_once("../controllers/editarController.php");
// Importar header.php
include("../../../templates/header.php");
?>

<!-- Contenedor de Editar -->
<div class="container">
    <!-- Título -->
    <h2 class="titulo-vista">Editar Producción Sellado</h2>

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
                    name="fecha_paq"
                    value="<?php echo date('Y-m-d\TH:i', strtotime($fila['fecha_paq'])); ?>">

            <!-- Operario -->
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

            <!-- Máquina -->
            <label>Máquina</label>
            <select name="id_maquina">
                <?php while($m = mysqli_fetch_assoc($maquinas)): ?>
                    <option
                        value="<?php echo $m['id_maquina']; ?>"
                        <?php echo ($m['id_maquina'] == $fila['id_maquina']) ? 'selected' : ''; ?>>
                        <?php echo $m['nombre_maquina']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Referencia -->
            <label>Referencia</label>
            <select name="id_referencia">
                <?php while($r = mysqli_fetch_assoc($referencias)): ?>
                    <option
                        value="<?php echo $r['id_referencia']; ?>"
                        <?php echo ($r['id_referencia'] == $fila['id_referencia']) ? 'selected' : ''; ?>>
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
                        <?php echo ($c['id_color'] == $fila['id_color']) ? 'selected' : ''; ?>>
                        <?php echo $c['nombre_color']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Turno -->
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

            <!-- Cantidad de Paquetes -->
            <label>Paquetes</label>
            <input
                type="number"
                name="paquetes_paq"
                value="<?php echo $fila['paquetes_paq']; ?>">

            <!-- Observaciones -->
            <label>Observaciones</label>
            <textarea name="observaciones_paq"><?php echo $fila['observaciones_paq']; ?></textarea>

            <br><br>

            <!-- Botón de Actualizar -->
            <button class="btn" type="submit">Actualizar</button>
            </form>

        </div>
    <!-- Botones de navegación -->
    <div class="btn-group">
        <a class="btn" href="../controllers/listaController.php">Volver al historial</a>
        <a class="btn" href="../controllers/dashboardController.php">Volver al dashboard</a>
    </div>
</div>

<?php 
// Importar footer.php
include("../../../templates/footer.php"); 
?>