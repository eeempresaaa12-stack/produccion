<?php
/** @var mysqli $conexion */

include(__DIR__ . "/../includes/header.php");
require_once(__DIR__ . "/../conexion.php");

/* ACTIVAR ERRORES (solo una vez) */

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* FUNCION SEGURA */

function obtenerTotal($conexion, $sql){
    $res = mysqli_query($conexion,$sql);
    if(!$res){return 0;}

    $row = mysqli_fetch_assoc($res);
    return $row['total'] ?? 0;
}


/* BUSQUEDAAAAAAAS  */

$meses = [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre'
];

$semanas = [
    1 => 'Semana 1',
    2 => 'Semana 2',
    3 => 'Semana 3',
    4 => 'Semana 4',
    5 => 'Semana 5'
];
$semana_actual = date('W', strtotime('this week'));
$mes_actual = date('n');
$mes_anterior = date('n', strtotime('-1 month'));

/* TOTAL HISTORICO */
$sql_total = "SELECT SUM(total_plana) total FROM PRODUCCION_PLANA";
$total = obtenerTotal($conexion, $sql_total);


/* PRODUCCION SEMANA ACTUAL */
$sql_semana = "SELECT SUM(total_plana) total
                FROM PRODUCCION_PLANA
                WHERE YEARWEEK(fecha_plana, 1) = YEARWEEK(CURDATE(), 1)";
$semana = obtenerTotal($conexion, $sql_semana);


/* PRODUCCION MES ACTUAL */
$sql_mes = "SELECT SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE())
            AND YEAR(fecha_plana)=YEAR(CURDATE())";
$mes = obtenerTotal($conexion, $sql_mes);


/* TOP MAQUINA DEL MES */
$sql_top_maquina = "SELECT m.nombre_maquina, IFNULL(SUM(p.total_plana),0) total
                    FROM PRODUCCION_PLANA p
                    LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
                    WHERE MONTH(p.fecha_plana)=MONTH(CURDATE())
                    AND YEAR(p.fecha_plana)=YEAR(CURDATE())
                    GROUP BY p.id_maquina
                    ORDER BY total DESC
                    LIMIT 1";

$res_top_maquina = mysqli_query($conexion,$sql_top_maquina);
$top_maquina = ($res_top_maquina && mysqli_num_rows($res_top_maquina) > 0)
    ? mysqli_fetch_assoc($res_top_maquina)
    : ["nombre_maquina" => "Sin datos", "total" => 0];

/* TOP MAQUINA MES ANTERIOR */
$sql_top_maquina_ant = "SELECT m.nombre_maquina, IFNULL(SUM(p.total_plana),0) total
                    FROM PRODUCCION_PLANA p
                    LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
                    WHERE MONTH(p.fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
                    AND YEAR(p.fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)
                    GROUP BY p.id_maquina
                    ORDER BY total DESC
                    LIMIT 1";
$res_top_maquina_ant = mysqli_query($conexion,$sql_top_maquina_ant);
$top_maquina_ant = ($res_top_maquina_ant && mysqli_num_rows($res_top_maquina_ant) > 0)
    ? mysqli_fetch_assoc($res_top_maquina_ant)
    : ["nombre_maquina" => "Sin datos", "total" => 0];


/* CRECIMIENTO */
$sql_actual = "SELECT SUM(total_plana) total FROM PRODUCCION_PLANA
                WHERE MONTH(fecha_plana)=MONTH(CURDATE())
                AND YEAR(fecha_plana)=YEAR(CURDATE())";

$sql_anterior = "SELECT SUM(total_plana) total FROM PRODUCCION_PLANA
                WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
                AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)";

$act = obtenerTotal($conexion, $sql_actual);
$ant = obtenerTotal($conexion, $sql_anterior);

$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;


/* RESUMEN BRUTO - RETAL - NETO MES ACTUAL */
$sql_resumen = "SELECT SUM(peso_plana) bruto, SUM(retal_plana) retal, SUM(total_plana) neto
                FROM PRODUCCION_PLANA
                WHERE MONTH(fecha_plana)=MONTH(CURDATE())
                AND YEAR(fecha_plana)=YEAR(CURDATE())";

$res_resumen = mysqli_query($conexion,$sql_resumen);

if($res_resumen){
    $datos = mysqli_fetch_assoc($res_resumen);
    $bruto = $datos['bruto'] ?? 0;
    $retal = $datos['retal'] ?? 0;
    $neto = $datos['neto'] ?? 0;
}else{
    $bruto = $retal = $neto = 0;
}

/* RESUMEN BRUTO - RETAL - NETO MES ANTERIOR */
$sql_resumen_ant = "SELECT SUM(peso_plana) bruto, SUM(retal_plana) retal, SUM(total_plana) neto
                    FROM PRODUCCION_PLANA
                    WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
                    AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)";

$res_resumen_ant = mysqli_query($conexion,$sql_resumen_ant);

if($res_resumen_ant){
    $datos_ant = mysqli_fetch_assoc($res_resumen_ant);
    $bruto_ant = $datos_ant['bruto'] ?? 0;
    $retal_ant = $datos_ant['retal'] ?? 0;
    $neto_ant = $datos_ant['neto'] ?? 0;
}else{
    $bruto_ant = $retal_ant = $neto_ant = 0;
}

/* EFICIENCIA */
$eficiencia = ($bruto > 0) ? (($neto / $bruto) * 100) : 0;
$eficiencia_ant = ($bruto_ant > 0) ? (($neto_ant / $bruto_ant) * 100) : 0;


/* MEJOR Y PEOR DIA DEL MES*/
$sql_dias = "SELECT 
                DATE(fecha_plana) fecha,
                SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE())
            AND YEAR(fecha_plana)=YEAR(CURDATE())
            GROUP BY DATE(fecha_plana)";

$res_dias = mysqli_query($conexion,$sql_dias);

$mejor_dia = null;
$peor_dia = null;

if($res_dias && mysqli_num_rows($res_dias) > 0){

    while($row = mysqli_fetch_assoc($res_dias)){

        if(!$mejor_dia || $row['total'] > $mejor_dia['total']){
            $mejor_dia = $row;
        }

        if(!$peor_dia || $row['total'] < $peor_dia['total']){
            $peor_dia = $row;
        }
    }

}else{
    $mejor_dia = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia = ["fecha"=>"Sin datos","total"=>0];
}

/* MEJOR Y PEOR DIA MES ANTERIOR */
$sql_dias_ant = "SELECT 
                DATE(fecha_plana) fecha,
                SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY DATE(fecha_plana)";

$res_dias_ant = mysqli_query($conexion,$sql_dias_ant);

$mejor_dia_ant = null;
$peor_dia_ant = null;

if($res_dias_ant && mysqli_num_rows($res_dias_ant) > 0){

    while($row = mysqli_fetch_assoc($res_dias_ant)){

        if(!$mejor_dia_ant || $row['total'] > $mejor_dia_ant['total']){
            $mejor_dia_ant = $row;
        }

        if(!$peor_dia_ant || $row['total'] < $peor_dia_ant['total']){
            $peor_dia_ant = $row;
        }
    }
}else{
    $mejor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
}

/* MEJOR OPERARIO (BULTOS) */
$sql_top_operario = "SELECT p.id_operario, o.nombre,
                        IFNULL(SUM(p.bultos_plana),0) total
                    FROM PRODUCCION_PLANA p
                    LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
                    WHERE MONTH(p.fecha_plana)=MONTH(CURDATE())
                    AND YEAR(p.fecha_plana)=YEAR(CURDATE())
                    GROUP BY p.id_operario
                    ORDER BY total DESC
                    LIMIT 1";

$res_top_operario = mysqli_query($conexion,$sql_top_operario);
$top_operario = ($res_top_operario && mysqli_num_rows($res_top_operario) > 0)
    ? mysqli_fetch_assoc($res_top_operario)
    : ["nombre" => "Sin datos", "total" => 0];

/* FILTRO FECHAS */
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


/* TABLA FECHAS */
$sql_tabla_fecha = "
SELECT 
DATE(p.fecha_plana) fecha,
SUM(p.peso_plana) bruto,
SUM(p.bultos_plana) bultos,
SUM(p.retal_plana) retal,
SUM(p.total_plana) neto
FROM PRODUCCION_PLANA p
WHERE DATE(p.fecha_plana) BETWEEN '$desde' AND '$hasta'
GROUP BY DATE(p.fecha_plana)
ORDER BY fecha DESC
";

$res_tabla_fecha = mysqli_query($conexion,$sql_tabla_fecha);


/* TABLA REFERENCIAS */
$sql_tabla_referencias = "
SELECT 
r.nombre_referencia,
SUM(p.peso_plana) bruto,
SUM(p.bultos_plana) bultos,
SUM(p.retal_plana) retal,
SUM(p.total_plana) neto
FROM PRODUCCION_PLANA p
LEFT JOIN REFERENCIAS r ON p.id_referencia = r.id_referencia
WHERE DATE(p.fecha_plana) BETWEEN '$desde' AND '$hasta'
GROUP BY r.id_referencia, r.nombre_referencia
ORDER BY neto DESC
";

$res_tabla_referencias = mysqli_query($conexion,$sql_tabla_referencias);


/* TABLA MAQUINAS */
$sql_tabla_maquina = "
SELECT 
m.nombre_maquina,
SUM(p.peso_plana) bruto,
SUM(p.bultos_plana) bultos,
SUM(p.retal_plana) retal,
SUM(p.total_plana) neto
FROM PRODUCCION_PLANA p
LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
WHERE DATE(p.fecha_plana) BETWEEN '$desde' AND '$hasta'
GROUP BY m.id_maquina, m.nombre_maquina
ORDER BY neto DESC
";

$res_tabla_maquina = mysqli_query($conexion,$sql_tabla_maquina);

?>


<!-- CONTAINER PRINCIPAL -->


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

        <p>
            📅 Mejor día: 
            <?php 
            echo ($mejor_dia_ant['fecha'] != "Sin datos") 
                ? date("d M Y", strtotime($mejor_dia_ant['fecha'])) 
                : "Sin datos"; 
            ?>
            (<?php echo number_format($mejor_dia_ant['total']); ?> kg)
        </p>

        <p>
            📉 Peor día: 
            <?php 
                echo ($peor_dia_ant['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($peor_dia_ant['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($peor_dia_ant['total']); ?> kg)
        </p>

        <p>
            🔧 Mejor máquina: 
            <?php echo $top_maquina_ant['nombre_maquina']; ?>

            <?php if($top_maquina_ant['total'] > 0){ ?>
                (<?php echo number_format($top_maquina_ant['total']); ?> kg)
            <?php } ?>
        </p>

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

        <p>
            📅 Mejor día: 
            <?php 
            echo ($mejor_dia['fecha'] != "Sin datos") 
                ? date("d M Y", strtotime($mejor_dia['fecha'])) 
                : "Sin datos"; 
            ?>
            (<?php echo number_format($mejor_dia['total']); ?> kg)
        </p>

        <p>
            📉 Peor día: 
            <?php 
                echo ($peor_dia['fecha'] != "Sin datos") 
                    ? date("d M Y", strtotime($peor_dia['fecha'])) 
                    : "Sin datos"; 
                ?>
            (<?php echo number_format($peor_dia['total']); ?> kg)
        </p>

        <p>
            🔧 Mejor máquina: 
            <?php echo $top_maquina['nombre_maquina']; ?>

            <?php if($top_maquina['total'] > 0){ ?>
                (<?php echo number_format($top_maquina['total']); ?> kg)
            <?php } ?>
        </p>

    </div>
</div>
<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" id="btnImportar" href="http://localhost/CONTROL_PRODUCCION/importar_plana.php" target="_blank">Importar Producción</a>

<!-- GRAFICOSS -->

<div class="seccion-graficos">

    <div class="controles">
        <label class="label" for="filtroMes">
            Mes:
            <select id="filtroMes" onchange="cargarDatos('semana')">
                <?php foreach($meses as $num => $nombre){ ?>
                <option 
                    value="<?php echo $num; ?>" 
                    <?php if($mes_actual == $num) echo "selected"; ?> >
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
                <?php foreach($semanas as $num => $nombre){ ?>
                <option 
                    value="<?php echo $num; ?>" 
                    <?php if($semana_actual == $num) echo "selected"; ?> >
                    <?php echo $nombre; ?>
                </option>
                <?php } ?>
            </select>
        </label>
        <button class ="btn" id="btnAnio" onclick="cargarDatos('anio')">Año</button>
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

<br> <br>

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

<br> <br>

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


<br> <br> <br>
    <div class="acciones">
        <a class="btn" href="https://docs.google.com/forms/d/e/1FAIpQLSfaRx43KiOMm9vi_y_TB46Mw2c-obkC9RCe7aMIZfdR2ACYcA/viewform?usp=dialog" target="_blank">Registrar Producción</a>
        <a class="btn" href="lista.php">Ver Historial</a>
        <a class="btn" href="../index.php">Volver al menú</a>
    </div>

<!---------------------------------------------->
<!--------------- GRAFICOOOOSSS --------------->
<!---------------------------------------------->

<!---------------------------------------------------------->
<!-- GRAFICO ROLLO, MAQUINA, AÑOS Y MESES -->
<!---------------------------------------------------------->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let chartProduccion;
let chartOperarios;
let chartMeses;
let chartReferencias;

function cargarDatos(tipo){
    let filtroMes = document.getElementById("filtroMes").value;
    let filtroSemana = document.getElementById("filtroSemana").value;

    fetch("get_produccion_plana.php?tipo=" + tipo + "&mes=" + filtroMes + "&semana=" + filtroSemana)
    .then(res => res.json())
    .then(data => {

        if(chartProduccion) chartProduccion.destroy();

        const tipo_grafico = (tipo === 'anio') ? 'bar' : 'line';

        chartProduccion = new Chart(document.getElementById('graficoProduccion'), {
            type: tipo_grafico,
            data: {
                labels: data.fechas,
                datasets: [{
                        label: 'Producción (kg)',
                        data: data.totales,
                        data: data.totales,
                        tension: 0.3
                    },
                    {
                        label: 'Retal (kg)',
                        data: data.retales
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    tooltip: {
                        enabled: true,
                        bodyFont: { size: 12 },
                        titleFont: { size: 13 },
                        padding: 10,
                        displayColors: false
                    }
                }
            }
            
        });

        if(chartOperarios) chartOperarios.destroy();

        chartOperarios = new Chart(document.getElementById('graficoOperarios'), {
            type: 'bar',
            data: {
                labels: data.operarios,
                datasets: [{
                    label: 'Bultos',
                    data: data.bultos_operarios
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    tooltip: {
                        enabled: true,
                        bodyFont: { size: 12 },
                        titleFont: { size: 13 },
                        padding: 10,
                        displayColors: false
                    }
                }
            }
        });

    });
}


cargarDatos('semana');


function cargarGraficoReferencias(){

    fetch("get_referencias_plana.php")
    .then(res => res.json())
    .then(data => {

        if(chartReferencias) chartReferencias.destroy();

        chartReferencias = new Chart(document.getElementById('graficoReferencias'), {
        type: 'bar',
        data: {
            labels: data.referencias,
            datasets: [
                {
                    label: 'Producción (kg)',
                    data: data.totales
                },
                {
                    label: 'Bultos',
                    data: data.bultos
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                tooltip: {
                    enabled: true,
                    bodyFont: { size: 12 },
                    titleFont: { size: 13 },
                    padding: 10,
                    displayColors: false
                }
            }
        }
    });

});

}

cargarGraficoReferencias();


function cargarGraficoMeses(){

    let anio = document.getElementById("filtroAnioMes").value;

    fetch(`get_produccion_meses_plana.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {

        if(chartMeses) chartMeses.destroy();

        const meses = ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];

        chartMeses = new Chart(document.getElementById('graficoMeses'), {
            type: 'bar',
            data: {
                labels: data.meses.map(m => meses[m-1]),
                datasets: [{
                    label: 'Producción mensual',
                    data: data.totales
                }]
            },

                       options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                },

                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        bodyFont: {
                            size: 12
                        },
                        titleFont: {
                            size: 12
                        }
                    }
                },

                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 13
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 13
                            }
                        }
                    }
                }
            }
        });

    });

    fetch(`get_total_anio_plana.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalAnio").innerText =
            "Total: " + Number(data.total).toLocaleString() + " kg";
    });

}

cargarGraficoMeses();

</script>
</div>
<?php include("../includes/footer.php"); ?>
