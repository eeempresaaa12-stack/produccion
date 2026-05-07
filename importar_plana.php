<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

include("conexion.php");

mysqli_set_charset($conexion,"utf8mb4");

echo "Conexión OK <br>";

$url = "https://docs.google.com/spreadsheets/d/1DO_G6MHfoMagMMEOUOipTiE6W1UC-65f7BamJZQwGSc/export?format=csv&gid=201213148";

/* =========================
FUNCIONES
========================= */

function convertirNumero($valor){

    $valor = trim($valor);

    if($valor === '' || $valor === null){
        return 0;
    }

    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);

    return (float)$valor;
}

function convertirMarca($fecha){

    $f = DateTime::createFromFormat('d/m/Y H:i:s', trim($fecha));

    if($f){
        return $f->format('Y-m-d H:i:s');
    }

    return null;
}

function convertirFecha($fecha){

    $f = DateTime::createFromFormat('d/m/Y H:i:s', trim($fecha));

    if($f){
        return $f->format('Y-m-d H:i:s');
    }

    return null;
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

$operarios = cargarCatalogo($conexion,"OPERARIOS","nombre","id_operario");
$maquinas = cargarCatalogo($conexion,"MAQUINAS","nombre_maquina","id_maquina");
$referencias = cargarCatalogo($conexion,"REFERENCIAS","nombre_referencia","id_referencia");
$turnos = cargarCatalogo($conexion,"TURNOS","nombre_turno","id_turno");

/* =========================
ABRIR CSV
========================= */

$archivo = fopen($url,"r");

if(!$archivo){
    die("No se pudo abrir el Sheet");
}

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

    $operario = trim($data[2]);
    $maquina = trim($data[3]);
    $referencia = trim($data[4]);
    $turno = trim($data[5]);

    $peso = convertirNumero($data[6]);
    $bultos = (int)$data[7];
    $retal = convertirNumero($data[8]);
    $total = convertirNumero($data[9]);

    /* =========================
    IDS
    ========================= */

    $id_operario = $operarios[$operario] ?? null;
    $id_maquina = $maquinas[$maquina] ?? null;
    $id_referencia = $referencias[$referencia] ?? null;
    $id_turno = $turnos[$turno] ?? null;

    /* =========================
    CREAR SI NO EXISTE
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

    if(!$id_turno){
        mysqli_query($conexion,"INSERT INTO TURNOS(nombre_turno) VALUES('$turno')");
        $id_turno = mysqli_insert_id($conexion);
        $turnos[$turno] = $id_turno;
    }

    /* =========================
    INSERT
    ========================= */

    $sql = "INSERT IGNORE INTO PRODUCCION_PLANA
    (marca_temporal,fecha_plana,id_operario,id_maquina,id_referencia,id_turno,peso_plana,bultos_plana,retal_plana,total_plana)
    VALUES
    ('$marca','$fecha','$id_operario','$id_maquina','$id_referencia','$id_turno','$peso','$bultos','$retal','$total')";

    mysqli_query($conexion,$sql);

    if(mysqli_affected_rows($conexion) > 0){
        echo "✅ Insertado REAL $contador <br>";
    }else{
        echo "⚠️ Ignorado duplicado $contador <br>";
    }

}

fclose($archivo);

echo "<br>🚀 Importación terminada";