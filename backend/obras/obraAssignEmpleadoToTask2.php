<?php
include "../../conexion.php";


$id_obra = filter_input(INPUT_POST, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_POST, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_partidas = filter_input(INPUT_POST, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_subpartidas_empty = filter_input(INPUT_POST, 'id_presupuestos_subpartidas_empty', FILTER_SANITIZE_SPECIAL_CHARS);
$id_empleado_empty = filter_input(INPUT_POST, 'id_empleado_empty', FILTER_SANITIZE_SPECIAL_CHARS);
$row = $_POST['name'];


$iteracion = 0;
if (count($row) > 0) {
    foreach ($row as $checkboxValue => $idEmpleadoAndIdSubpartida) {
        $x = explode("-", $checkboxValue);
        $id_empleado = array_shift($x);
        $id_presupuestos_subpartidas = end($x);

        // solo se ejecuta la primera vez
        if ($iteracion == 0) {
            $sql2 = "DELETE FROM obras_trabajadores_subpartidas where id_presupuesto='$id_presupuesto' and id_obra='$id_obra' and id_presupuestos_subpartidas='$id_presupuestos_subpartidas'";
            mysqli_query($mysqli, $sql2);
            $iteracion++;
        }

        $sql = "INSERT INTO obras_trabajadores_subpartidas (id_obra, id_presupuesto, id_presupuestos_subpartidas, id_empleado) VALUES ('$id_obra','$id_presupuesto','$id_presupuestos_subpartidas','$id_empleado')";
        mysqli_query($mysqli, $sql);
    }
}

if (count($row) == 0) {
    $sql2 = "DELETE FROM obras_trabajadores_subpartidas where id_presupuesto='$id_presupuesto' and id_obra='$id_obra' and id_presupuestos_subpartidas='$id_presupuestos_subpartidas_empty'";
    mysqli_query($mysqli, $sql2);
}

header("Location: ../../pages/obraTareas.php?id_obra=$id_obra");
?>