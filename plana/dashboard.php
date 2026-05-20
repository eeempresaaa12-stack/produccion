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


/* RESUMEN BRUTO - RETAL - NETO */

$sql_resumen = "SELECT 
    SUM(peso_plana) bruto,
    SUM(retal_plana) retal,
    SUM(total_plana) neto
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


/* EFICIENCIA */

$eficiencia = ($bruto > 0) ? (($neto / $bruto) * 100) : 0;


/* MEJOR Y PEOR DIA */

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


/* MAQUINA TOP */

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


/* MEJOR OPERARIO (BULTOS) */

$sql_top_operario = "SELECT 
    p.id_operario,
    o.nombre,
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


$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


/* tabla fechas */

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


/* tabla referencias */

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


/* tabla maquina */

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

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" href="http://localhost/CONTROL_PRODUCCION/importar_plana.php" target="_blank">Importar datos del formulario</a>

<div class="resumen-mes">

    <h3>📊 Resumen del mes (Plana)</h3>

    <!-- CRECIMIENTO -->
     
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

    <!-- PRODUCCION -->

    <p>🏭 Producción bruta: <?php echo number_format($bruto); ?> kg</p>
    <p>♻️ Retal: <?php echo number_format($retal); ?> kg</p>
    <p>✅ Producción final: <?php echo number_format($neto); ?> kg</p>
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
        📉 Día más bajo: 
        <?php 
            echo ($peor_dia['fecha'] != "Sin datos") 
                ? date("d M Y", strtotime($peor_dia['fecha'])) 
                : "Sin datos"; 
            ?>
        (<?php echo number_format($peor_dia['total']); ?> kg)
    </p>

    <p>
        🏭 Máquina más productiva: 
        <?php echo $top_maquina['nombre_maquina']; ?>

        <?php if($top_maquina['total'] > 0){ ?>
            (<?php echo number_format($top_maquina['total']); ?> kg)
        <?php } ?>
    </p>

</div>

<!-- GRAFICOSS -->

<div class="seccion-graficos">

    <div class="controles">
        <button class="btn" onclick="cargarDatos('semana')">Semana</button>
        <button class="btn" onclick="cargarDatos('mes')">Mes</button>
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

    fetch("get_produccion_plana.php?tipo=" + tipo)
    .then(res => res.json())
    .then(data => {

        if(chartProduccion) chartProduccion.destroy();

        chartProduccion = new Chart(document.getElementById('graficoProduccion'), {
            type: 'line',
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