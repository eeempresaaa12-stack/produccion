<?php
/** @var mysqli_result $resultado */
/** @var int $pagina */
/** @var int $total_paginas */
/** @var string $busqueda */
/** @var string $fecha */

require_once("../../../auth/proteger.php");
require_once("../controllers/listaController.php");
include("../../../templates/header.php");
?>

<div class="container" id="containerHistorial">

    <h2 class="titulo-vista">Historial Producción Sellado</h2>

    <a class="btn" id="btnRegistrar" href="https://docs.google.com/forms/d/e/1FAIpQLSdBDR9C7O6HMN1L_pmwnOjqLjT4Ca7vod0XE_l-qjx9jmn9Tg/viewform?usp=dialog" target="_blank">Registrar Producción</a>

    <div class="card">

        <!--  FILTROS -->
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

            <input type="text" name="buscar" placeholder="Buscar..."
            value="<?php echo $busqueda; ?>">

            <input type="date" name="fecha"
            value="<?php echo $fecha; ?>">

            <a class="btn">Filtrar</a>

            <a class="btn" href="lista.php">Limpiar</a>

        </form>
        <br>

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
            <th>Acciones</th>
            </tr>
        </thead>

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
                <td>
                    <a class="btn" href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a>

                    <a class="btn" href="../controllers/eliminar.php?id=<?php echo $fila['id']; ?>" 
                    onclick="return confirm('¿Seguro que deseas eliminar este registro?');">
                    Eliminar
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>

        </table>
    </div>
<br>

<!-- PAGINACIÓN -->
<div class="card" style="text-align:center;">
    <?php
    $rango = 5;
    $inicio = max(1, $pagina - $rango);
    $fin = min($total_paginas, $pagina + $rango);

    // botón anterior
    if($pagina > 1){
        echo '<a href="?pagina='.($pagina-1).'&buscar='.$busqueda.'&fecha='.$fecha.'">«</a> ';
    }

    // primera página si el rango no empieza en 1
    if($inicio > 1){
        echo '<a href="?pagina=1&buscar='.$busqueda.'&fecha='.$fecha.'">1</a> ... ';
    }

    for($i = $inicio; $i <= $fin; $i++){

        if($i == $pagina){
            echo "<strong>$i</strong> ";
        }else{
            echo '<a href="?pagina='.$i.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$i.'</a> ';
        }

    }

    // si faltan páginas al final
    if($fin < $total_paginas){
        echo ' ... <a href="?pagina='.$total_paginas.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$total_paginas.'</a>';
    }

    // botón siguiente
    if($pagina < $total_paginas){
        echo ' <a href="?pagina='.($pagina+1).'&buscar='.$busqueda.'&fecha='.$fecha.'">»</a>';
    }
    ?>
</div>
<br>

<div class="acciones">

    <?php if($_SESSION['rol'] == 'admin'){ ?>
        <a class="btn" href="dashboard.php">Volver al Dashboard</a>
    <?php } ?>
    <a class="btn" href="../../../index.php">Volver al menú</a>

</div>
</div>

<?php include("../../../templates/footer.php"); ?>
