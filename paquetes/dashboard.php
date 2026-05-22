<?php
/** @var mysqli $conexion */

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

/* TOP OPERARIO DEL MES ANTERIOR*/
$sql_top_ant = "SELECT o.nombre, IFNULL(SUM(p.paquetes_paq),0) total
                FROM PRODUCCION_PAQUETES p
                LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
                WHERE MONTH(p.fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
                AND YEAR(p.fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)
                GROUP BY p.id_operario
                ORDER BY total DESC
                LIMIT 1";

$res_top_ant = mysqli_query($conexion,$sql_top_ant);

$top_ant = ($res_top_ant && mysqli_num_rows($res_top_ant) > 0)
    ? mysqli_fetch_assoc($res_top_ant)
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

/* MEJOR Y PEOR DIA MES ANTERIOR */
$sql_dias_ant = "SELECT 
                    DATE(fecha_paq) fecha,
                    SUM(paquetes_paq) total
                FROM PRODUCCION_PAQUETES
                WHERE MONTH(fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
                AND YEAR(fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)
                GROUP BY DATE(fecha_paq)";

$res_dias_ant = mysqli_query($conexion,$sql_dias_ant);

if(!$res_dias_ant){
    $mejor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
} else {

    $mejor_dia_ant = null;
    $peor_dia_ant = null;

    while($row = mysqli_fetch_assoc($res_dias_ant)){

    if(!$mejor_dia_ant || $row['total'] > $mejor_dia_ant['total']){
        $mejor_dia_ant = $row;
    }

    if(!$peor_dia_ant || $row['total'] < $peor_dia_ant['total']){
        $peor_dia_ant = $row;
    }
}

if(!$mejor_dia_ant){
    $mejor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
}

if(!$peor_dia_ant){
    $peor_dia_ant = ["fecha"=>"Sin datos","total"=>0];
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
$dias_mes_ant = date('t', strtotime('-1 month'));
$promedio_ant = ($ant > 0) ? ($ant / $dias_mes_ant) : 0;

$dias_mes = date('t');
$promedio = ($act > 0) ? ($act / $dias_mes) : 0;


/* FILTRO FECHAS */
$desde = $_GET['desde'] ?? date('Y-m-01');
$hasta = $_GET['hasta'] ?? date('Y-m-d');


/* TABLA FECHAS */
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


/* TABLA OPERARIOS */
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

<div class="resumenes">
    <div class="resumen-anterior">

        <h3> Resumen de <?php echo $meses[$mes_anterior]; ?></h3>

        <p>
            📦 Producción total:
            <?php echo number_format($ant); ?> paquetes
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
            👷 Mejor operario:
            <?php echo $top_ant['nombre']; ?>
            (<?php echo number_format($top_ant['total']); ?> paquetes)
        </p>

    </div>
    <div class="comparacion">
        <p>
            <?php 
            if($diferencia > 0){
                echo "<span style='color:green; display:block; text-align:center'>▲ Subió ".round($porcentaje,1)."%</span>
                    <span style='color:green; display:block; text-align:center'>+" . number_format($diferencia) . " paquetes</span>";
            }elseif($diferencia < 0){
                echo "<span style='color:red; display:block; text-align:center'>▼ Bajó ".round($porcentaje,1)."%</span>
                    <span style='color:red; display:block; text-align:center'>-" . number_format(abs($diferencia)) . " paquetes</span>";
            }else{
                echo "<span style='display:block; text-align:center'>Sin cambios</span>";
            }
            ?>
        </p>
    </div>
    <div class="resumen-actual">

        <h3> Resumen de <?php echo $meses[$mes_actual]; ?></h3>

        <p>
            📦 Producción total:
            <?php echo number_format($act); ?> paquetes
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
            echo round($promedio); 
            ?> paquetes
        </p>

        <p>
        👷 Mejor operario: <?php 
        echo $top['nombre']; ?> 
        (<?php echo number_format($top['total']); ?> paquetes)
        </p>

    </div>
</div>

<!-- BOTON PARA IMPORTAR DATOS DE GOOGLEEEEE -->

<a class="btn" id="btnImportar" onclick="abrirModal()">Importar Producción</a>

<?php
$res = mysqli_query($conexion, "SELECT ultima_fecha FROM IMPORTAR WHERE nombre='paquetes'");
$row = mysqli_fetch_assoc($res);
$ultima_fecha = $row['ultima_fecha'] ?? 'Nunca';
?>

<!-- GRAFICOSS -->

<div class="seccion-graficos">

    <div class="controles">
        
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
        <label class="label" for="filtroSemana">
            Semana:
            <select id="filtroSemana" onchange="actualizarFiltros()">
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
        <button class="btn" id="btnAnio" onclick="cargarDatos('anio')">Año</button>
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
    <div class="acciones">
        <a class="btn" href="https://docs.google.com/forms/d/e/1FAIpQLSdBDR9C7O6HMN1L_pmwnOjqLjT4Ca7vod0XE_l-qjx9jmn9Tg/viewform?usp=dialog" target="_blank">Registrar Producción</a>
        <a class="btn" href="lista.php">Ver Historial</a>
        <a class="btn" href="../index.php">Volver al menú</a>
    </div>
</div>

<!-- MODAL DE IMPORTACION -->
<div class="overlay" id="modalImportar">
    <div class="modal">
        
        <div class="modal-header">
            <h2>Importar Paquetes</h2>
            <p>Última importación: <strong><?php echo $ultima_fecha; ?></strong></p>
            <button id="cerrarBtn" onclick="cerrarModal()">X</button>
        </div>
        <div class="btn-row">
            <a class="btn-nuevos" href="../importar_paquetes.php?modo=nuevos">
                <div class="btn-text"><span class="btn-icon">🗲</span>Importar Nuevos<span class="btn-arrow">›</span></div>
            </a>
            <a class="btn-todo" href="../importar_paquetes.php?modo=todo">
                <div class="btn-text"><span class="btn-icon">↻</span>Reimportar Todo<span class="btn-arrow">›</span></div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function abrirModal(){
    document.getElementById('modalImportar').style.display = 'flex';
}

function cerrarModal(){
    document.getElementById('modalImportar').style.display = 'none';
}



let chartProduccion;
let chartOperarios;

function cargarDatos(tipo){
    let mes = document.getElementById("filtroMes").value;
    let semana = document.getElementById("filtroSemana").value;

    fetch("get_produccion.php?tipo=" + tipo + "&mes=" + mes + "&semana=" + semana)
    .then(res => res.json())
    .then(data => {

        if(chartProduccion) chartProduccion.destroy();

        const tipo_grafico = (tipo === 'anio') ? 'bar' : 'line';

        chartProduccion = new Chart(document.getElementById('graficoProduccion'), {
            type: tipo_grafico,
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

function actualizarFiltros(){
    let semana = document.getElementById("filtroSemana").value;

    if(semana == ""){
        cargarDatos('mes');
    } else {
        cargarDatos('semana');
    }
}

actualizarFiltros();


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

<?php include("../includes/footer.php"); ?>
