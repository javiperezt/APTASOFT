<?php
include "../../conexion.php";
// Connect to the database

// Get the event details from the POST data
$id = $_POST['id'];
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];

// Update the event in the database
$sql = "UPDATE events SET title = '$title' WHERE id = $id";
$mysqli->query($sql);
$mysqli->close();
?>
