<?php
/** @var mysqli $conexion */

require_once("../../includes/conexion.php");
require_once("../importarModel.php");

mysqli_set_charset($conexion, "utf8mb4");

$modo         = $_GET['modo'] ?? 'nuevos';
$ultima_fecha = obtenerUltimaFecha($conexion, 'rollo');

$url        = "https://docs.google.com/spreadsheets/d/1CpdDtlNVQJoSHJ7WAc0BrXP-FIohYWhMB6si1bzmOz0/export?format=csv&gid=1327914009";
$titulo     = "Producción de Rollos";
$subtitulo  = "PRODUCCION_ROLLO";
$volver_url = "../../modules/rollo/views/dashboard.php";

[$filas, $omitidas] = leerSheet($url, $modo, $ultima_fecha);
$total = count($filas);

include("../views/progreso.php");
?>

<?php
// Sin filas nuevas
if ($total === 0 && $modo === 'nuevos') {
    echo "<script>
        document.getElementById('msg').textContent        = 'No hay registros nuevos...';
        document.getElementById('dot').className          = 'dot done';
        document.getElementById('status-lbl').textContent = 'Completado';
        document.getElementById('fill').style.width       = '100%';
        document.getElementById('pct').textContent        = '100';
        document.getElementById('counter').textContent    = '$omitidas registros ya importados';
        document.getElementById('btn-volver').classList.add('show');
    </script>";
    if (ob_get_level()) ob_flush(); flush();
    echo "</body></html>"; exit;
}

// Conexión OK
echo "<script>
    document.getElementById('dot').className           = 'dot';
    document.getElementById('msg').textContent         = 'Conexión OK · $total registros encontrados';
    document.getElementById('fill').style.width        = '5%';
    document.getElementById('pct').textContent         = '5';
    document.getElementById('status-lbl').textContent  = 'Procesando';
</script>\n";
if (ob_get_level()) ob_flush(); flush();

/* =====================
   CATÁLOGOS
===================== */
echo "<script>document.getElementById('msg').textContent='Cargando catálogos…';</script>\n";
if (ob_get_level()) ob_flush(); flush();

$maquinas    = cargarCatalogo($conexion, "MAQUINAS",    "nombre_maquina",    "id_maquina");
$referencias = cargarCatalogo($conexion, "REFERENCIAS", "nombre_referencia", "id_referencia");
$colores     = cargarCatalogo($conexion, "COLORES",     "nombre_color",      "id_color");
$turnos      = cargarCatalogo($conexion, "TURNOS",      "nombre_turno",      "id_turno");

/* =====================
   PROCESAR FILAS
===================== */
$contador     = 0;
$insertados   = 0;
$actualizados = 0;
$duplicados   = 0;
$nueva_fecha  = null;

foreach ($filas as $data) {

    $marca      = convertirMarca($data[0]);
    $fecha      = convertirFecha($data[1]) ?? $marca;
    $maquina    = limpiarNombre($data[2]);
    $referencia = limpiarNombre($data[3]);
    $color      = limpiarNombre($data[4]);
    $turno      = limpiarNombre($data[5]);
    $peso_rollo = convertirNumero($data[6]);
    $retal      = convertirNumero($data[7]);

    $id_maquina    = $maquinas[$maquina]       ?? autoCrear($conexion, $maquinas,    "MAQUINAS",    "nombre_maquina",    $maquina);
    $id_referencia = $referencias[$referencia] ?? autoCrear($conexion, $referencias, "REFERENCIAS", "nombre_referencia", $referencia);
    $id_color      = $colores[$color]          ?? autoCrear($conexion, $colores,     "COLORES",     "nombre_color",      $color);
    $id_turno      = $turnos[$turno]           ?? autoCrear($conexion, $turnos,      "TURNOS",      "nombre_turno",      $turno);

    if ($modo === 'todo') {
        $sql = "INSERT INTO PRODUCCION_ROLLO
                    (marca_temporal,fecha_roll,id_maquina,id_referencia,
                    id_color,id_turno,peso_rollo,retal_roll)
                VALUES
                    ('$marca','$fecha','$id_maquina','$id_referencia',
                    '$id_color','$id_turno','$peso_rollo','$retal')
                ON DUPLICATE KEY UPDATE
                    fecha_roll    = VALUES(fecha_roll),
                    id_maquina    = VALUES(id_maquina),
                    id_referencia = VALUES(id_referencia),
                    id_color      = VALUES(id_color),
                    id_turno      = VALUES(id_turno),
                    peso_rollo    = VALUES(peso_rollo),
                    retal_roll    = VALUES(retal_roll)";
    } else {
        $sql = "INSERT IGNORE INTO PRODUCCION_ROLLO
                    (marca_temporal,fecha_roll,id_maquina,id_referencia,
                    id_color,id_turno,peso_rollo,retal_roll)
                VALUES
                    ('$marca','$fecha','$id_maquina','$id_referencia',
                    '$id_color','$id_turno','$peso_rollo','$retal')";
    }

    procesarFila($conexion, $sql, $marca, $contador, $total, $insertados, $actualizados, $duplicados, $nueva_fecha);
}

/* =====================
   FINALIZAR
===================== */
finalizarImportacion($conexion, 'rollo', $nueva_fecha, $insertados, $actualizados, $duplicados, $total);
?>

</body>
</html>