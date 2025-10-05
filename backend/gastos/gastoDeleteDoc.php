<?php
include "../../conexion.php";

$id_documento = filter_input(INPUT_POST, 'id_documento', FILTER_SANITIZE_SPECIAL_CHARS);

$getDoc = $mysqli->query("SELECT * FROM gastos_archivos where id='$id_documento'");
while ($row = $getDoc->fetch_assoc()) {
    $src = $row['src'];
}

if ($getDoc->num_rows > 0) {
    $sql = "DELETE FROM gastos_archivos where id='$id_documento'";
    mysqli_query($mysqli, $sql);
    unlink("../../docs/gastos/$src");
}
