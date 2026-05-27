<?php
/* =================================================
   CONSULTAS
================================================= */
function obtenerTotalRollo($conexion, $sql){
    $res = mysqli_query($conexion, $sql);
    if(!$res){
        return 0;
    }
    $row = mysqli_fetch_assoc($res);
    return $row['total'] ?? 0;
}

/* TOTAL HISTORICO */
function obtenerTotalHistoricoRollo($conexion){
    $sql = "SELECT SUM(total_roll) total
            FROM PRODUCCION_ROLLO";
    return obtenerTotalRollo($conexion, $sql);
}

/* PRODUCCION SEMANA */
function obtenerProduccionSemanaRollo($conexion){
    $sql = "SELECT SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE YEARWEEK(fecha_roll,1)=YEARWEEK(CURDATE(),1)";
    return obtenerTotalRollo($conexion, $sql);
}

/* PRODUCCION MES */
function obtenerProduccionMesRollo($conexion){
    $sql = "SELECT SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE())
            AND YEAR(fecha_roll)=YEAR(CURDATE())";
    return obtenerTotalRollo($conexion, $sql);
}

/* TOP MAQUINA MES ACTUAL */
function obtenerTopMaquinaRollo($conexion){
    $sql = "SELECT 
                m.nombre_maquina,
                IFNULL(SUM(r.total_roll),0) total
            FROM PRODUCCION_ROLLO r
            LEFT JOIN MAQUINAS m
                ON r.id_maquina = m.id_maquina
            WHERE MONTH(r.fecha_roll)=MONTH(CURDATE())
            AND YEAR(r.fecha_roll)=YEAR(CURDATE())
            GROUP BY r.id_maquina
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        "nombre_maquina" => "Sin datos",
        "total" => 0
    ];
}

/* TOP MAQUINA MES ANTERIOR */
function obtenerTopMaquinaAnteriorRollo($conexion){
    $sql = "SELECT 
                m.nombre_maquina,
                IFNULL(SUM(r.total_roll),0) total
            FROM PRODUCCION_ROLLO r
            LEFT JOIN MAQUINAS m
                ON r.id_maquina = m.id_maquina
            WHERE MONTH(r.fecha_roll)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(r.fecha_roll)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY r.id_maquina
            ORDER BY total DESC
            LIMIT 1";
    $res = mysqli_query($conexion, $sql);
    if($res && mysqli_num_rows($res) > 0){
        return mysqli_fetch_assoc($res);
    }
    return [
        "nombre_maquina" => "Sin datos",
        "total" => 0
    ];
}

/* TOTAL MES ACTUAL Y ANTERIOR */
function obtenerTotalMesActualRollo($conexion){
    $sql = "SELECT SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE())
            AND YEAR(fecha_roll)=YEAR(CURDATE())";
    return obtenerTotalRollo($conexion, $sql);
}
function obtenerTotalMesAnteriorRollo($conexion){
    $sql = "SELECT SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_roll)=YEAR(CURDATE()-INTERVAL 1 MONTH)";
    return obtenerTotalRollo($conexion, $sql);
}

/* RESUMEN MES ACTUAL */
function obtenerResumenMesRollo($conexion){
    $sql = "SELECT SUM(peso_rollo) bruto, SUM(retal_roll) retal, SUM(total_roll) neto
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE())
            AND YEAR(fecha_roll)=YEAR(CURDATE())";
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
function obtenerResumenMesAnteriorRollo($conexion){
    $sql = "SELECT SUM(peso_rollo) bruto, SUM(retal_roll) retal, SUM(total_roll) neto
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_roll)=YEAR(CURDATE()-INTERVAL 1 MONTH)";
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
function obtenerMejorPeorDiaMesRollo($conexion){
    $sql = "SELECT 
                DATE(fecha_roll) fecha,
                SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE())
            AND YEAR(fecha_roll)=YEAR(CURDATE())
            GROUP BY DATE(fecha_roll)";
    $res = mysqli_query($conexion, $sql);
    $mejor = null;
    $peor = null;
    if($res && mysqli_num_rows($res) > 0){
        while($row = mysqli_fetch_assoc($res)){
            if(!$mejor || $row['total'] > $mejor['total']){
                $mejor = $row;
            }
            if(!$peor || $row['total'] < $peor['total']){
                $peor = $row;
            }
        }
    }else{
        $mejor = [
            'fecha' => 'Sin datos',
            'total' => 0
        ];
        $peor = [
            'fecha' => 'Sin datos',
            'total' => 0
        ];
    }
    return [
        'mejor' => $mejor,
        'peor' => $peor
    ];
}

/* MEJOR Y PEOR DIA MES ANTERIOR */
function obtenerMejorPeorDiaAnteriorRollo($conexion){
    $sql = "SELECT 
                DATE(fecha_roll) fecha,
                SUM(total_roll) total
            FROM PRODUCCION_ROLLO
            WHERE MONTH(fecha_roll)=MONTH(CURDATE()-INTERVAL 1 MONTH)
            AND YEAR(fecha_roll)=YEAR(CURDATE()-INTERVAL 1 MONTH)
            GROUP BY DATE(fecha_roll)";
    $res = mysqli_query($conexion, $sql);
    $mejor = null;
    $peor = null;
    if($res && mysqli_num_rows($res) > 0){
        while($row = mysqli_fetch_assoc($res)){
            if(!$mejor || $row['total'] > $mejor['total']){
                $mejor = $row;
            }
            if(!$peor || $row['total'] < $peor['total']){
                $peor = $row;
            }
        }
    }else{
        $mejor = [
            'fecha' => 'Sin datos',
            'total' => 0
        ];
        $peor = [
            'fecha' => 'Sin datos',
            'total' => 0
        ];
    }
    return [
        'mejor' => $mejor,
        'peor' => $peor
    ];
}

/* TABLAS */
function obtenerTablaFechasRollo($conexion, $desde, $hasta){
    $sql = "SELECT 
                DATE(p.fecha_roll) fecha,
                SUM(p.peso_rollo) bruto,
                SUM(p.retal_roll) retal,
                SUM(p.total_roll) neto
            FROM PRODUCCION_ROLLO p
            WHERE DATE(p.fecha_roll)
                BETWEEN '$desde' AND '$hasta'
            GROUP BY DATE(p.fecha_roll)
            ORDER BY fecha DESC";
    return mysqli_query($conexion, $sql);
}
function obtenerTablaMaquinasRollo($conexion, $desde, $hasta){
    $sql = "SELECT 
                m.nombre_maquina,
                SUM(p.peso_rollo) bruto,
                SUM(p.retal_roll) retal,
                SUM(p.total_roll) neto
            FROM PRODUCCION_ROLLO p
            LEFT JOIN MAQUINAS m
                ON p.id_maquina = m.id_maquina
            WHERE DATE(p.fecha_roll)
                BETWEEN '$desde' AND '$hasta'
            GROUP BY m.id_maquina, m.nombre_maquina
            ORDER BY neto DESC";
    return mysqli_query($conexion, $sql);
}

/* ULTIMA IMPORTACION */
function obtenerUltimaImportacionRollo($conexion){
    $sql = "SELECT ultima_fecha
            FROM IMPORTAR
            WHERE nombre = 'rollo'";
    $res = mysqli_query($conexion, $sql);
    if(!$res){
        return 'Nunca';
    }
    $row = mysqli_fetch_assoc($res);
    return $row['ultima_fecha'] ?? 'Nunca';
}
