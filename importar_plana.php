<?php

ini_set('memory_limit', '512M');
set_time_limit(0);

// Desactivo de Compresion
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('output_buffering', 0);

include("conexion.php");
mysqli_set_charset($conexion,"utf8mb4");

define('PROGRESS_FILE', __DIR__ . '/importar_paquetes_progress.json');
define('DB_HOST', 'switchyard.proxy.rlwy.net');
define('DB_USER', 'root');
define('DB_PASS', 'EagzBrYIJHawioQrQhpqbjYAxXFhMwUU');
define('DB_NAME', 'CONTROL_PRODUCCION');
define('DB_PORT', 22573);

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
    return $f ? $f->format('Y-m-d H:i:s') : null;
}

function convertirFecha($fecha){
    $f = DateTime::createFromFormat('d/m/Y H:i:s', trim($fecha));
    return $f ? $f->format('Y-m-d H:i:s') : null;
}

function cargarCatalogo($conexion, $tabla, $campo_nombre, $campo_id) {
    $lista = [];
    $res = mysqli_query($conexion, "SELECT $campo_id,$campo_nombre FROM $tabla");
    while ($row = mysqli_fetch_assoc($res))
        $lista[trim($row[$campo_nombre])] = $row[$campo_id];
    return $lista;
}
// Enviar actualizacion de progreso al JS en tiempo real
function sendProgress($pct, $msg, $extra = '') {
    $msg = addslashes($msg);
    echo "<script>up($pct,'$msg'$extra);</script>\n";
    if (ob_get_level()) ob_flush();
    flush();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Importando Maquina Plana</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="./css/estilos_importar.css"> 

</head>
<body>

<div class="wrapper">

  <div class="top">
    <div>
      <h1>Producción de Maquina Plana</h1>
      <p>Google Sheets &rarr; MySQL &middot; PRODUCCION_PLANA</p>
    </div>
  </div>

  <div class="card">
    <div class="status-row">
      <span class="dot idle" id="dot"></span>
      <span id="status-lbl">Iniciando</span>
    </div>

    <div id="msg">Preparando importación…</div>

    <div class="track"><div id="fill"></div></div>

    <div class="num-row">
      <div class="pct-wrap">
        <span id="pct">0</span><span class="sign">%</span>
      </div>
      <span id="counter">— / — registros</span>
    </div>

    <div id="stats">
      <div class="st ok">
        <div class="v" id="s-ok">0</div>
        <div class="l">Insertados</div>
      </div>
      <div class="st upd">
        <div class="v" id="s-upd">0</div>
        <div class="l">Actualizados</div>
      </div>
      <div class="st dup">
        <div class="v" id="s-dup">0</div>
        <div class="l">Duplicados</div>
      </div>
      <div class="st tot">
        <div class="v" id="s-tot">0</div>
        <div class="l">Total</div>
      </div>
    </div>

    <button id="toggle-btn" onclick="toggleLog()">
      Ver detalle de registros
      <svg width="11" height="11" viewBox="0 0 12 12" fill="none">
        <path d="M2 4l4 4 4-4" stroke="currentColor" stroke-width="1.6"
              stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>

    <div id="log"></div>
  </div>

</div>

<script>
function up(pct, msg) {
  document.getElementById('fill').style.width   = pct + '%';
  document.getElementById('pct').textContent    = pct;
  document.getElementById('msg').textContent    = msg;
  document.getElementById('dot').className      = 'dot';       
  document.getElementById('status-lbl').textContent = 'Procesando';
}

function tick(cur, totalp, ok, upd, dup, msgLog, type) {
  document.getElementById('s-ok').textContent  = ok;
  document.getElementById('s-upd').textContent = upd;
  document.getElementById('s-dup').textContent = dup;
  document.getElementById('counter').textContent = cur + ' / ' + totalp + ' registros';
  var pct = 8 + Math.round((cur / totalp) * 90);
  document.getElementById('fill').style.width = pct + '%';
  document.getElementById('pct').textContent  = pct;
  document.getElementById('msg').textContent  =
    'Importando \u2026 (' + cur + '\u202f/\u202f' + totalp + ')';
  // Log
  var row = document.createElement('div');
  row.className = 'lr ' + type;
  row.innerHTML = '<span class="n">' + cur + '</span>'
                + '<span class="t">' + msgLog + '</span>';
  var log = document.getElementById('log');
  log.appendChild(row);
  log.scrollTop = log.scrollHeight;
}

function done(ok, upd, dup, totalp) {
  document.getElementById('fill').style.width = '100%';
  document.getElementById('pct').textContent  = '100';
  document.getElementById('msg').textContent  = 'Importación completada exitosamente';
  document.getElementById('dot').className    = 'dot done';
  document.getElementById('status-lbl').textContent = 'Completado';
  document.getElementById('s-ok').textContent  = ok;
  document.getElementById('s-upd').textContent = upd;
  document.getElementById('s-dup').textContent = dup;
  document.getElementById('s-tot').textContent = totalp;
  document.getElementById('stats').classList.add('show');
  document.getElementById('toggle-btn').classList.add('show');
}

function toggleLog() {
  document.getElementById('toggle-btn').classList.toggle('open');
  document.getElementById('log').classList.toggle('open');
}
</script>

<?php
$url = "https://docs.google.com/spreadsheets/d/1DO_G6MHfoMagMMEOUOipTiE6W1UC-65f7BamJZQwGSc/export?format=csv&gid=201213148";

$archivo = fopen($url,"r");
if (!$archivo) {
    echo "<script>
      document.getElementById('msg').textContent = 'Error: no se pudo abrir el Google Sheet';
      document.getElementById('dot').className = 'dot error';
      document.getElementById('status-lbl').textContent = 'Error';
    </script>";
    echo "</body></html>"; exit;
}

$filas = []; $primera = true;
while (($data = fgetcsv($archivo, 1000, ",")) !== FALSE) {
    if ($primera) { $primera = false; continue; }
    $filas[] = $data;
}
fclose($archivo);
$totalp = count($filas);

// Actualizar UI con total encontrado
echo "<script>
  document.getElementById('dot').className = 'dot';
  document.getElementById('msg').textContent = 'Conexión OK · $totalp registros encontrados';
  document.getElementById('fill').style.width = '5%';
  document.getElementById('pct').textContent = '5';
  document.getElementById('status-lbl').textContent = 'Procesando';
</script>\n";
if (ob_get_level()) ob_flush(); flush();

/* =====================
   CATÁLOGOS
===================== */
echo "<script>document.getElementById('msg').textContent='Cargando catálogos…';</script>\n";
if (ob_get_level()) ob_flush(); flush();

$operarios = cargarCatalogo($conexion,"OPERARIOS","nombre","id_operario");
$maquinas = cargarCatalogo($conexion,"MAQUINAS","nombre_maquina","id_maquina");
$referencias = cargarCatalogo($conexion,"REFERENCIAS","nombre_referencia","id_referencia");
$turnos = cargarCatalogo($conexion,"TURNOS","nombre_turno","id_turno");

/* =====================
   PROCESAR FILAS
===================== */
$contador   = 0;
$insertados = 0;
$actualizados = 0;
$duplicados = 0;

foreach ($filas as $data) {
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

    $id_operario = $operarios[$operario] ?? null;
    $id_maquina = $maquinas[$maquina] ?? null;
    $id_referencia = $referencias[$referencia] ?? null;
    $id_turno = $turnos[$turno] ?? null;

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

    $sql = "INSERT IGNORE INTO PRODUCCION_PLANA
        (marca_temporal,fecha_plana,id_operario,id_maquina,id_referencia,
        id_turno,peso_plana,bultos_plana,retal_plana,total_plana)
          VALUES ('$marca','$fecha','$id_operario','$id_maquina','$id_referencia',
          '$id_turno','$peso','$bultos','$retal','$total')
        ON DUPLICATE KEY UPDATE
          fecha_plana = VALUES(fecha_plana),
          id_operario = VALUES(id_operario),
          id_maquina = VALUES(id_maquina),
          id_referencia = VALUES(id_referencia),
          id_turno = VALUES(id_turno),
          peso_plana = VALUES(peso_plana),
          bultos_plana = VALUES(bultos_plana),
          retal_plana = VALUES(retal_plana),
          total_plana = VALUES(total_plana)";

    mysqli_query($conexion, $sql);
    
    $rows = mysqli_affected_rows($conexion);
    if ($rows === 1) {
        $insertados++;
        $tipo = 'ok';
        $logMsg = addslashes("✔ Insertado · $fecha");
    } elseif ($rows === 2) {
        $actualizados++;
        $tipo = 'upd';
        $logMsg = addslashes("↻ Actualizado · $fecha");
    } else {
        $duplicados++;
        $tipo = 'dup';
        $logMsg = addslashes("↩ Duplicado · $fecha");
    }
    

    echo "<script>tick($contador,$totalp,$insertados,$actualizados,$duplicados,'$logMsg','$tipo');</script>\n";
    if (ob_get_level()) ob_flush(); flush();
}

/* =====================
   FINALIZAR
===================== */
echo "<script>done($insertados,$actualizados,$duplicados,$totalp);</script>\n";
if (ob_get_level()) ob_flush(); flush();
?>

</body>
</html>