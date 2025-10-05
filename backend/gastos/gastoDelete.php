<?php
include "../../conexion.php";

$id_gasto = filter_input(INPUT_POST, 'id_gasto', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE gastos set is_active=0 where id='$id_gasto'";
mysqli_query($mysqli, $sql);

