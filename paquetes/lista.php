<?php
include("../includes/header.php");
require_once("../conexion.php");

/* FILTROS */
$busqueda = $_GET['buscar'] ?? '';
$fecha = $_GET['fecha'] ?? '';

$busqueda = mysqli_real_escape_string($conexion, $busqueda);
$fecha = mysqli_real_escape_string($conexion, $fecha);

/* PAGINACIÓN */
$limite = 10;
$pagina = $_GET['pagina'] ?? 1;
$inicio = ($pagina - 1) * $limite;

/* BASE */
$sql_base = "FROM PRODUCCION_PAQUETES p

LEFT JOIN OPERARIOS o ON p.id_operario = o.id_operario
LEFT JOIN MAQUINAS m ON p.id_maquina = m.id_maquina
LEFT JOIN REFERENCIAS r ON p.id_referencia = r.id_referencia
LEFT JOIN COLORES c ON p.id_color = c.id_color
LEFT JOIN TURNOS t ON p.id_turno = t.id_turno

WHERE 1=1";

/* BUSCADOR COMPLETO */
if(!empty($busqueda)){
    $sql_base .= " AND (
        o.nombre LIKE '%$busqueda%' OR
        m.nombre_maquina LIKE '%$busqueda%' OR
        r.nombre_referencia LIKE '%$busqueda%' OR
        c.nombre_color LIKE '%$busqueda%' OR
        t.nombre_turno LIKE '%$busqueda%' OR

        p.id LIKE '%$busqueda%' OR
        p.paquetes_paq LIKE '%$busqueda%'
    )";
}

/*  FILTRO POR FECHA */
if(!empty($fecha)){
    $sql_base .= " AND DATE(p.fecha_paq) = '$fecha'";
}

/*  TOTAL */
$total_sql = "SELECT COUNT(*) as total $sql_base";
$total_resultado = mysqli_query($conexion,$total_sql);
$total_fila = mysqli_fetch_assoc($total_resultado);
$total_registros = $total_fila['total'];

$total_paginas = ceil($total_registros / $limite);

/* CONSULTA FINAL */
$sql = "SELECT p.*, 

o.nombre AS operario,
m.nombre_maquina,
r.nombre_referencia,
c.nombre_color,
t.nombre_turno

$sql_base

ORDER BY p.fecha_paq DESC
LIMIT $inicio, $limite";

$resultado = mysqli_query($conexion,$sql);
?>

<div class="container">

<h2 class="titulo-vista">Historial Producción Paquetes</h2>

<div class="card">

<!--  FILTROS -->
<form method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">

<input type="text" name="buscar" placeholder="Buscar..."
value="<?php echo $busqueda; ?>">

<input type="date" name="fecha"
value="<?php echo $fecha; ?>">

<button class="btn">Filtrar</button>

<a class="btn" href="lista.php">Limpiar</a>

</form>

<br>

<table class="tabla">

<thead>
<tr>
<th>ID</th>
<th>Fecha</th>
<th>Operario</th>
<th>Máquina</th>
<th>Turno</th>
<th>Referencia</th>
<th>Color</th>
<th>Paquetes</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

<?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

<tr>

<td><?php echo $fila['id']; ?></td>
<td><?php echo $fila['fecha_paq']; ?></td>
<td><?php echo $fila['operario']; ?></td>
<td><?php echo $fila['nombre_maquina']; ?></td>
<td><?php echo $fila['nombre_turno']; ?></td>
<td><?php echo $fila['nombre_referencia']; ?></td>
<td><?php echo $fila['nombre_color']; ?></td>
<td><?php echo $fila['paquetes_paq']; ?></td>

<td>

<a class="btn" href="editar.php?id=<?php echo $fila['id']; ?>">Editar</a>

<a class="btn" href="eliminar.php?id=<?php echo $fila['id']; ?>" 
onclick="return confirm('¿Seguro que deseas eliminar este registro?');">
Eliminar
</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<br>

<!-- PAGINACIÓN -->
<div class="card" style="text-align:center;">

<?php

$rango = 5; // cantidad de páginas antes y después

$inicio = max(1, $pagina - $rango);
$fin = min($total_paginas, $pagina + $rango);

// botón anterior
if($pagina > 1){
    echo '<a href="?pagina='.($pagina-1).'&buscar='.$busqueda.'&fecha='.$fecha.'">«</a> ';
}

// primera página si el rango no empieza en 1
if($inicio > 1){
    echo '<a href="?pagina=1&buscar='.$busqueda.'&fecha='.$fecha.'">1</a> ... ';
}

for($i = $inicio; $i <= $fin; $i++){

    if($i == $pagina){
        echo "<strong>$i</strong> ";
    }else{
        echo '<a href="?pagina='.$i.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$i.'</a> ';
    }

}

// si faltan páginas al final
if($fin < $total_paginas){
    echo ' ... <a href="?pagina='.$total_paginas.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$total_paginas.'</a>';
}

// botón siguiente
if($pagina < $total_paginas){
    echo ' <a href="?pagina='.($pagina+1).'&buscar='.$busqueda.'&fecha='.$fecha.'">»</a>';
}

?>

</div>

<br>

<div class="btn-group">

<a class="btn" href="dashboard.php">Volver al dashboard</a>
<a class="btn" href="../index.php">Volver al menú</a>

</div>

</div>

