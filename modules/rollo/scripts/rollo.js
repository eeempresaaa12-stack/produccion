window.chartProduccion = window.chartProduccion || null;
window.chartOperarios = window.chartOperarios || null;
window.chartMeses = window.chartMeses || null;

function cargarDatos(tipo){
    let mes = document.getElementById("filtroMes").value;
    let semana = document.getElementById("filtroSemana").value;
    fetch("../ajax/getProduccionRollo.php?tipo=" + tipo + "&mes=" + mes + "&semana=" + semana)
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

function actualizarFiltros(){
    let semana = document.getElementById("filtroSemana").value;
    if(semana == ""){
        cargarDatos('mes');
    } else {
        cargarDatos('semana');
    }
}
actualizarFiltros();

function cargarGraficoMeses(){
    let anio = document.getElementById("filtroAnioMes").value;
    fetch(`../ajax/getProduccionMesesRollo.php?anio=${anio}`)
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
    fetch(`../ajax/getTotalAnioRollo.php?anio=${anio}`)
    .then(res => res.json())
    .then(data => {
        document.getElementById("totalAnio").innerText =
            "Total: " + Number(data.total).toLocaleString() + " kg";
    });

}
cargarGraficoMeses();
