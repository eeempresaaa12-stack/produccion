<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

include("conexion.php");

mysqli_set_charset($conexion,"utf8mb4");

echo "Conexión OK <br>";

$url = "https://docs.google.com/spreadsheets/d/1CpdDtlNVQJoSHJ7WAc0BrXP-FIohYWhMB6si1bzmOz0/export?format=csv&gid=1327914009";

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

    $f = DateTime::createFromFormat('d/m/Y', trim($fecha));

    if($f){
        return $f->format('Y-m-d 00:00:00');
    }

    return null;
}

function convertirNumero($valor){

    $valor = trim($valor);

    if($valor === '' || $valor === null){
        return 0;
    }

    // quitar separador de miles
    $valor = str_replace('.', '', $valor);

    // convertir decimal latino a decimal estándar
    $valor = str_replace(',', '.', $valor);

    return (float)$valor;
}


/* =========================
CARGAR CATALOGOS
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
    $fecha = convertirFecha($data[1]);

    $maquina = limpiarNombre($data[2]);
    $referencia = limpiarNombre($data[3]);
    $color = limpiarNombre($data[4]);

    $turno = limpiarNombre($data[5]);

    $peso_rollo = convertirNumero($data[6]);
    $retal = convertirNumero($data[7]);
    $total_roll = 0;

    /* =========================
    BUSCAR IDS
    ========================= */

    $id_maquina = $maquinas[$maquina] ?? null;
    $id_referencia = $referencias[$referencia] ?? null;
    $id_color = $colores[$color] ?? null;
    $id_turno = $turnos[$turno] ?? null;

    /* =========================
    CREAR SI NO EXISTE
    ========================= */

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
    INSERT PRODUCCION ROLLO
    ========================= */

    $sql = "INSERT IGNORE INTO PRODUCCION_ROLLO
    (marca_temporal,fecha_roll,id_maquina,id_referencia,id_color,id_turno,peso_rollo,retal_roll)
    VALUES
    ('$marca','$fecha','$id_maquina','$id_referencia','$id_color','$id_turno','$peso_rollo','$retal')";

    mysqli_query($conexion,$sql);

    if(mysqli_affected_rows($conexion) > 0){
        echo "✅ Insertado REAL $contador <br>";
    }else{
        echo "⚠️ Ignorado duplicado $contador <br>";
    }

}

fclose($archivo);

echo "<br>🚀 Importación terminada";