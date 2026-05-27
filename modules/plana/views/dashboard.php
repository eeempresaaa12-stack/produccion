<?php
/** @var array $meses */
/** @var array $semanas */
/** @var int $mes_actual */
/** @var int $mes_anterior */
/** @var int $semana_actual */
/** @var float $total */
/** @var float $semana */
/** @var float $mes */
/** @var array $top_operario */
/** @var array $top_operario_ant */
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
/** @var mysqli_result $res_tabla_fecha */
/** @var mysqli_result $res_tabla_referencias */
/** @var mysqli_result $res_tabla_maquina */
/** @var string $desde */
/** @var string $hasta */
/** @var string $ultima_fecha */

require_once("../controllers/dashboardController.php");
include("../../../templates/header.php");
?>

<!-- CONTENEDOR PRINCIPAL -->
<div class="container">

<h2 class="titulo-vista">Producción Máquina Plana</h2>

<div class="kpis">

    <div class="card-kpi">
        <p>Total histórico</p>
        <h2><?php echo number_format($total); ?> kg</h2>
    </div>

    <div class="card-kpi">
        <p>Producción semanal</p>
        <h2><?php echo number_format($semana); ?> kg</h2>
    </div>

    <div class="card-kpi">
        <p>Producción del mes</p>
        <h2><?php echo number_format($mes); ?> kg</h2>
    </div>

</div>

<div class="top-container">
    <div class="card-kpi top-card">
        <p>Top operario del mes</p>
        <h2>
            <?php echo $top_operario['nombre'] ?? 'Sin datos'; ?><br>
            <small><?php echo number_format($top_operario['total'] ?? 0); ?> paquetes</small>
        </h2>
    </div>
</div>

<div class="resumenes">
    <div class="resumen-anterior">

        <h3> Resumen de <?php echo $meses[$mes_anterior]; ?></h3>

        <p>🏭 Producción bruta: <?php echo number_format($bruto_ant); ?> kg</p>
        <p>♻️ Retal: <?php echo number_format($retal_ant); ?> kg</p>
        <p>📦 Producción final: <?php echo number_format($neto_ant); ?> kg</p>
        <p>⚙️ Eficiencia: <?php echo round($eficiencia_ant,1); ?>%</p>

        <p>📅 Mejor día: <?php echo ($mejor_dia_ant['fecha'] != "Sin datos") ? date("d M Y", strtotime($mejor_dia_ant['fecha'])) : "Sin datos"; ?> (<?php echo number_format($mejor_dia_ant['total']); ?> kg)</p>

        <p>📉 Peor día: <?php echo ($peor_dia_ant['fecha'] != "Sin datos") ? date("d M Y", strtotime($peor_dia_ant['fecha'])) : "Sin datos"; ?> (<?php echo number_format($peor_dia_ant['total']); ?> kg)</p>

        <p>🔧 Mejor máquina: <?php echo $top_operario_ant['nombre'] ?? 'Sin datos'; ?> <?php if(!empty($top_operario_ant['total'])){ ?>(<?php echo number_format($top_operario_ant['total']); ?> kg)<?php } ?></p>

    </div>
    <div class="comparacion">
        <p>
            <?php 
            if($diferencia > 0){
                echo "<span style='color:green'>▲ Subió ".round($porcentaje,1)."% (+" . number_format($diferencia) . " kg)</span>";
            }elseif($diferencia < 0){
                echo "<span style='color:red'>▼ Bajó ".round($porcentaje,1)."% (-" . number_format(abs($diferencia)) . " kg)</span>";
            }else{
                echo "Sin cambios";
            }
            ?>
        </p>
    </div>
    <div class="resumen-actual">

        <h3> Resumen de <?php echo $meses[$mes_actual]; ?></h3>

        <p>🏭 Producción bruta: <?php echo number_format($bruto); ?> kg</p>
        <p>♻️ Retal: <?php echo number_format($retal); ?> kg</p>
        <p>📦 Producción final: <?php echo number_format($neto); ?> kg</p>
        <p>⚙️ Eficiencia: <?php echo round($eficiencia,1); ?>%</p>

        <p>📅 Mejor día: <?php echo ($mejor_dia['fecha'] != "Sin datos") ? date("d M Y", strtotime($mejor_dia['fecha'])) : "Sin datos"; ?> (<?php echo number_format($mejor_dia['total']); ?> kg)</p>

        <p>📉 Peor día: <?php echo ($peor_dia['fecha'] != "Sin datos") ? date("d M Y", strtotime($peor_dia['fecha'])) : "Sin datos"; ?> (<?php echo number_format($peor_dia['total']); ?> kg)</p>

        <p>🔧 Mejor máquina: <?php echo $top_operario['nombre'] ?? 'Sin datos'; ?> <?php if(!empty($top_operario['total'])){ ?>(<?php echo number_format($top_operario['total']); ?> kg)<?php } ?></p>

    </div>
</div>

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLE -->
<a class="btn" id="btnImportar" onclick="abrirModal('modalImportar')">Importar Producción</a>

<!-- GRAFICOSS -->
<div class="seccion-graficos">

    <div class="controles">
        <label class="label" for="filtroMes">
            Mes:
            <select id="filtroMes" onchange="cargarDatos('semana')">
                <?php foreach($meses as $num => $nombre){ ?>
                <option value="<?php echo $num; ?>" <?php if($mes_actual == $num) echo "selected"; ?>><?php echo $nombre; ?></option>
                <?php } ?>
            </select>
        </label>
        <label class="label" for="filtroSemana">
            Semana:
            <select id="filtroSemana" onchange="cargarDatos('semana')">
                <option value="" <?php if($semana_actual == "") echo "selected"; ?>>Todas</option>
                <?php foreach($semanas as $num => $nombre){ ?>
                <option value="<?php echo $num; ?>" <?php if($semana_actual == $num) echo "selected"; ?>><?php echo $nombre; ?></option>
                <?php } ?>
            </select>
        </label>
        <button class="btn" id="btnAnio" onclick="cargarDatos('anio')">Año</button>
    </div>

    <div class="grid-graficos">

        <div class="card-grafico">
            <h3>Producción (kg)</h3>
            <canvas id="graficoProduccion"></canvas>
        </div>

        <div class="card-grafico">
            <h3>Producción por operario (bultos)</h3>
            <canvas id="graficoOperarios"></canvas>
        </div>

    </div>

</div>

<br><br><br>

<div class="card-grafico">
    <h3>Producción por referencia</h3>
    <canvas id="graficoReferencias"></canvas>
</div>

<br><br>

<div class="contenedor-grafico">

    <h3>Producción por año (Máquina Plana)</h3>

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

<br><br><br>

<!-- TABLAS -->
<form class="filtro-fechas" method="GET">

<label>Desde</label>
<input type="date" name="desde" value="<?php echo $desde; ?>">

<label>Hasta</label>
<input type="date" name="hasta" value="<?php echo $hasta; ?>">

<button class="btn" type="submit">Filtrar</button>

</form>

<div class="tabla-dashboard">

<h3>Producción por fecha</h3>

<table>
<tr>
<th>Fecha</th>
<th>Bruto</th>
<th>Bultos</th>
<th>Retal</th>
<th>Neto</th>
</tr>

<?php
$total_bruto = 0;
$total_bultos = 0;
$total_retal = 0;
$total_neto = 0;

while($row = mysqli_fetch_assoc($res_tabla_fecha)){
    $total_bruto += $row['bruto'];
    $total_bultos += $row['bultos'];
    $total_retal += $row['retal'];
    $total_neto += $row['neto'];
?>
<tr>
<td><?php echo date("d M Y", strtotime($row['fecha'])); ?></td>
<td><?php echo number_format($row['bruto'],2); ?></td>
<td><?php echo number_format($row['bultos']); ?></td>
<td><?php echo number_format($row['retal'],2); ?></td>
<td><?php echo number_format($row['neto'],2); ?></td>
</tr>
<?php } ?>
<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_bruto,2); ?></strong></td>
<td><strong><?php echo number_format($total_bultos); ?></strong></td>
<td><strong><?php echo number_format($total_retal,2); ?></strong></td>
<td><strong><?php echo number_format($total_neto,2); ?></strong></td>
</tr>
</table>

</div>

<br><br>

<div class="tabla-dashboard">

<h3>Producción por referencia</h3>

<table>
<tr>
<th>Referencia</th>
<th>Bruto</th>
<th>Bultos</th>
<th>Retal</th>
<th>Neto</th>
</tr>

<?php
$total_bruto = 0;
$total_bultos = 0;
$total_retal = 0;
$total_neto = 0;

while($row = mysqli_fetch_assoc($res_tabla_referencias)){
    $total_bruto += $row['bruto'];
    $total_bultos += $row['bultos'];
    $total_retal += $row['retal'];
    $total_neto += $row['neto'];
?>
<tr>
<td><?php echo $row['nombre_referencia']; ?></td>
<td><?php echo number_format($row['bruto'],2); ?></td>
<td><?php echo number_format($row['bultos']); ?></td>
<td><?php echo number_format($row['retal'],2); ?></td>
<td><?php echo number_format($row['neto'],2); ?></td>
</tr>
<?php } ?>
<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_bruto,2); ?></strong></td>
<td><strong><?php echo number_format($total_bultos); ?></strong></td>
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
<th>Bruto</th>
<th>Bultos</th>
<th>Retal</th>
<th>Neto</th>
</tr>

<?php
$total_bruto = 0;
$total_bultos = 0;
$total_retal = 0;
$total_neto = 0;

while($row = mysqli_fetch_assoc($res_tabla_maquina)){
    $total_bruto += $row['bruto'];
    $total_bultos += $row['bultos'];
    $total_retal += $row['retal'];
    $total_neto += $row['neto'];
?>
<tr>
<td><?php echo $row['nombre_maquina']; ?></td>
<td><?php echo number_format($row['bruto'],2); ?></td>
<td><?php echo number_format($row['bultos']); ?></td>
<td><?php echo number_format($row['retal'],2); ?></td>
<td><?php echo number_format($row['neto'],2); ?></td>
</tr>
<?php } ?>
<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_bruto,2); ?></strong></td>
<td><strong><?php echo number_format($total_bultos); ?></strong></td>
<td><strong><?php echo number_format($total_retal,2); ?></strong></td>
<td><strong><?php echo number_format($total_neto,2); ?></strong></td>
</tr>
</table>

</div>

<br><br><br>
    <div class="acciones">
        <a class="btn" href="https://docs.google.com/forms/d/e/1FAIpQLSfaRx43KiOMm9vi_y_TB46Mw2c-obkC9RCe7aMIZfdR2ACYcA/viewform?usp=dialog" target="_blank">Registrar Producción</a>
        <a class="btn" href="lista.php">Ver Historial</a>
        <a class="btn" href="../../../index.php">Volver al menú</a>
    </div>
</div>

<!-- MODAL DE IMPORTACION -->
<div class="overlay" id="modalImportar">
    <div class="modal">
        <div class="modal-header">
            <h2>Importar Plana</h2>
            <p>Última Fecha Importada: <strong><?php echo $ultima_fecha; ?></strong></p>
            <button id="cerrarBtn" onclick="cerrarModal('modalImportar')">X</button>
        </div>
        <div class="btn-row">
            <a class="btn-nuevos" href="../../../importar/controllers/imp_plana.php?modo=nuevos" >
                <div class="btn-text"><span class="btn-icon">🗲</span>Importar Nuevos<span class="btn-arrow">›</span></div>
            </a>
            <a class="btn-todo" href="../../../importar/controllers/imp_plana.php?modo=todo" >
                <div class="btn-text"><span class="btn-icon">⟳</span>Reimportar Todo<span class="btn-arrow">›</span></div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="../../shared/global.js"></script>
<script src="../scripts/plana.js"></script>

<?php include("../../../templates/footer.php"); ?>