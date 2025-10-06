<?php
include "../../conexion.php";
header('Content-Type: application/json');

$query = "SELECT id, titulo FROM obras WHERE is_active = 1 ORDER BY titulo ASC";
$resultado = $mysqli->query($query);
$obras = [];

while ($fila = $resultado->fetch_assoc()) {
    $obras[] = [
        'id' => $fila['id'],
        'titulo' => $fila['titulo']
    ];
}

echo json_encode($obras);
?>
