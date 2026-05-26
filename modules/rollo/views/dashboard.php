<?php
/** @var array $meses */
/** @var int $mes_actual */
/** @var int $mes_anterior */
/** @var string|int $semana_actual */
/** @var float $total */
/** @var float $semana */
/** @var float $mes */
/** @var array $top_maquina */
/** @var array $top_maquina_ant */
/** @var float $bruto */
/** @var float $retal */
/** @var float $neto */
/** @var float $eficiencia */
/** @var float $bruto_ant */
/** @var float $retal_ant */
/** @var float $neto_ant */
/** @var float $eficiencia_ant */
/** @var array $mejor_dia */
/** @var array $peor_dia */
/** @var array $mejor_dia_ant */
/** @var array $peor_dia_ant */
/** @var float $diferencia */
/** @var float $porcentaje */
/** @var mysqli $conexion */
/** @var mysqli_result $res_tabla_fecha */
/** @var mysqli_result $res_tabla_maquina */
/** @var string $ultima_fecha */

require_once("../controllers/dashboardController.php");
include("../../../templates/header.php");
?>

<!-- /* CONTENEDOR PRINCIPAL */ -->
<div class="container">

<h2 class="titulo-vista">Producción Rollo</h2>

<div class="kpis">

    <div class="card-kpi">
        <p>Total histórico</p>
        <h2><?php echo number_format($total,2); ?> kg</h2>
    </div>

    <div class="card-kpi">
        <p>Producción semanal</p>
        <h2><?php echo number_format($semana,2); ?> kg</h2>
    </div>

    <div class="card-kpi">
        <p>Producción del mes</p>
        <h2><?php echo number_format($mes,2); ?> kg</h2>
    </div>

</div>

<div class="top-container">
    <div class="card-kpi top-card">
        <p>Top maquina del mes</p>
        <h2>
            <?php 
            if(!empty($top_maquina)){
                echo $top_maquina['nombre_maquina'] . "<br><span style='font-size:14px'>(".$top_maquina['total']." kg)</span>";
            }else{
                echo "Sin datos";
            }
            ?>
        </h2>
    </div>
</div>

<div class="resumenes">
    <div class="resumen-anterior">

        <h3> Resumen de <?php echo $meses[$mes_anterior]; ?></h3>

        <p>🏭 Producción bruta: <?php echo number_format($bruto_ant,2); ?> kg</p>
        <p>♻️ Retal: <?php echo number_format($retal_ant,2); ?> kg</p>
        <p>📦 Producción final: <?php echo number_format($neto_ant,2); ?> kg</p>
        <p>⚙️ Eficiencia: <?php echo round($eficiencia_ant,2); ?>%</p>

        <p>
            📅 Mejor día: 
            <?php 
                echo ($mejor_dia_ant['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($mejor_dia_ant['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($mejor_dia_ant['total'],2); ?> kg)
        </p>

        <p>
            📉 Peor día: 
            <?php 
                echo ($peor_dia_ant['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($peor_dia_ant['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($peor_dia_ant['total'],2); ?> kg)
        </p>

        <p>
            🔧 Mejor máquina: 
            <?php echo $top_maquina_ant['nombre_maquina']; ?>

            <?php if($top_maquina_ant['total'] > 0){ ?>
                (<?php echo number_format($top_maquina_ant['total'],2); ?> kg)
            <?php } ?>
        </p>

    </div>
    <div class="comparacion">
        <p>
            <?php 
            if($diferencia > 0){
                echo "<span style='color:green'>▲ Subió ".round($porcentaje,1)."% </span>
                    <span style='color:green'>+" . number_format($diferencia) . " kg</span>";
            }elseif($diferencia < 0){
                echo "<span style='color:red'>▼ Bajó ".round($porcentaje,1)."% 
                    <span style='color:red'>-" . number_format(abs($diferencia)) . " kg</span>";
            }else{
                echo "Sin cambios";
            }
            ?>
        </p>
    </div>
    <div class="resumen-actual">

        <h3> Resumen de <?php echo $meses[$mes_actual]; ?></h3>

        

        <p>🏭 Producción bruta: <?php echo number_format($bruto,2); ?> kg</p>
        <p>♻️ Retal: <?php echo number_format($retal,2); ?> kg</p>
        <p>📦 Producción final: <?php echo number_format($neto,2); ?> kg</p>
        <p>⚙️ Eficiencia: <?php echo round($eficiencia,2); ?>%</p>

        <p>
            📅 Mejor día: 
            <?php 
                echo ($mejor_dia['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($mejor_dia['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($mejor_dia['total'],2); ?> kg)
        </p>

        <p>
            📉 Peor día: 
            <?php 
                echo ($peor_dia['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($peor_dia['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($peor_dia['total'],2); ?> kg)
        </p>

        <p>
            🔧 Mejor máquina: 
            <?php echo $top_maquina['nombre_maquina']; ?>

            <?php if($top_maquina['total'] > 0){ ?>
                (<?php echo number_format($top_maquina['total'],2); ?> kg)
            <?php } ?>
        </p>

    </div>
</div>

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" id="btnImportar" onclick="abrirModal('modalImportar')">Importar Producción</a>

<!-- GRAFICOSS -->

<div class="seccion-graficos">

    <div class="controles">
        <label class="label" for="filtroMes">
            Mes:
            <select id="filtroMes" onchange="cargarDatos('semana')">
                <?php foreach($meses as $num => $nombre){ ?>
                <option 
                    value="<?php echo $num; ?>" 
                    <?php if($num == $mes_actual) echo "selected"; ?>>
                    <?php echo $nombre; ?>
                </option>
                <?php } ?>
            </select>
        </label>
        <label class="label" for="filtroSemana">
            Semana:
            <select id="filtroSemana" onchange="cargarDatos('semana')">
                <option value = "" <?php if($semana_actual == "") echo "selected"; ?> >
                    Todas
                </option>
                <option value = "1" <?php if($semana_actual == "1") echo "selected"; ?> >Semana 1</option>
                <option value = "2" <?php if($semana_actual == "2") echo "selected"; ?> >Semana 2</option>
                <option value = "3" <?php if($semana_actual == "3") echo "selected"; ?> >Semana 3</option>
                <option value = "4" <?php if($semana_actual == "4") echo "selected"; ?> >Semana 4</option>
                <option value = "5" <?php if($semana_actual == "5") echo "selected"; ?> >Semana 5</option>
            </select>
        </label>
        <button class ="btn" id="btnAnio" onclick="cargarDatos('anio')">Año</button>
    </div>

    <div class="grid-graficos">

        <div class="card-grafico">

            <h3>Producción por rollo</h3>

            <canvas id="graficoProduccion"></canvas>

        </div>

        <div class="card-grafico">
            
            <h3>Producción por máquina  </h3>

            <canvas id="graficoOperarios"></canvas>

        </div>

    </div>

</div>

<br><br><br>

<div class="contenedor-grafico">

    <h3>Producción por año (Rollo)</h3>

        <div class="header-grafico-meses">

            <select id="filtroAnioMes" onchange="cargarGraficoMeses()">
                <?php for($i=date('Y'); $i>=2023; $i--){ ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php } ?>
            </select>

            <div id="totalAnio" class="total-anio">Total: 0 kg</div>

        </div>

    <canvas id="graficoMeses"></canvas>

</div>

<br> <br> <br>

<!-- TABLAAAAS -->

<form class="filtro-fechas" method="GET">

<label>Desde</label>
<input type="date" name="desde"
value="<?php echo $_GET['desde'] ?? date('Y-m-01'); ?>">

<label>Hasta</label>
<input type="date" name="hasta"
value="<?php echo $_GET['hasta'] ?? date('Y-m-d'); ?>">

<button class="btn" type="submit">Filtrar</button>

</form>

<div class="tabla-dashboard">

<h3>Producción por fecha</h3>

<table>

<tr>
<th>Fecha</th>
<th>Bruto (kg)</th>
<th>Retal (kg)</th>
<th>Neto (kg)</th>
</tr>

<?php
$total_bruto = 0;
$total_retal = 0;
$total_neto = 0;

while($row = mysqli_fetch_assoc($res_tabla_fecha)){

$total_bruto += $row['bruto'];
$total_retal += $row['retal'];
$total_neto += $row['neto'];
?>

<tr>
<td><?php echo date("d M Y", strtotime($row['fecha'])); ?></td>
<td><?php echo number_format($row['bruto'],2); ?></td>
<td><?php echo number_format($row['retal'],2); ?></td>
<td><?php echo number_format($row['neto'],2); ?></td>
</tr>

<?php } ?>

<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_bruto,2); ?></strong></td>
<td><strong><?php echo number_format($total_retal,2); ?></strong></td>
<td><strong><?php echo number_format($total_neto,2); ?></strong></td>
</tr>

</table>

</div>

<br><br>

<div class="tabla-dashboard">

<h3>Producción por máquina</h3>

<table>

<tr>
<th>Máquina</th>
<th>Bruto (kg)</th>
<th>Retal (kg)</th>
<th>Neto (kg)</th>
</tr>

<?php
$total_bruto = 0;
$total_retal = 0;
$total_neto = 0;

while($row = mysqli_fetch_assoc($res_tabla_maquina)){

$total_bruto += $row['bruto'];
$total_retal += $row['retal'];
$total_neto += $row['neto'];
?>

<tr>
<td><?php echo $row['nombre_maquina']; ?></td>
<td><?php echo number_format($row['bruto'],2); ?></td>
<td><?php echo number_format($row['retal'],2); ?></td>
<td><?php echo number_format($row['neto'],2); ?></td>
</tr>

<?php } ?>

<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_bruto,2); ?></strong></td>
<td><strong><?php echo number_format($total_retal,2); ?></strong></td>
<td><strong><?php echo number_format($total_neto,2); ?></strong></td>
</tr>

</table>

</div>

<br> <br> <br>
    <div class="acciones">
        <a class="btn" href="https://docs.google.com/forms/d/e/1FAIpQLSch9DWsxKlht9EWeGYErV7ZpUKCQ0anjesuLoku87wk8ds8Bw/viewform?usp=dialog" target="_blank">Registrar Producción</a>
        <a class="btn" href="lista.php">Ver Historial</a>
        <a class="btn" href="../../../index.php">Volver al menú</a>
    </div>
</div>

<!-- MODAL DE IMPORTACION -->
 <div class="overlay" id ="modalImportar">
    <div class="modal">
        
        <div class="modal-header">
            <h2>Importar Rollo</h2>
            <p>Última Fecha Importada: <strong><?php echo $ultima_fecha; ?></strong></p>
            <button id="cerrarBtn" onclick="cerrarModal('modalImportar')">X</button>
        </div>
        <div class="btn-row">
            <a class="btn-nuevos" href="../../../importar/controllers/imp_rollo.php?modo=nuevos" >
                <div class="btn-text"><span class="btn-icon">🗲</span>Importar Nuevos<span class="btn-arrow">›</span></div>
            </a>
            <a class="btn-todo" href="../../../importar/controllers/imp_rollo.php?modo=todo" >
                <div class="btn-text"><span class="btn-icon">⟳</span>Reimportar Todo<span class="btn-arrow">›</span></div>
            </a>
        </div>  
    </div>
 </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../../shared/global.js"></script>
<script src="../scripts/rollo.js"></script>

<?php include("../../../templates/footer.php"); ?>
