<?php
session_start();

include "../conexion.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_presupuestos_subpartidas = filter_input(INPUT_GET, 'id_presupuestos_subpartidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuesto = filter_input(INPUT_GET, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);
$id_presupuestos_partidas = filter_input(INPUT_GET, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

?>
<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <div class="d-flex gap-2 align-items-center">
        <a class="pointer"
           href="obraTareas.php?id_obra=<?= $id_obra; ?>"><i
                    class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Registro de horas</p>
    </div>
    <div>
        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#obraAddParteToPlanning">+
            Registrar parte</a>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5">Partidas</p>
    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th width="30px" scope="col"></th>
                <th width="120px" scope="col">Fecha</th>
                <th scope="col">Trabajador</th>
                <th width="120px" scope="col">Horas</th>
                <th width="100px" scope="col">Estado</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $c1 = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuesto='$id_presupuesto' and id_obra='$id_obra' and id_presupuestos_subpartidas='$id_presupuestos_subpartidas' order by fecha,id_empleado");
            while ($row = $c1->fetch_assoc()) {
                $id_obras_registro_partes = $row['id'];
                $horas = $row['horas'];
                $id_empleado = $row['id_empleado'];
                $fecha = $row['fecha'];
                $is_verified = $row['is_verified'];

                if ($is_verified) {
                    $text = "Verificado";
                    $class = "text-bg-success";
                } else {
                    $text = "Sin verificar";
                    $class = "text-bg-warning";
                }

                if ($id_empleado) {
                    $c2 = $mysqli->query("SELECT * FROM empleados WHERE id=$id_empleado");
                    while ($row = $c2->fetch_assoc()) {
                        $nombre_empleado = $row['nombre'];
                    }
                }

                include "../components/obras/parteLine.php";
            }
            if ($c1->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->
</div>


</body>
<?php include "../components/modals/obraAddParteToPlanning.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>

<script>
    const updateGenerico = (tabla, columna, fila, valor) => {
        $.ajax({
            method: "POST",
            url: "../backend/updateGenerico.php",
            data: {
                tabla: tabla,
                columna: columna,
                fila: fila,
                valor: valor
            }
        }).done(function () {
            if (columna === "is_verified") {
                $(".tableComon").load(location.href + " .tableComon");
            }
            showMessage();
        });
    }

    const obrasDeleteParte = (id_parte) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/obrasDeleteParte.php",
            data: {
                id_parte: id_parte
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }
</script>
</html>