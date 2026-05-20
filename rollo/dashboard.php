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

$sql_total = "SELECT SUM(total_roll) total FROM PRODUCCION_ROLLO";
$total = obtenerTotal($conexion, $sql_total);


/* PRODUCCION SEMANA ACTUAL */

$sql_semana = "SELECT SUM(total_roll) total
FROM PRODUCCION_ROLLO
WHERE YEARWEEK(fecha_roll, 1) = YEARWEEK(CURDATE(), 1)";
$semana = obtenerTotal($conexion, $sql_semana);


/* PRODUCCION MES ACTUAL */

$sql_mes = "SELECT SUM(total_roll) total
FROM PRODUCCION_ROLLO
WHERE MONTH(fecha_roll)=MONTH(CURDATE())
AND YEAR(fecha_roll)=YEAR(CURDATE())";
$mes = obtenerTotal($conexion, $sql_mes);


/* CRECIMIENTO */

$sql_actual = "SELECT SUM(total_roll) total FROM PRODUCCION_ROLLO
WHERE MONTH(fecha_roll)=MONTH(CURDATE())
AND YEAR(fecha_roll)=YEAR(CURDATE())";

$sql_anterior = "SELECT SUM(total_roll) total FROM PRODUCCION_ROLLO
WHERE MONTH(fecha_roll)=MONTH(CURDATE()-INTERVAL 1 MONTH)
AND YEAR(fecha_roll)=YEAR(CURDATE()-INTERVAL 1 MONTH)";

$act = obtenerTotal($conexion, $sql_actual);
$ant = obtenerTotal($conexion, $sql_anterior);

$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;


/* RESUMEN BRUTO - RETAL - NETO */

$sql_resumen = "SELECT 
    SUM(peso_rollo) bruto,
    SUM(retal_roll) retal,
    SUM(total_roll) neto
FROM PRODUCCION_ROLLO
WHERE MONTH(fecha_roll)=MONTH(CURDATE())
AND YEAR(fecha_roll)=YEAR(CURDATE())";

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
    DATE(fecha_roll) fecha,
    SUM(total_roll) total
FROM PRODUCCION_ROLLO
WHERE MONTH(fecha_roll)=MONTH(CURDATE())
AND YEAR(fecha_roll)=YEAR(CURDATE())
GROUP BY DATE(fecha_roll)";

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

$sql_top_maquina = "SELECT m.nombre_maquina, IFNULL(SUM(r.total_roll),0) total
FROM PRODUCCION_ROLLO r
LEFT JOIN MAQUINAS m ON r.id_maquina = m.id_maquina
WHERE MONTH(r.fecha_roll)=MONTH(CURDATE())
AND YEAR(r.fecha_roll)=YEAR(CURDATE())
GROUP BY r.id_maquina
ORDER BY total DESC
LIMIT 1";

$res_top_maquina = mysqli_query($conexion,$sql_top_maquina);
$top_maquina = ($res_top_maquina && mysqli_num_rows($res_top_maquina) > 0)
    ? mysqli_fetch_assoc($res_top_maquina)
    : ["nombre_maquina" => "Sin datos", "total" => 0];


/* filtro fechas */
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


/* tabla fechas */
$sql_tabla_fecha = "
SELECT 
DATE(p.fecha_roll) fecha,
SUM(p.peso_rollo) bruto,
SUM(p.retal_roll) retal,
SUM(p.total_roll) neto
FROM PRODUCCION_ROLLO p
WHERE DATE(p.fecha_roll) BETWEEN '$desde' AND '$hasta'
GROUP BY DATE(p.fecha_roll)
ORDER BY fecha DESC
";
$res_tabla_fecha = mysqli_query($conexion,$sql_tabla_fecha);


/* tabla maquina */
$sql_tabla_maquina = "
SELECT 
m.nombre_maquina,
SUM(p.peso_rollo) bruto,
SUM(p.retal_roll) retal,
SUM(p.total_roll) neto
FROM PRODUCCION_ROLLO p
LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
WHERE DATE(p.fecha_roll) BETWEEN '$desde' AND '$hasta'
GROUP BY m.id_maquina, m.nombre_maquina
ORDER BY neto DESC
";
$res_tabla_maquina = mysqli_query($conexion,$sql_tabla_maquina);

?>


<!-- /* CONTENEDOR PRINCIPAL */ -->



<div class="container">

<h2 class="titulo-vista">Producción por Rollo</h2>

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

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" href="http://localhost/CONTROL_PRODUCCION/importar_rollo.php" target="_blank">Importar datos del formulario</a>

<div class="resumen-mes">

    <h3>📊 Resumen del mes (Rollo)</h3>

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

    <p>🏭 Producción bruta: <?php echo number_format($bruto,2); ?> kg</p>
    <p>♻️ Retal: <?php echo number_format($retal,2); ?> kg</p>
    <p>✅ Producción final: <?php echo number_format($neto,2); ?> kg</p>
    <p>⚙️ Eficiencia: <?php echo round($eficiencia,2); ?>%</p>

    <!-- DIAS -->
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
        📉 Día más bajo: 
        <?php 
            echo ($peor_dia['fecha'] != "Sin datos") 
                ? date("d M Y", strtotime($peor_dia['fecha'])) 
                : "Sin datos"; 
            ?>
        (<?php echo number_format($peor_dia['total'],2); ?> kg)
    </p>

    <p>
        🏭 Máquina más productiva: 
        <?php echo $top_maquina['nombre_maquina']; ?>

        <?php if($top_maquina['total'] > 0){ ?>
            (<?php echo number_format($top_maquina['total'],2); ?> kg)
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
        <a class="btn" href="../index.php">Volver al menú</a>
    </div>
</div>


<!---------------------------------------------->
<!--------------- GRAFICOOOOSSS --------------->
<!---------------------------------------------->

<!---------------------------------------------------------->
<!-- GRAFICO PRODUCCION, OPERARIO, PRODUCCION POR REFERENCIA Y POR AÑO -->
<!---------------------------------------------------------->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let chartProduccion;
let chartOperarios;

function cargarDatos(tipo){

    fetch("get_produccion_rollo.php?tipo=" + tipo)
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
                        tension: 0.3
                    },
                    {
                        label: 'Retal (kg)',
                        data: data.retales,
                        tension: 0.3
                    }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });

        /* MAQUINAS */

// ORDENA MAQUINAS DE MAYOR A MENOR
        let maquinasOrdenadas = data.operarios.map((maq, i) => ({
            maquina: maq,
            total: data.totales_operarios[i]
        }));

        maquinasOrdenadas.sort((a,b) => b.total - a.total);

        let labelsMaquinas = maquinasOrdenadas.map(m => m.maquina);
        let datosMaquinas = maquinasOrdenadas.map(m => m.total);

        if(chartOperarios) chartOperarios.destroy();

        chartOperarios = new Chart(document.getElementById('graficoOperarios'), {
            type: 'bar',
            data: {
                labels: labelsMaquinas,
                datasets: [{
                    label: 'Producción por máquina',
                    data: datosMaquinas,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
}


cargarDatos('semana');


let chartMeses;

function cargarGraficoMeses(){

    let anio = document.getElementById("filtroAnioMes").value;

    fetch(`get_produccion_meses_rollo.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {

        if(chartMeses) chartMeses.destroy();

        const nombresMeses = [
            "Ene","Feb","Mar","Abr","May","Jun",
            "Jul","Ago","Sep","Oct","Nov","Dic"
        ];

        chartMeses = new Chart(document.getElementById('graficoMeses'), {
            type: 'bar',
            data: {
                labels: data.meses.map(m => nombresMeses[m-1]),
                datasets: [{
                    label: 'Producción mensual (kg)',
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
    
    fetch(`get_total_anio_rollo.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalAnio").innerText =
            "Total: " + Number(data.total).toLocaleString() + " kg";
    });

}

cargarGraficoMeses();

</script>