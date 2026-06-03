<?php
/** @var mysqli_result $resultado */
/** @var int $pagina */
/** @var int $total_paginas */
/** @var string $busqueda */
/** @var string $fecha */

// Importar proteger.php
require_once("../../../auth/proteger.php");
// Importar listaController.php
require_once("../controllers/listaController.php");
// Importar header.php
include("../../../templates/header.php");
?>

<!-- Contenedor del Historial -->
<div class="container" id="containerHistorial">
    <!-- Título -->
    <h2 class="titulo-vista">Historial Producción Sellado</h2>

        <!-- Botón para registrar producción en Google Forms -->
        <a class="btn" id="btnRegistrar" href="https://docs.google.com/forms/d/e/1FAIpQLSdBDR9C7O6HMN1L_pmwnOjqLjT4Ca7vod0XE_l-qjx9jmn9Tg/viewform?usp=dialog" target="_blank">
            Registrar Producción
        </a>

        <br> <br>

        <!-- Tarjeta -->
        <div class="card">
            <!-- Filtros de búsqueda -->
            <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                <input type="text" name="buscar" placeholder="Buscar..."
                    value="<?php echo $busqueda; ?>">
                <input type="date" name="fecha"
                    value="<?php echo $fecha; ?>">
                <!-- Botón para filtrar -->
                <a class="btn">Filtrar</a>
                <!-- Botón para limpiar el filtro -->
                <a class="btn" href="lista.php">Limpiar</a>
            </form>

            <br>

            <!-- Tabla de registros -->
            <table class="tabla">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Operario</th>
                        <th>Máquina</th>
                        <th>Turno</th>
                        <th>Referencia</th>
                        <th>Color</th>
                        <th>Paquetes</th>
                    </tr>
                </thead>
                
                <!-- Filas de registros de la base de datos -->
                <tbody>
                    <?php while($fila = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><?php echo $fila['id']; ?></td>
                            <td><?php echo $fila['fecha_paq']; ?></td>
                            <td><?php echo $fila['operario']; ?></td>
                            <td><?php echo $fila['nombre_maquina']; ?></td>
                            <td><?php echo $fila['nombre_turno']; ?></td>
                            <td><?php echo $fila['nombre_referencia']; ?></td>
                            <td><?php echo $fila['nombre_color']; ?></td>
                            <td><?php echo $fila['paquetes_paq']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <br>

<!-- Paginación -->
<div class="paginacion" style="text-align:center;">
    <?php
        // Rango de páginas visibles en la paginación
        $rango = 5;
        $inicio = max(1, $pagina - $rango);
        $fin = min($total_paginas, $pagina + $rango);
        // Botón anterior
        if($pagina > 1){
            echo '<a href="?pagina='.($pagina-1).'&buscar='.$busqueda.'&fecha='.$fecha.'">«</a> ';
        }
        // Primera página si el rango no empieza en 1
        if($inicio > 1){
            echo '<a href="?pagina=1&buscar='.$busqueda.'&fecha='.$fecha.'">1</a> ... ';
        }
        // Páginas del rango
        for($i = $inicio; $i <= $fin; $i++){
            if($i == $pagina){
                echo "<strong>$i</strong> ";
            }else{
                echo '<a href="?pagina='.$i.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$i.'</a> ';
            }
        }
        // Última página si faltan páginas al final
        if($fin < $total_paginas){
            echo ' ... <a href="?pagina='.$total_paginas.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$total_paginas.'</a>';
        }
        // Botón de siguiente
        if($pagina < $total_paginas){
            echo ' <a href="?pagina='.($pagina+1).'&buscar='.$busqueda.'&fecha='.$fecha.'">»</a>';
        }
    ?>
</div>

<br>

<!-- Botones de navegación -->
<div class="acciones">
    <!-- Redirigir al Dashboard (solo Administradores) -->
    <?php if($_SESSION['rol'] == 'admin'){ ?>
        <a class="btn" href="dashboard.php">Volver al Dashboard</a>
    <?php } ?>
    <!-- Redirigir al Index -->
    <a class="btn" href="../../../index.php">Volver al menú</a>
</div>

</div>

<?php 
// Importar footer.php
include("../../../templates/footer.php"); 
?>