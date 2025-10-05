<?php
include "../../conexion.php";

$id_factura = filter_input(INPUT_POST, 'id_factura', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "UPDATE facturas set is_active=0 where id='$id_factura'";
mysqli_query($mysqli, $sql);

