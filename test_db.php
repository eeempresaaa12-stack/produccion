<?php
$host     = "switchyard.proxy.rlwy.net";
$usuario  = "root";
$password = "EagzBrYIJHawioQrQhpqbjYAxXFhMwUU";
$database = "CONTROL_PRODUCCION";
$puerto   = 22573;

$conexion = mysqli_connect($host, $usuario, $password, $database, $puerto);

if (!$conexion) {
    die("❌ Error de conexión: " . mysqli_connect_error());
}

echo "✅ Conexión exitosa a la base de datos";

$result = mysqli_query($conexion, "SHOW TABLES");
echo "<br><br>Tablas encontradas:<br>";
while($row = mysqli_fetch_array($result)){
    echo "- " . $row[0] . "<br>";
}
?>