<?php
include "../conexion.php";
include "../authCookieSessionValidate.php";
if (!$isLoggedIn) {header("Location: ../index.php");}


$tabla = filter_input(INPUT_POST, 'tabla', FILTER_SANITIZE_SPECIAL_CHARS);
$columna = filter_input(INPUT_POST, 'columna', FILTER_SANITIZE_SPECIAL_CHARS);
$fila = filter_input(INPUT_POST, 'fila', FILTER_SANITIZE_SPECIAL_CHARS);
$valor = filter_input(INPUT_POST, 'valor', FILTER_SANITIZE_SPECIAL_CHARS);

try {
    // total_x_cantidad es un campo virtual calculado, no una columna real
    // Se actualiza via calculateTotalxCantidad.php desde JavaScript
    if ($columna == 'total_x_cantidad') {
        // No hacemos nada aquí, el JavaScript llamará a calculateTotalxCantidad.php
        exit;
    }

    $sql = "UPDATE $tabla set $columna='$valor' where id=$fila";
    $result = mysqli_query($mysqli, $sql);

    if (!$result) {
        throw new \Exception("Error en la query: " . mysqli_error($mysqli));
    }
} catch (\Throwable $exception) {
    \Sentry\captureException($exception);
    // Re-lanzar la excepción si quieres que falle visiblemente
    throw $exception;
}

