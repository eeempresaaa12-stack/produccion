function abrirModal(idModal){
    document.getElementById(idModal).style.display = "flex";
}

function cerrarModal(idModal){
    document.getElementById(idModal).style.display = "none";
}

function formatearNumero(numero){
    return Number(numero).toLocaleString();
}