<?php
include "../../conexion.php";


// Obtener los datos del evento desde la solicitud AJAX
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];

// Crear una consulta SQL para insertar el evento en la tabla 'events'
$sql = "INSERT INTO events (title, start, end) VALUES ('$title', '$start', '$end')";

// Ejecutar la consulta y verificar si fue exitosa
if ($mysqli->query($sql) === TRUE) {
    echo "El evento se agregó correctamente.";
} else {
    echo "Error al agregar el evento: " . $mysqli->error;
}

// Cerrar la conexión a la base de datos
$mysqli->close();

