<?php
include "../../conexion.php";

$id_facturas_partidas = filter_input(INPUT_POST, 'id_facturas_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM facturas_partidas where id=$id_facturas_partidas";
mysqli_query($mysqli, $sql);


