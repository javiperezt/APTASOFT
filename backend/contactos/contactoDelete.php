<?php
include "../../conexion.php";


$id_contacto = filter_input(INPUT_POST, 'id_contacto', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE contactos set is_active=0 where id=$id_contacto";
mysqli_query($mysqli, $sql);

