<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

include("conexion.php");

mysqli_set_charset($conexion,"utf8mb4");

echo "Conexión OK <br>";

$url = "https://docs.google.com/spreadsheets/d/17gs1oTKRYY9S-qiU5ZMJCFrxe3K5r_pU1lgeZgUP8u8/export?format=csv&gid=399598423";

/* =========================
FUNCIONES
========================= */

function limpiarNombre($texto){

    $texto = trim($texto);

    if(strpos($texto,' - ') !== false){
        $partes = explode(' - ',$texto);
        return trim($partes[1]);
    }

    return $texto;
}

function convertirMarca($fecha){

    $fecha = trim($fecha);

    $f = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);

    if(!$f){
        $f = DateTime::createFromFormat('d/m/Y G:i:s', $fecha);
    }

    if($f){
        return $f->format('Y-m-d H:i:s');
    }

    return null;
}

function convertirFecha($fecha){

    $f = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);

    if($f){
        return $f->format('Y-m-d H:i:s');
    }

    return null;
}

/* =========================
CARGAR CATALOGOS A MEMORIA
========================= */

function cargarCatalogo($conexion,$tabla,$campo_nombre,$campo_id){

    $lista = [];

    $sql = "SELECT $campo_id,$campo_nombre FROM $tabla";
    $res = mysqli_query($conexion,$sql);

    while($row = mysqli_fetch_assoc($res)){
        $lista[trim($row[$campo_nombre])] = $row[$campo_id];
    }

    return $lista;
}

$operarios = cargarCatalogo($conexion,"OPERARIOS","nombre","id_operario");
$maquinas = cargarCatalogo($conexion,"MAQUINAS","nombre_maquina","id_maquina");
$referencias = cargarCatalogo($conexion,"REFERENCIAS","nombre_referencia","id_referencia");
$colores = cargarCatalogo($conexion,"COLORES","nombre_color","id_color");
$turnos = cargarCatalogo($conexion,"TURNOS","nombre_turno","id_turno");

/* =========================
ABRIR CSV
========================= */

$archivo = fopen($url,"r");

if(!$archivo){
    die("❌ No se pudo abrir el Google Sheet");
}

echo "Google Sheet abierto <br>";

$primera = true;
$contador = 0;

while(($data = fgetcsv($archivo,1000,",")) !== FALSE){

    if($primera){
        $primera = false;
        continue;
    }

    $contador++;

    $marca = convertirMarca($data[0]);
    $fecha = $data[1];
    $operario = limpiarNombre($data[2]);
    $maquina = limpiarNombre($data[3]);
    $referencia = limpiarNombre($data[4]);
    $color = limpiarNombre($data[5]);
    $turno = limpiarNombre($data[6]);
    $paquetes = (int)$data[7];
    $obs = mysqli_real_escape_string($conexion,$data[8]);

    $fecha_mysql = convertirFecha($fecha);

    /* =========================
    BUSCAR IDS EN MEMORIA
    ========================= */

    $id_operario = $operarios[$operario] ?? null;
    $id_maquina = $maquinas[$maquina] ?? null;
    $id_referencia = $referencias[$referencia] ?? null;
    $id_color = $colores[$color] ?? null;
    $id_turno = $turnos[$turno] ?? null;

    /* =========================
    SI NO EXISTE EN CATALOGO LO CREA
    ========================= */

    if(!$id_operario){
        mysqli_query($conexion,"INSERT INTO OPERARIOS(nombre) VALUES('$operario')");
        $id_operario = mysqli_insert_id($conexion);
        $operarios[$operario] = $id_operario;
    }

    if(!$id_maquina){
        mysqli_query($conexion,"INSERT INTO MAQUINAS(nombre_maquina) VALUES('$maquina')");
        $id_maquina = mysqli_insert_id($conexion);
        $maquinas[$maquina] = $id_maquina;
    }

    if(!$id_referencia){
        mysqli_query($conexion,"INSERT INTO REFERENCIAS(nombre_referencia) VALUES('$referencia')");
        $id_referencia = mysqli_insert_id($conexion);
        $referencias[$referencia] = $id_referencia;
    }

    if(!$id_color){
        mysqli_query($conexion,"INSERT INTO COLORES(nombre_color) VALUES('$color')");
        $id_color = mysqli_insert_id($conexion);
        $colores[$color] = $id_color;
    }

    if(!$id_turno){
        mysqli_query($conexion,"INSERT INTO TURNOS(nombre_turno) VALUES('$turno')");
        $id_turno = mysqli_insert_id($conexion);
        $turnos[$turno] = $id_turno;
    }

    /* =========================
    INSERT PRODUCCIÓN
    ========================= */

    $sql = "INSERT IGNORE INTO PRODUCCION_PAQUETES
    (marca_temporal,fecha_paq,id_operario,id_maquina,id_referencia,id_color,id_turno,paquetes_paq,observaciones_paq)
    VALUES
    ('$marca','$fecha_mysql','$id_operario','$id_maquina','$id_referencia','$id_color','$id_turno','$paquetes','$obs')";

    mysqli_query($conexion,$sql);

    if(mysqli_affected_rows($conexion) > 0){
        echo "✅ Insertado REAL $contador <br>";
    }else{
        echo "⚠️ Ignorado (duplicado) $contador <br>";
    }

}

fclose($archivo);

echo "<br>🚀 Proceso terminado";