<?php
include "../../conexion.php";

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Crear una consulta SQL para obtener los eventos de la tabla 'events'
$sql = "SELECT id, title, start, end FROM events";

// Ejecutar la consulta y obtener los resultados
$result = $mysqli->query($sql);

// Crear un array para almacenar los eventos
$events = array();

// Recorrer los resultados y agregar cada evento al array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $event = array();
        $event['id'] = $row['id'];
        $event['title'] = $row['title'];
        $event['start'] = $row['start'];
        $event['end'] = $row['end'];
        array_push($events, $event);
    }
}

// Devolver los eventos en formato JSON
echo json_encode($events);

// Cerrar la conexión a la base de datos
$mysqli->close();
?>
