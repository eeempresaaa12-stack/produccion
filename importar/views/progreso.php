<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Importando <?php echo $titulo; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="../../assets/css/importar.css">
</head>
<body>

<div class="wrapper">

    <div class="top">
        <div>
            <h1><?php echo $titulo; ?></h1>
            <p>Google Sheets &rarr; MySQL &middot; <?php echo $subtitulo; ?></p>
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

<a id="btn-volver" href="#" onclick="volverAtras(); return false;">← Volver</a>

<script>window.VOLVER_URL = '<?php echo $volver_url; ?>';</script>
<script src="../scripts/importar.js"></script>