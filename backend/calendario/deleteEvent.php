<?php
include "../../conexion.php";
// Connect to the database

// Get the event ID from the POST data
$id = $_POST['id'];

// Delete the event from the database
$sql = "DELETE FROM events WHERE id = $id";
$mysqli->query($sql);
$mysqli->close();