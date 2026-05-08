<?php
/** @var mysqli $conexion */

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
$sql_base = "FROM PRODUCCION_ROLLO r

LEFT JOIN MAQUINAS m ON r.id_maquina = m.id_maquina
LEFT JOIN TURNOS t ON r.id_turno = t.id_turno
LEFT JOIN REFERENCIAS ref ON r.id_referencia = ref.id_referencia
LEFT JOIN COLORES c ON r.id_color = c.id_color

WHERE 1=1";

/* BUSCADOR */
if(!empty($busqueda)){
    $sql_base .= " AND (
        ref.nombre_referencia LIKE '%$busqueda%' OR
        m.nombre_maquina LIKE '%$busqueda%' OR
        c.nombre_color LIKE '%$busqueda%' OR

        r.id LIKE '%$busqueda%' OR
        r.peso_rollo LIKE '%$busqueda%' OR
        r.retal_roll LIKE '%$busqueda%' OR
        r.total_roll LIKE '%$busqueda%'
    )";
}

/* FILTRO POR FECHA */
if(!empty($fecha)){
    $sql_base .= " AND DATE(r.fecha_roll) = '$fecha'";
}

/* TOTAL REGISTROS */
$total_sql = "SELECT COUNT(*) as total $sql_base";
$total_resultado = mysqli_query($conexion,$total_sql);
$total_fila = mysqli_fetch_assoc($total_resultado);
$total_registros = $total_fila['total'];

$total_paginas = ceil($total_registros / $limite);

/* CONSULTA FINAL */
$sql = "SELECT r.*, 
m.nombre_maquina,
t.nombre_turno,
ref.nombre_referencia,
c.nombre_color

$sql_base

ORDER BY r.fecha_roll DESC
LIMIT $inicio, $limite";

$resultado = mysqli_query($conexion,$sql);
?>

<div class="container">

<h2 class="titulo-vista">Registros Producción Rollo</h2>

<div class="card">

<!-- FILTROS -->
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
<th>Máquina</th>
<th>Turno</th>
<th>Referencia</th>
<th>Color</th>
<th>Peso</th>
<th>Retal</th>
<th>Total</th>
<th>Acciones</th>
</tr>
</thead>

<tbody>

<?php while($fila = mysqli_fetch_assoc($resultado)){ ?>

<tr>
<td><?php echo $fila['id']; ?></td>
<td><?php echo $fila['fecha_roll']; ?></td>
<td><?php echo $fila['nombre_maquina']; ?></td>
<td><?php echo $fila['nombre_turno']; ?></td>
<td><?php echo $fila['nombre_referencia']; ?></td>
<td><?php echo $fila['nombre_color']; ?></td>
<td><?php echo $fila['peso_rollo']; ?></td>
<td><?php echo $fila['retal_roll']; ?></td>
<td><?php echo $fila['total_roll']; ?></td>

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

$rango = 5; 

$inicio = max(1, $pagina - $rango);
$fin = min($total_paginas, $pagina + $rango);

if($pagina > 1){
    echo '<a href="?pagina='.($pagina-1).'&buscar='.$busqueda.'&fecha='.$fecha.'">«</a> ';
}

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

if($fin < $total_paginas){
    echo ' ... <a href="?pagina='.$total_paginas.'&buscar='.$busqueda.'&fecha='.$fecha.'">'.$total_paginas.'</a>';
}

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
