<?php
/* =================================================
   FUNCIÓN BASE
================================================= */
// Ejecutar consulta y retornar valor 'total'
function obtenerTotal($conexion, $sql){
    $res = mysqli_query($conexion,$sql);
    if(!$res){
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return $row['total'] ?? 0;
}

/* =================================================
   CONSULTAS DE TOTALES
================================================= */
// Total histórico de producción
function obtenerTotalHistorico($conexion){
    $sql = "SELECT SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES";
    return obtenerTotal($conexion, $sql);
}
// Producción de la semana actual
function obtenerProduccionSemana($conexion){
    $sql = "SELECT SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE YEARWEEK(fecha_paq, 1) = YEARWEEK(CURDATE(), 1)";
    return obtenerTotal($conexion, $sql);
}
// Producción del mes actual
function obtenerProduccionMes($conexion){
    $sql = "SELECT SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq) = MONTH(CURDATE())
            AND YEAR(fecha_paq) = YEAR(CURDATE())";
    return obtenerTotal($conexion, $sql);
}

/* =================================================
   TOP OPERARIO
================================================= */
// Operario con más producción en el mes actual
function obtenerTopOperario($conexion){
    $sql = "SELECT o.nombre, IFNULL(SUM(p.paquetes_paq),0) total
            FROM PRODUCCION_PAQUETES p
            LEFT JOIN OPERARIOS o 
            ON p.id_operario = o.id_operario
            WHERE MONTH(p.fecha_paq)=MONTH(CURDATE())
            AND YEAR(p.fecha_paq)=YEAR(CURDATE())
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion,$sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        "nombre" => "Sin datos",
        "total" => 0
    ];
}
// Operario con más producción en el mes anterior
function obtenerTopOperarioAnterior($conexion){
    $sql = "SELECT o.nombre, IFNULL(SUM(p.paquetes_paq),0) total
            FROM PRODUCCION_PAQUETES p
            LEFT JOIN OPERARIOS o 
            ON p.id_operario = o.id_operario
            WHERE MONTH(p.fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(p.fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion,$sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        "nombre" => "Sin datos",
        "total" => 0
    ];
}

/* =================================================
   TOTALES MES ACTUAL Y ANTERIOR
================================================= */
// Total de paquetes del mes actual
function obtenerTotalMesActual($conexion){
    $sql = "SELECT SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE())
            AND YEAR(fecha_paq)=YEAR(CURDATE())";
    return obtenerTotal($conexion,$sql);
}
// Total de paquetes del mes anterior
function obtenerTotalMesAnterior($conexion){
    $sql = "SELECT SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)";
    return obtenerTotal($conexion,$sql);
}

/* =================================================
   MEJOR Y PEOR DÍA
================================================= */
// Mejor y peor día de producción del mes actual
function obtenerMejorPeorDiaMes($conexion){
    $sql = "SELECT 
                DATE(fecha_paq) fecha,
                SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE())
            AND YEAR(fecha_paq)=YEAR(CURDATE())
            GROUP BY DATE(fecha_paq)";
    $res = mysqli_query($conexion,$sql);
    $mejor_dia = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia = ["fecha"=>"Sin datos","total"=>0];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            if($row['total'] > $mejor_dia['total']){
                $mejor_dia = $row;
            }
            if(
                $peor_dia['fecha'] == "Sin datos"
                || $row['total'] < $peor_dia['total']
            ){
                $peor_dia = $row;
            }
        }
    }
    return [
        "mejor" => $mejor_dia,
        "peor" => $peor_dia
    ];
}
// Mejor y peor día de producción del mes anterior
function obtenerMejorPeorDiaAnterior($conexion){
    $sql = "SELECT 
                DATE(fecha_paq) fecha,
                SUM(paquetes_paq) total
            FROM PRODUCCION_PAQUETES
            WHERE MONTH(fecha_paq)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_paq)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY DATE(fecha_paq)";
    $res = mysqli_query($conexion,$sql);
    $mejor_dia = ["fecha"=>"Sin datos","total"=>0];
    $peor_dia = ["fecha"=>"Sin datos","total"=>0];
    if($res){
        while($row = mysqli_fetch_assoc($res)){
            if($row['total'] > $mejor_dia['total']){
                $mejor_dia = $row;
            }
            if(
                $peor_dia['fecha'] == "Sin datos"
                || $row['total'] < $peor_dia['total']
            ){
                $peor_dia = $row;
            }
        }
    }
    return [
        "mejor" => $mejor_dia,
        "peor" => $peor_dia
    ];
}

/* =================================================
   TABLAS
================================================= */
// Producción agrupada por fecha en un rango
function obtenerTablaFechas($conexion,$desde,$hasta){
    $sql = "
    SELECT 
    DATE(fecha_paq) as fecha,
    SUM(paquetes_paq) total
    FROM PRODUCCION_PAQUETES
    WHERE DATE(fecha_paq) BETWEEN '$desde' AND '$hasta'
    GROUP BY DATE(fecha_paq)
    ORDER BY fecha DESC
    ";
    return mysqli_query($conexion,$sql);
}
// Producción agrupada por operario en un rango
function obtenerTablaOperarios($conexion,$desde,$hasta){
    $sql = "
    SELECT 
    o.nombre,
    SUM(p.paquetes_paq) total
    FROM PRODUCCION_PAQUETES p
    LEFT JOIN OPERARIOS o 
    ON p.id_operario = o.id_operario
    WHERE DATE(p.fecha_paq) BETWEEN '$desde' AND '$hasta'
    GROUP BY o.nombre
    ORDER BY total DESC
    ";
    return mysqli_query($conexion,$sql);
}

/* =================================================
   IMPORTACIÓN
================================================= */
// Fecha de la última importación de paquetes
function obtenerUltimaImportacion($conexion){
    $sql = "SELECT ultima_fecha 
            FROM IMPORTAR 
            WHERE nombre = 'paquetes'";
    $res = mysqli_query($conexion, $sql);
    if(!$res){
        return 'Nunca';
    }
    $row = mysqli_fetch_assoc($res);
    return $row['ultima_fecha'] ?? 'Nunca';
}
