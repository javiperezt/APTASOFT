<?php
include "../../conexion.php";

$codigo = filter_input(INPUT_POST, 'codigo', FILTER_SANITIZE_SPECIAL_CHARS);

$exist = "";
$c0 = $mysqli->query("SELECT * FROM gastos where codigo='$codigo'");
if ($c0->num_rows == 0) {
    $exist = "";
} else {
    $exist = "<p class='letraPeq text-warning mt-2'>Código repetido ⚠️</p>";
}

echo $exist;