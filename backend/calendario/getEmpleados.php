<?php
include "../../conexion.php";
header('Content-Type: application/json');

$query = "SELECT id, nombre, correo FROM empleados WHERE is_active = 1 ORDER BY nombre ASC";
$resultado = $mysqli->query($query);
$empleados = [];

while ($fila = $resultado->fetch_assoc()) {
    $empleados[] = [
        'id' => $fila['id'],
        'nombre' => $fila['nombre'],
        'correo' => $fila['correo']
    ];
}

echo json_encode($empleados);
?>
