<?php 
/** @var array $meses */
/** @var int $mes_actual */
/** @var int $mes_anterior */
/** @var int $semana_actual */
/** @var int $total */
/** @var int $semana */
/** @var int $mes */
/** @var array $top */
/** @var array $top_ant */
/** @var array $mejor_dia */
/** @var array $peor_dia */
/** @var array $mejor_dia_ant */
/** @var array $peor_dia_ant */
/** @var int $act */
/** @var int $ant */
/** @var float $porcentaje */
/** @var float $diferencia */
/** @var float $promedio */
/** @var float $promedio_ant */
/** @var string $desde */
/** @var string $hasta */
/** @var mysqli_result $res_tabla_fecha */
/** @var mysqli_result $res_tabla_operario */
/** @var string $ultima_fecha */

// Restringir acceso solo a administradores
$soloAdmin = true;
// Importar proteger.php
require_once("../../../auth/proteger.php");
// Importar dashboardController.php
require_once("../controllers/dashboardController.php");
// Importar header.php
include("../../../templates/header.php");
?>

<!-- Contenedor Principal -->
<div class="container">
    <!-- Título -->
    <h2 class="titulo-vista">Producción Sellado</h2>
    
    <!-- KPIs principales -->
    <div class="kpis">
        <!-- KPI de total historico de producción -->
        <div class="card-kpi">
            <p>Total histórico</p>
            <h2><?php echo number_format($total); ?></h2>
        </div>
        <!-- KPI de producción de la semana actual -->
        <div class="card-kpi">
            <p>Producción semanal</p>
            <h2><?php echo number_format($semana); ?></h2>
        </div>
        <!-- KPI de producción del mes actual -->
        <div class="card-kpi">
            <p>Producción del mes</p>
            <h2><?php echo number_format($mes); ?></h2>
        </div>
    </div>

    <!-- Top operario del mes -->
    <div class="top-container">
        <div class="card-kpi top-card">
            <p>Top operario del mes</p>
            <h2>
                <?php 
                if(!empty($top)){
                    echo $top['nombre'] . "<br><span style='font-size:14px'>(".$top['total']." paquetes)</span>";
                }else{
                    echo "Sin datos";
                }
                ?>
            </h2>
        </div>
    </div>

    <!-- Comparativo mes anterior vs mes actual -->
    <div class="resumenes">
        <!-- Resumen mes anterior -->
        <div class="resumen-anterior">
            <h3> Resumen de <?php echo $meses[$mes_anterior]; ?></h3>
            <p>
                📦 Producción total: <?php 
                echo number_format($ant); ?> paquetes
            </p>
            <p>
                📅 Mejor día: <?php 
                echo $mejor_dia_ant['fecha'] != "Sin datos" && $mejor_dia_ant['fecha'] != "" 
                    ? date("d M Y", strtotime($mejor_dia_ant['fecha'])) 
                    : "Sin datos";
                ?>
                (<?php echo number_format($mejor_dia_ant['total']); ?> paquetes)
            </p>
            <p>
                📉 Peor día: <?php 
                echo $peor_dia_ant['fecha'] != "Sin datos" && $peor_dia_ant['fecha'] != ""
                    ? date("d M Y", strtotime($peor_dia_ant['fecha']))
                    : "Sin datos";
                ?>
                (<?php echo number_format($peor_dia_ant['total']); ?> paquetes)
            </p>
            <p>
                📊 Promedio diario: <?php 
                echo round($promedio_ant); ?> paquetes
            </p>
            <p>
                👷 Mejor operario: <?php 
                echo $top_ant['nombre'] ?? 'Sin datos'; ?>
                <?php if(!empty($top_ant['total']) && $top_ant['total'] > 0){ ?>
                    (<?php echo number_format($top_ant['total']); ?> paquetes)
                <?php } ?>
            </p>
        </div>

        <!-- Indicador de variación entre meses -->
        <div class="comparacion">
            <p>
                <?php 
                if($diferencia > 0){
                    echo "<span style='color:green'>▲ Subió ".round($porcentaje,1)."%</span>
                        <span style='color:green'>+" . number_format($diferencia) . " paquetes</span>";
                }elseif($diferencia < 0){
                    echo "<span style='color:red'>▼ Bajó ".round($porcentaje,1)."%</span>
                        <span style='color:red'>-" . number_format(abs($diferencia)) . " paquetes</span>";
                }else{
                    echo "Sin cambios";
                }
                ?>
            </p>
        </div>

        <!-- Resumen mes actual -->
        <div class="resumen-actual">
            <h3> Resumen de <?php echo $meses[$mes_actual]; ?></h3>
            <p>
                📦 Producción total: <?php 
                echo number_format($act); ?> paquetes
            </p>
            <p>
                📅 Mejor día: <?php 
                echo ($mejor_dia['fecha'] != "Sin datos" && $mejor_dia['fecha'] != "")
                    ? date("d M Y", strtotime($mejor_dia['fecha'])) 
                    : "Sin datos"; 
                ?>
                (<?php echo number_format($mejor_dia['total']); ?> paquetes)
            </p>
            <p>
                📉 Peor día: <?php 
                echo ($peor_dia['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($peor_dia['fecha'])) 
                    : "Sin datos"; 
                ?>
                (<?php echo number_format($peor_dia['total']); ?> paquetes)
            </p>
            <p>
                📊 Promedio diario: <?php 
                echo round($promedio); ?> paquetes
            </p>
            <p>
                👷 Mejor operario: <?php 
                echo $top['nombre'] ?? 'Sin datos'; ?>
                <?php if(!empty($top['total']) && $top['total'] > 0){ ?>
                    (<?php echo number_format($top['total']); ?> paquetes)
                <?php } ?>
            </p>
        </div>
    </div>

    <!-- Botón para abrir modal de importación -->
    <a class="btn" id="btnImportar" onclick="abrirModal('modalImportar')">Importar Producción</a>

    <!-- Sección de gráficos -->
    <div class="seccion-graficos">

        <!-- Filtros -->
        <div class="controles">
            <!-- Filtrar por mes -->
            <label class="label" for="filtroMes">
                Mes:
                <select id="filtroMes" onchange="actualizarFiltros()">
                    <?php foreach($meses as $num => $nombre){ ?>
                        <option 
                            value="<?php echo $num; ?>" 
                            <?php if($num == $mes_actual) echo "selected"; ?> >
                            <?php echo $nombre; ?>
                        </option>
                    <?php } ?>
                </select>
            </label>
            <!-- Filtrar por semana segun mes -->
            <label class="label" for="filtroSemana">
                Semana:
                <select id="filtroSemana" onchange="actualizarFiltros()">
                    <!-- Todas las semanas del mes seleccionado-->
                    <option value = "" <?php if($semana_actual == "") echo "selected"; ?> >
                        Todas
                    </option>
                    <option value="1" <?php if($semana_actual == 1) echo "selected"; ?>>Semana 1</option>
                    <option value="2" <?php if($semana_actual == 2) echo "selected"; ?>>Semana 2</option>
                    <option value="3" <?php if($semana_actual == 3) echo "selected"; ?>>Semana 3</option>
                    <option value="4" <?php if($semana_actual == 4) echo "selected"; ?>>Semana 4</option>
                    <option value="5" <?php if($semana_actual == 5) echo "selected"; ?>>Semana 5</option>
                </select>
            </label>
            <!-- Filtro de año agrupado por semanas -->
            <button class="btn" id="btnAnio" onclick="cargarDatos('anio')">Año</button>
        </div>

        <!-- Gráficos de producción y operarios -->
        <div class="grid-graficos">
            <div class="card-grafico">
                <h3>Producción de paquetes</h3>
                <canvas id="graficoProduccion"></canvas>
            </div>

            <div class="card-grafico">
                <h3>Producción por Operario</h3>
                <canvas id="graficoOperarios"></canvas>
            </div>
        </div>

    </div>

    <br><br><br>

    <!-- Gráfico de producción mensual por año -->
    <div class="contenedor-grafico">
        <h3>Producción por año (Paquetes)</h3>
            <div class="header-grafico-meses">
                <!-- Selector de año -->
                <select id="filtroAnioMes" onchange="cargarGraficoMeses()">
                    <?php for($i=date('Y'); $i>=2023; $i--){ ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php } ?>
                </select>

                <div id="totalAnio" class="total-anio">Total: 0</div>

            </div>
        <canvas id="graficoMeses"></canvas>
    </div>

    <br> <br> <br>

    <!-- Tablas -->
    <form class="filtro-fechas" method="GET">
        <!-- Filtros de fechas -->
        <label>Desde</label>
            <input type="date" name="desde"
                value="<?php echo $desde; ?>">
        <label>Hasta</label>
            <input type="date" name="hasta"
                value="<?php echo $hasta; ?>">
        <!-- Botón de filtrar -->
        <button class="btn" type="submit">Filtrar</button>
    </form>

    <div class="grid-tablas">
        <!-- Tabla de producción por fecha -->
        <div class="tabla-dashboard">
            <h3>Producción por fecha</h3>
            <table>
                <tr>
                    <th>Fecha</th>
                    <th>Paquetes producidos</th>
                </tr>
                <?php
                $total_fecha = 0;
                while($row = mysqli_fetch_assoc($res_tabla_fecha)){ 
                    $total_fecha += $row['total'];
                ?>
                <tr>
                    <td><?php echo date("d M Y", strtotime($row['fecha'])); ?></td>
                    <td><?php echo number_format($row['total']); ?></td>
                </tr>
                <?php } ?>
                <!-- Fila de total -->
                <tr class="fila-total">
                    <td><strong>TOTAL</strong></td>
                    <td><strong><?php echo number_format($total_fecha); ?></strong></td>
                </tr>
            </table>
        </div>

        <!-- Tabla de producción por operario -->
        <div class="tabla-dashboard">
            <h3>Producción por operario</h3>
            <table>
                <tr>
                    <th>Operario</th>
                    <th>Paquetes producidos</th>
                </tr>
                <?php
                $total_operario = 0;
                while($row = mysqli_fetch_assoc($res_tabla_operario)){
                    $total_operario += $row['total'];
                ?>
                <tr>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo number_format($row['total']); ?></td>
                </tr>
                <?php } ?>
                <!-- Fila de total -->
                <tr class="fila-total">
                    <td><strong>TOTAL</strong></td>
                    <td><strong><?php echo number_format($total_operario); ?></strong></td>
                </tr>
            </table>
        </div>
    </div>

    <br><br><br>

    <!-- Botones de navegación -->
    <div class="acciones">
        <!-- Redirigir al Lista -->
        <a class="btn" href="lista.php">Ver Historial</a>
        <!-- Redirigir al Index -->
        <a class="btn" href="../../../index.php">Volver al menú</a>
    </div>

</div>

<!-- Modal de importación -->
<div class="overlay" id="modalImportar">
    <div class="modal">  
        <div class="modal-header">
            <h2>Importar Paquetes</h2>
            <p>Última Fecha Importada: <strong><?php echo $ultima_fecha; ?></strong></p>
            <button id="cerrarBtn" onclick="cerrarModal('modalImportar')">X</button>
        </div>
        <!-- Opciones de importación -->
        <div class="btn-row">
            <a class="btn-nuevos" href="../../../importar/controllers/imp_sellado.php?modo=nuevos">
                <div class="btn-text"><span class="btn-icon">🗲</span>Importar Nuevos<span class="btn-arrow">›</span></div>
            </a>
            <a class="btn-todo" href="../../../importar/controllers/imp_sellado.php?modo=todo">
                <div class="btn-text"><span class="btn-icon">⟳</span>Reimportar Todo<span class="btn-arrow">›</span></div>
            </a>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../../shared/global.js"></script>
<script src="../scripts/paquetes.js"></script>

<?php 
// Importar footer.php
include("../../../templates/footer.php"); 
?>