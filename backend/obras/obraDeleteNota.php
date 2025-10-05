<?php
include "../../conexion.php";


$id_nota = filter_input(INPUT_POST, 'id_nota', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM obras_notas where id='$id_nota'";
mysqli_query($mysqli, $sql);
