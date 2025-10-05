<?php
session_start();
include "../../conexion.php";

if (isset($_POST)) {
    // Count total files
    $countfiles = count($_FILES['upload']['name']);
    $uploaded_by = filter_input(INPUT_POST, 'uploaded_by', FILTER_SANITIZE_SPECIAL_CHARS);
    $id_gasto = filter_input(INPUT_POST, 'id_gasto', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!is_dir("../../docs/gastos/$id_gasto")) {
        mkdir("../../docs/gastos/$id_gasto");
    }

    $time=time();

    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['upload']['name'][$i];
        $file_name = strtolower(array_shift(explode('.', $filename)));
        $file_name = str_replace(' ', '_', $file_name);
        $file_name = preg_replace("/[^A-Za-z0-9\-]/", "", $file_name);
        $file_ext = strtolower(end(explode('.', $_FILES['upload']['name'][$i])));
        $file_name = $file_name."$time".".".$file_ext;

        // Upload file
        move_uploaded_file($_FILES['upload']['tmp_name'][$i], "../../docs/gastos/$id_gasto/" . $file_name);

        $path = "$id_gasto/$file_name";

        $sql = "INSERT INTO gastos_archivos (id_gasto, titulo, src, id_empleado) VALUES ('$id_gasto', '$file_name', '$path', '$uploaded_by')";
        mysqli_query($mysqli, $sql);
    }
    header("Location: ../../pages/gastoDetail.php?id_gasto=$id_gasto");
}
?>
