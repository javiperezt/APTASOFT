<?php
session_start();
include "../../conexion.php";


if (isset($_POST)) {
    // Count total files
    $countfiles = count($_FILES['upload']['name']);
    $uploaded_by = filter_input(INPUT_POST, 'uploaded_by', FILTER_SANITIZE_SPECIAL_CHARS);
    $id_directorio = filter_input(INPUT_POST, 'id_directorio', FILTER_SANITIZE_SPECIAL_CHARS);
    $id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

    $obras_directorios = $mysqli->query("SELECT * FROM obras_directorios where id='$id_directorio'");
    while ($row = $obras_directorios->fetch_assoc()) {
        $directorio = $row['directorio'];
    }

    if (!is_dir("../../docs/obras/$id_obra")) {
        mkdir("../../docs/obras/$id_obra");
    }

    if (!is_dir("../../docs/obras/$id_obra/$directorio")) {
        mkdir("../../docs/obras/$id_obra/$directorio");
    }

    for ($i = 0; $i < $countfiles; $i++) {
        $filename = $_FILES['upload']['name'][$i];
        $file_name = strtolower(array_shift(explode('.', $filename)));
        $file_name = str_replace(' ', '_', $file_name);
        $file_name = preg_replace("/[^A-Za-z0-9\-]/", "", $file_name);
        $file_ext = strtolower(end(explode('.', $_FILES['upload']['name'][$i])));
        $file_name = $file_name . "." . $file_ext;

        // Upload file
        move_uploaded_file($_FILES['upload']['tmp_name'][$i], "../../docs/obras/$id_obra/$directorio/" . $file_name);

        $path = "$id_obra/$directorio/$file_name";

        $sql = "INSERT INTO obras_archivos (id_obra, id_directorio, titulo, src, id_empleado) VALUES ('$id_obra', '$id_directorio', '$file_name', '$path', '$uploaded_by')";
        mysqli_query($mysqli, $sql);
    }
    header("Location: ../../pages/obraArchivos.php?id_obra=$id_obra");
}
?>
