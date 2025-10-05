<?php
include "../../conexion.php";

$id_pago = filter_input(INPUT_POST, 'id_pago', FILTER_SANITIZE_SPECIAL_CHARS);

$sql = "DELETE FROM facturas_pagos WHERE id=$id_pago";
mysqli_query($mysqli, $sql);

