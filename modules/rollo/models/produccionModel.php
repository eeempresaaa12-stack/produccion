<?php
/* =================================================
   CONSULTAS
================================================= */
// Obtener registro de producción por ID
function obtenerProduccionPorId($conexion, $id){
    $sql = "SELECT * FROM PRODUCCION_ROLLO WHERE id = $id";
    $res = mysqli_query($conexion, $sql);
    return mysqli_fetch_assoc($res);
}

// Catálogos para selectores del formulario
function obtenerMaquinas($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM MAQUINAS"
    );
}
function obtenerTurnos($conexion){
    return mysqli_query($conexion,
        "SELECT * FROM TURNOS"
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

/* =================================================
   ACTUALIZAR
================================================= */
// Actualizar registro de producción por ID
function actualizarProduccion($conexion, $id, $datos){
    $sql = "UPDATE PRODUCCION_ROLLO SET
            fecha_roll='{$datos['fecha']}',
            id_maquina='{$datos['id_maquina']}',
            id_turno='{$datos['id_turno']}',
            id_referencia='{$datos['id_referencia']}',
            id_color='{$datos['id_color']}',
            peso_rollo='{$datos['peso']}',
            retal_roll='{$datos['retal']}',
            total_roll='{$datos['total']}'
            WHERE id=$id";
    mysqli_query($conexion, $sql);
}

/* =================================================
   ELIMINAR
================================================= */
// Eliminar registro de producción por ID
function eliminarProduccion($conexion, $id){
    $sql = "DELETE FROM PRODUCCION_ROLLO 
            WHERE id = $id";
    mysqli_query($conexion, $sql);
}
