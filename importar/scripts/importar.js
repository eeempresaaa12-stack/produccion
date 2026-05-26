function up(pct, msg) {
    document.getElementById('fill').style.width        = pct + '%';
    document.getElementById('pct').textContent         = pct;
    document.getElementById('msg').textContent         = msg;
    document.getElementById('dot').className           = 'dot';
    document.getElementById('status-lbl').textContent  = 'Procesando';
}

function tick(cur, total, ok, upd, dup, msgLog, type) {
    document.getElementById('s-ok').textContent  = ok;
    document.getElementById('s-upd').textContent = upd;
    document.getElementById('s-dup').textContent = dup;
    document.getElementById('counter').textContent = cur + ' / ' + total + ' registros';

    var pct = 8 + Math.round((cur / total) * 90);
    document.getElementById('fill').style.width = pct + '%';
    document.getElementById('pct').textContent  = pct;
    document.getElementById('msg').textContent  =
        'Importando \u2026 (' + cur + '\u202f/\u202f' + total + ')';

    var row = document.createElement('div');
    row.className = 'lr ' + type;
    row.innerHTML = '<span class="n">' + cur + '</span>'
                  + '<span class="t">' + msgLog + '</span>';

    var log = document.getElementById('log');
    log.appendChild(row);
    log.scrollTop = log.scrollHeight;
}

function done(ok, upd, dup, total) {
    document.getElementById('fill').style.width        = '100%';
    document.getElementById('pct').textContent         = '100';
    document.getElementById('msg').textContent         = 'Importación completada exitosamente';
    document.getElementById('dot').className           = 'dot done';
    document.getElementById('status-lbl').textContent  = 'Completado';

    document.getElementById('s-ok').textContent  = ok;
    document.getElementById('s-upd').textContent = upd;
    document.getElementById('s-dup').textContent = dup;
    document.getElementById('s-tot').textContent = total;

    document.getElementById('stats').classList.add('show');
    document.getElementById('toggle-btn').classList.add('show');
    document.getElementById('btn-volver').classList.add('show');
}

function toggleLog() {
    document.getElementById('toggle-btn').classList.toggle('open');
    document.getElementById('log').classList.toggle('open');
}

function volverAtras() {
    if (window.history.length > 2) {
        window.history.back();
    } else {
        var url = window.VOLVER_URL || '/index.php';
        window.location.href = url;
    }
}