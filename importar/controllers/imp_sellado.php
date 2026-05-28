<?php
/** @var mysqli $conexion */

// Importar conexion.php 
require_once("../../includes/conexion.php");
mysqli_set_charset($conexion, "utf8mb4");

// Importar importarModel.php
require_once("../importarModel.php");

// Parametros de importación
$modo         = $_GET['modo'] ?? 'nuevos';
$ultima_fecha = obtenerUltimaFecha($conexion, 'paquetes');

// Fuente de datos
$url        = "https://docs.google.com/spreadsheets/d/17gs1oTKRYY9S-qiU5ZMJCFrxe3K5r_pU1lgeZgUP8u8/export?format=csv&gid=399598423";
$titulo     = "Producción de Paquetes";
$subtitulo  = "PRODUCCION_PAQUETES";
$volver_url = "../../modules/paquetes/views/dashboard.php";

// Leer filas del Google Sheet
[$filas, $omitidas] = leerSheet($url, $modo, $ultima_fecha);
$total = count($filas);

// Importar progreso.php
include("../views/progreso.php");
?>

<?php
// Sin registros nuevos
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

// Actualizar barra y conexión exitosa
echo "<script>
    document.getElementById('dot').className           = 'dot';
    document.getElementById('msg').textContent         = 'Conexión OK · $total registros encontrados';
    document.getElementById('fill').style.width        = '5%';
    document.getElementById('pct').textContent         = '5';
    document.getElementById('status-lbl').textContent  = 'Procesando';
</script>\n";
if (ob_get_level()) ob_flush(); flush();

// Cargar catálogos desde la base de datos
echo "<script>document.getElementById('msg').textContent='Cargando catálogos…';</script>\n";
if (ob_get_level()) ob_flush(); flush();

$operarios   = cargarCatalogo($conexion, "OPERARIOS",   "nombre",            "id_operario");
$maquinas    = cargarCatalogo($conexion, "MAQUINAS",    "nombre_maquina",    "id_maquina");
$referencias = cargarCatalogo($conexion, "REFERENCIAS", "nombre_referencia", "id_referencia");
$colores     = cargarCatalogo($conexion, "COLORES",     "nombre_color",      "id_color");
$turnos      = cargarCatalogo($conexion, "TURNOS",      "nombre_turno",      "id_turno");

// Contadores de resultado
$contador     = 0;
$insertados   = 0;
$actualizados = 0;
$duplicados   = 0;
$nueva_fecha  = null;

foreach ($filas as $data) {
    // Limpiar y convertir datos de cada fila
    $marca      = convertirMarca($data[0]);
    $fecha      = convertirFecha($data[1]);
    $operario   = limpiarNombre($data[2]);
    $maquina    = limpiarNombre($data[3]);
    $referencia = limpiarNombre($data[4]);
    $color      = limpiarNombre($data[5]);
    $turno      = limpiarNombre($data[6]);
    $paquetes   = (int)$data[7];
    $obs        = mysqli_real_escape_string($conexion, $data[8]);

    // Obtener IDs de catálogos o crearlos si no existen
    $id_operario   = $operarios[$operario]     ?? autoCrear($conexion, $operarios,   "OPERARIOS",   "nombre",            $operario);
    $id_maquina    = $maquinas[$maquina]       ?? autoCrear($conexion, $maquinas,    "MAQUINAS",    "nombre_maquina",    $maquina);
    $id_referencia = $referencias[$referencia] ?? autoCrear($conexion, $referencias, "REFERENCIAS", "nombre_referencia", $referencia);
    $id_color      = $colores[$color]          ?? autoCrear($conexion, $colores,     "COLORES",     "nombre_color",      $color);
    $id_turno      = $turnos[$turno]           ?? autoCrear($conexion, $turnos,      "TURNOS",      "nombre_turno",      $turno);

    // Modo 'todo': Insertar o actualizar si ya existe
    if ($modo === 'todo') {
        $sql = "INSERT INTO PRODUCCION_PAQUETES
                    (marca_temporal,fecha_paq,id_operario,id_maquina,id_referencia,
                    id_color,id_turno,paquetes_paq,observaciones_paq)
                VALUES
                    ('$marca','$fecha','$id_operario','$id_maquina','$id_referencia',
                    '$id_color','$id_turno','$paquetes','$obs')
                ON DUPLICATE KEY UPDATE
                    fecha_paq         = VALUES(fecha_paq),
                    id_operario       = VALUES(id_operario),
                    id_maquina        = VALUES(id_maquina),
                    id_referencia     = VALUES(id_referencia),
                    id_color          = VALUES(id_color),
                    id_turno          = VALUES(id_turno),
                    paquetes_paq      = VALUES(paquetes_paq),
                    observaciones_paq = VALUES(observaciones_paq)";
    // Modo 'nuevos': Insertar solo si no existe
    } else {
        $sql = "INSERT IGNORE INTO PRODUCCION_PAQUETES
                    (marca_temporal,fecha_paq,id_operario,id_maquina,id_referencia,
                    id_color,id_turno,paquetes_paq,observaciones_paq)
                VALUES
                    ('$marca','$fecha','$id_operario','$id_maquina','$id_referencia',
                    '$id_color','$id_turno','$paquetes','$obs')";
    }
    // Ejecutar inserción y actualizar progreso
    procesarFila($conexion, $sql, $marca, $contador, $total, $insertados, $actualizados, $duplicados, $nueva_fecha);
}

// Al finalizar: Mostrar contadores y guardar última fecha
finalizarImportacion($conexion, 'paquetes', $nueva_fecha, $insertados, $actualizados, $duplicados, $total);
?>