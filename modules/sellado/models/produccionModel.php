<?php
/* =================================================
   CONSULTAS
================================================= */
function obtenerProduccionPorId($conexion, $id){
    $sql = "SELECT * 
            FROM PRODUCCION_PAQUETES 
            WHERE id = $id";
    $res = mysqli_query($conexion, $sql);
    return mysqli_fetch_assoc($res);
}

function obtenerOperarios($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM OPERARIOS"
    );
}

function obtenerMaquinas($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM MAQUINAS"
    );
}

function obtenerReferencias($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM REFERENCIAS"
    );
}

function obtenerColores($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM COLORES"
    );
}

function obtenerTurnos($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM TURNOS"
    );
}

/* ACTUALIZAR */
function actualizarProduccion($conexion, $id, $datos){
    $sql = "UPDATE PRODUCCION_PAQUETES SET
    fecha_paq='{$datos['fecha']}',
    id_operario='{$datos['id_operario']}',
    id_maquina='{$datos['id_maquina']}',
    id_referencia='{$datos['id_referencia']}',
    id_color='{$datos['id_color']}',
    id_turno='{$datos['id_turno']}',
    paquetes_paq='{$datos['paquetes']}',
    observaciones_paq='{$datos['observaciones']}'
    WHERE id=$id";
    mysqli_query($conexion, $sql);
}

/* ELIMINAR */
function eliminarProduccion($conexion, $id){
    $sql = "DELETE FROM PRODUCCION_PAQUETES WHERE id = $id";
    mysqli_query($conexion, $sql);
}