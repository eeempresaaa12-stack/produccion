<?php
include("../includes/header.php");
require_once("../conexion.php");

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

$sql_total = "SELECT SUM(paquetes_paq) total FROM PRODUCCION_PAQUETES";
$total = obtenerTotal($conexion, $sql_total);


/* PRODUCCION SEMANA ACTUAL */

$sql_semana = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE YEARWEEK(fecha_paq, 1) = YEARWEEK(CURDATE(), 1)";
$semana = obtenerTotal($conexion, $sql_semana);


/* PRODUCCION MES ACTUAL */

$sql_mes = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE MONTH(fecha_paq) = MONTH(CURDATE())
AND YEAR(fecha_paq) = YEAR(CURDATE())";
$mes = obtenerTotal($conexion, $sql_mes);


/* TOP OPERARIO DEL MES */

$sql_top = "SELECT o.nombre, IFNULL(SUM(p.paquetes_paq),0) total
FROM PRODUCCION_PAQUETES p
LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
WHERE MONTH(p.fecha_paq) = MONTH(CURDATE())
AND YEAR(p.fecha_paq) = YEAR(CURDATE())
GROUP BY p.id_operario
ORDER BY total DESC
LIMIT 1";

$res_top = mysqli_query($conexion,$sql_top);
$top = ($res_top && mysqli_num_rows($res_top) > 0)
    ? mysqli_fetch_assoc($res_top)
    : ["nombre" => "Sin datos", "total" => 0];


/* MEJOR Y PEOR DIA DEL MES */

$sql_dias = "SELECT 
                DATE(fecha_paq) fecha,
                SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE())
            AND YEAR(fecha_paq)=YEAR(CURDATE())
            GROUP BY DATE(fecha_paq)";

$res_dias = mysqli_query($conexion,$sql_dias);

if(!$res_dias){
    $mejor_dia = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia = ["fecha"=>"Sin datos","total"=>0];
} else {

    $mejor_dia = null;
    $peor_dia = null;

    while($row = mysqli_fetch_assoc($res_dias)){

    if(!$mejor_dia || $row['total'] > $mejor_dia['total']){
        $mejor_dia = $row;
    }

    if(!$peor_dia || $row['total'] < $peor_dia['total']){
        $peor_dia = $row;
    }
}

if(!$mejor_dia){
    $mejor_dia = ["fecha"=>"Sin datos","total"=>0];
}

if(!$peor_dia){
    $peor_dia = ["fecha"=>"Sin datos","total"=>0];
}

}


/* MES ACTUAL */

$sql_actual = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE MONTH(fecha_paq)=MONTH(CURDATE())
AND YEAR(fecha_paq)=YEAR(CURDATE())";


/* MES ANTERIOR */

$sql_anterior = "SELECT SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE MONTH(fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
AND YEAR(fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)";

$act = obtenerTotal($conexion, $sql_actual);
$ant = obtenerTotal($conexion, $sql_anterior);


/* CALCULOS SEGUROS */

$diferencia = ($act ?? 0) - ($ant ?? 0);
$porcentaje = ($ant > 0) ? (($diferencia / $ant) * 100) : 0;


/* PROMEDIO */

$dias_mes = date('t');
$promedio = ($act > 0) ? ($act / $dias_mes) : 0;


/* filtro fechas */

$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


/* tablas fechas */

$sql_tabla_fecha = "
SELECT 
DATE(fecha_paq) as fecha,
SUM(paquetes_paq) total
FROM PRODUCCION_PAQUETES
WHERE DATE(fecha_paq) BETWEEN '$desde' AND '$hasta'
GROUP BY DATE(fecha_paq)
ORDER BY fecha DESC
";

$res_tabla_fecha = mysqli_query($conexion,$sql_tabla_fecha);


/* tabla operador */

$sql_tabla_operario = "
SELECT 
o.nombre,
SUM(p.paquetes_paq) total
FROM PRODUCCION_PAQUETES p
LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
WHERE DATE(p.fecha_paq) BETWEEN '$desde' AND '$hasta'
GROUP BY o.nombre
ORDER BY total DESC
";

$res_tabla_operario = mysqli_query($conexion,$sql_tabla_operario);

?>


<!-- CONTAINER PRINCIPALLL -->


<div class="container">

<h2 class="titulo-vista">Producción Paquetes</h2>

<div class="kpis">

    <div class="card-kpi">
        <p>Total histórico</p>
        <h2><?php echo number_format($total); ?></h2>
    </div>

    <div class="card-kpi">
        <p>Producción semanal</p>
        <h2><?php echo number_format($semana); ?></h2>
    </div>

    <div class="card-kpi">
        <p>Producción del mes</p>
        <h2><?php echo number_format($mes); ?></h2>
    </div>


</div>

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

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" href="http://localhost/CONTROL_PRODUCCION/importar_paquetes.php" target="_blank">Importar datos del formulario</a>

<div class="resumen-mes">

    <h3>📊 Resumen del mes</h3>

    <!-- CRECIMIENTO -->

    <p>
        <?php 
        if($diferencia > 0){
            echo "<span style='color:green'>▲ Subió ".round($porcentaje,1)."% (+" . number_format($diferencia) . " paquetes)</span>";
        }elseif($diferencia < 0){
            echo "<span style='color:red'>▼ Bajó ".round($porcentaje,1)."% (-" . number_format(abs($diferencia)) . " paquetes)</span>";
        }else{
            echo "Sin cambios";
        }
        ?>
    </p>

    <!-- PRODUCCION -->

    <p>
        📅 Mejor día: <?php 
            echo ($mejor_dia['fecha'] != "Sin datos" && $mejor_dia['fecha'] != "")
                ? date("d M Y", strtotime($mejor_dia['fecha'])) 
                : "Sin datos"; 
            ?>
        (<?php echo number_format($mejor_dia['total']); ?> paquetes)
    </p>

    <p>
        📉 Día más bajo: <?php 
            echo ($peor_dia['fecha'] != "Sin datos") 
                ? date("d M Y", strtotime($peor_dia['fecha'])) 
                : "Sin datos"; 
            ?>
        (<?php echo number_format($peor_dia['total']); ?> paquetes)
    </p>

    <p>
        📊 Promedio diario: <?php echo round($promedio); ?> paquetes
    </p>

    <p>
    👷 Mejor operario: 
    <?php echo $top['nombre']; ?> 
    (<?php echo number_format($top['total']); ?> paquetes)
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

<div class="contenedor-grafico">

     <h3>Producción por año (Paquetes)</h3>

        <div class="header-grafico-meses">

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

<div class="grid-tablas">

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

<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_fecha); ?></strong></td>
</tr>

</table>

</div>

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

<tr class="fila-total">
<td><strong>TOTAL</strong></td>
<td><strong><?php echo number_format($total_operario); ?></strong></td>
</tr>

</table>

</div>

</div>

<br> <br> <br>

<a class="btn" href="formulario.php">Registrar Producción</a>
<a class="btn" href="lista.php">Ver Historial</a>
<a class="btn" href="../index.php">Volver al menú</a>

</div>

<!---------------------------------------------->
<!--------------- GRAFICOOOOSSS --------------->
<!---------------------------------------------->


<!---------------------------------------------------------->
<!-- GRAFICO PRODUCCION PAQUETES, OPERARIO, AÑOS Y MESES -->
<!---------------------------------------------------------->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let chartProduccion;
let chartOperarios;

function cargarDatos(tipo){

    fetch("get_produccion.php?tipo=" + tipo)
    .then(res => res.json())
    .then(data => {

        if(chartProduccion) chartProduccion.destroy();

        chartProduccion = new Chart(document.getElementById('graficoProduccion'), {
            type: 'line',
            data: {
                labels: data.fechas,
                datasets: [{
                    label: 'Producción',
                    data: data.totales,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                tooltip: {
                    enabled: true,
                    bodyFont: {
                        size: 12
                    },
                    titleFont: {
                        size: 13
                    },
                    padding: 10,
                    displayColors: false
                    }
                }
            }
        });

        // AHORA SI ORDENA DE MAYOR A MENOR
        let combinado = data.operarios.map((operario, i) => ({
            nombre: operario,
            total: data.totales_operarios[i]
        }));

        combinado.sort((a, b) => b.total - a.total);

        let operariosOrdenados = combinado.map(o => o.nombre);
        let totalesOrdenados = combinado.map(o => o.total);

        if(chartOperarios) chartOperarios.destroy();

        chartOperarios = new Chart(document.getElementById('graficoOperarios'), {
            type: 'bar',
            data: {
                labels: operariosOrdenados,
                datasets: [{
                    label: 'Operarios',
                    data: totalesOrdenados,
                    maxBarThickness: 40
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
                    tooltip: {
                        enabled: true,
                        bodyFont: {
                            size: 12
                        },
                        titleFont: {
                            size: 13
                        },
                        padding: 10,
                        displayColors: false
                    }
                }
            }
        });

    });
}


cargarDatos('semana');


let chartMeses;

function cargarGraficoMeses(){

    let anio = document.getElementById("filtroAnioMes").value;

    fetch(`get_produccion_meses.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {

        if(chartMeses) chartMeses.destroy();

        const nombresMeses = [
            "Ene","Feb","Mar","Abr","May","Jun",
            "Jul","Ago","Sep","Oct","Nov","Dic"
        ];

    fetch(`get_total_anio.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {
            document.getElementById("totalAnio").innerText =
                "Total: " + Number(data.total).toLocaleString();
        });

        chartMeses = new Chart(document.getElementById('graficoMeses'), {
            type: 'bar',
            data: {
                labels: data.meses.map(m => nombresMeses[m-1]),
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
}

cargarGraficoMeses();

</script>

