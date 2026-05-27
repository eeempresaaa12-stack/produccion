<?php
/* =================================================
   CONSULTAS
================================================= */
function obtenerTotalPlana($conexion, $sql){
    $res = mysqli_query($conexion, $sql);
    if(!$res){
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return $row['total'] ?? 0;
}

/* TOTAL HISTORICO */
function obtenerTotalHistoricoPlana($conexion){
    $sql = "SELECT SUM(total_plana) total FROM PRODUCCION_PLANA";
    return obtenerTotalPlana($conexion, $sql);
}

/* PRODUCCION SEMANA */
function obtenerProduccionSemanaPlana($conexion){
    $sql = "SELECT SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE YEARWEEK(fecha_plana, 1) = YEARWEEK(CURDATE(), 1)";
    return obtenerTotalPlana($conexion, $sql);
}

/* PRODUCCION MES */
function obtenerProduccionMesPlana($conexion){
    $sql = "SELECT SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana) = MONTH(CURDATE())
            AND YEAR(fecha_plana) = YEAR(CURDATE())";
    return obtenerTotalPlana($conexion, $sql);
}

/* TOP OPERARIO MES ACTUAL */
function obtenerTopOperarioPlana($conexion){
    $sql = "SELECT o.nombre, IFNULL(SUM(p.bultos_plana),0) total
            FROM PRODUCCION_PLANA p
            LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
            WHERE MONTH(p.fecha_plana)=MONTH(CURDATE())
            AND YEAR(p.fecha_plana)=YEAR(CURDATE())
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        'nombre' => 'Sin datos',
        'total' => 0
    ];
}

/* TOP OPERARIO MES ANTERIOR */
function obtenerTopOperarioAnteriorPlana($conexion){
    $sql = "SELECT o.nombre, IFNULL(SUM(p.bultos_plana),0) total
            FROM PRODUCCION_PLANA p
            LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
            WHERE MONTH(p.fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(p.fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY p.id_operario
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        'nombre' => 'Sin datos',
        'total' => 0
    ];
}

/* TOTAL MES ACTUAL Y ANTERIOR */
function obtenerTotalMesActualPlana($conexion){
    $sql = "SELECT SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE())
            AND YEAR(fecha_plana)=YEAR(CURDATE())";
    return obtenerTotalPlana($conexion, $sql);
}
function obtenerTotalMesAnteriorPlana($conexion){
    $sql = "SELECT SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)";
    return obtenerTotalPlana($conexion, $sql);
}

/* RESUMEN MES ACTUAL */
function obtenerResumenMesPlana($conexion){
    $sql = "SELECT SUM(peso_plana) bruto, SUM(retal_plana) retal, SUM(total_plana) neto
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE())
            AND YEAR(fecha_plana)=YEAR(CURDATE())";
    $res = mysqli_query($conexion, $sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return [
            'bruto' => $row['bruto'] ?? null,
            'retal' => $row['retal'] ?? null,
            'neto'  => $row['neto']  ?? null
        ];
    }
    return ['bruto' => null, 'retal' => null, 'neto' => null];
}

/* RESUMEN MES ANTERIOR */
function obtenerResumenMesAnteriorPlana($conexion){
    $sql = "SELECT SUM(peso_plana) bruto, SUM(retal_plana) retal, SUM(total_plana) neto
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)";
    $res = mysqli_query($conexion, $sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return [
            'bruto' => $row['bruto'] ?? null,
            'retal' => $row['retal'] ?? null,
            'neto'  => $row['neto']  ?? null
        ];
    }
    return ['bruto' => null, 'retal' => null, 'neto' => null];
}

/* MEJOR Y PEOR DIA MES ACTUAL*/
function obtenerMejorPeorDiaMesPlana($conexion){
    $sql = "SELECT DATE(fecha_plana) fecha, SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE())
            AND YEAR(fecha_plana)=YEAR(CURDATE())
            GROUP BY DATE(fecha_plana)";
    $res = mysqli_query($conexion, $sql);
    $mejor = ['fecha' => 'Sin datos', 'total' => 0];
    $peor = ['fecha' => 'Sin datos', 'total' => 0];
    if($res && mysqli_num_rows($res) > 0){
        while($row = mysqli_fetch_assoc($res)){
            if($row['total'] > $mejor['total']){
                $mejor = $row;
            }
            if($peor['fecha'] === 'Sin datos' || $row['total'] < $peor['total']){
                $peor = $row;
            }
        }
    }
    return [
        'mejor' => $mejor,
        'peor' => $peor
    ];
}

/* MEJOR Y PEOR DIA MES ANTERIOR */
function obtenerMejorPeorDiaAnteriorPlana($conexion){
    $sql = "SELECT DATE(fecha_plana) fecha, SUM(total_plana) total
            FROM PRODUCCION_PLANA
            WHERE MONTH(fecha_plana)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_plana)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY DATE(fecha_plana)";
    $res = mysqli_query($conexion, $sql);
    $mejor = ['fecha' => 'Sin datos', 'total' => 0];
    $peor = ['fecha' => 'Sin datos', 'total' => 0];
    if($res && mysqli_num_rows($res) > 0){
        while($row = mysqli_fetch_assoc($res)){
            if($row['total'] > $mejor['total']){
                $mejor = $row;
            }
            if($peor['fecha'] === 'Sin datos' || $row['total'] < $peor['total']){
                $peor = $row;
            }
        }
    }
    return [
        'mejor' => $mejor,
        'peor' => $peor
    ];
}

/* TABLAS */
function obtenerTablaFechasPlana($conexion, $desde, $hasta){
    $sql = "SELECT DATE(fecha_plana) fecha, SUM(peso_plana) bruto, SUM(bultos_plana) bultos, SUM(retal_plana) retal, SUM(total_plana) neto
            FROM PRODUCCION_PLANA
            WHERE DATE(fecha_plana) BETWEEN '$desde' AND '$hasta'
            GROUP BY DATE(fecha_plana)
            ORDER BY fecha DESC";
    return mysqli_query($conexion, $sql);
}
function obtenerTablaReferenciasPlana($conexion, $desde, $hasta){
    $sql = "SELECT r.nombre_referencia, SUM(p.peso_plana) bruto, SUM(p.bultos_plana) bultos, SUM(p.retal_plana) retal, SUM(p.total_plana) neto
            FROM PRODUCCION_PLANA p
            LEFT JOIN REFERENCIAS r ON p.id_referencia = r.id_referencia
            WHERE DATE(p.fecha_plana) BETWEEN '$desde' AND '$hasta'
            GROUP BY r.id_referencia, r.nombre_referencia
            ORDER BY neto DESC";
    return mysqli_query($conexion, $sql);
}
function obtenerTablaMaquinasPlana($conexion, $desde, $hasta){
    $sql = "SELECT m.nombre_maquina, SUM(p.peso_plana) bruto, SUM(p.bultos_plana) bultos, SUM(p.retal_plana) retal, SUM(p.total_plana) neto
            FROM PRODUCCION_PLANA p
            LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
            WHERE DATE(p.fecha_plana) BETWEEN '$desde' AND '$hasta'
            GROUP BY m.id_maquina, m.nombre_maquina
            ORDER BY neto DESC";
    return mysqli_query($conexion, $sql);
}

/* ULTIMA IMPORTACION */
function obtenerUltimaImportacionPlana($conexion){
    $sql = "SELECT ultima_fecha FROM IMPORTAR WHERE nombre = 'plana'";
    $res = mysqli_query($conexion, $sql);
    if(!$res){
        return 'Nunca';
    }
    $row = mysqli_fetch_assoc($res);
    return $row['ultima_fecha'] ?? 'Nunca';
}
