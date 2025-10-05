<?php
include "../../conexion.php";


$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "INSERT INTO contactos (nombre) VALUE ('$nombre')";
mysqli_query($mysqli, $sql);
$id_contacto = mysqli_insert_id($mysqli);

header("Location: ../../pages/contactoDetail.php?id_contacto=$id_contacto");

