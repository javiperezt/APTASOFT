<?php
include "../../conexion.php";


$id_archivo = filter_input(INPUT_POST, 'id_archivo', FILTER_SANITIZE_SPECIAL_CHARS);

$getDoc = $mysqli->query("SELECT * FROM obras_archivos where id='$id_archivo'");
while ($row = $getDoc->fetch_assoc()) {
    $src = $row['src'];
}

if ($getDoc->num_rows > 0) {
    $sql = "DELETE FROM obras_archivos where id='$id_archivo'";
    mysqli_query($mysqli, $sql);
    unlink("../../docs/obras/$src");
}
