<?php
include "../../conexion.php";


$id_parte = filter_input(INPUT_POST, 'id_parte', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM obras_registro_partes where id=$id_parte";
mysqli_query($mysqli, $sql);
