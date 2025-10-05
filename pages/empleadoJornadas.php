<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
require_once "../DateClass.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$dateClass = new DateClass();

$id_empleado = filter_input(INPUT_GET, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
while ($row = $c0->fetch_assoc()) {
    $id_rol = $row['id_rol'];
    $id_proveedor = $row['id_proveedor'];
    $id_empresa = $row['id_empresa'];
    $nombre = $row['nombre'];
    $correo = $row['correo'];
    $password = $row['password'];
    $tel = $row['tel'];
    $movil = $row['movil'];
    $nif = $row['nif'];
}
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
        <a href="empleados.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Registro de jornadas</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoDetail.php?id_empleado=<?= $id_empleado; ?>">Ficha</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="empleadoJornadas.php?id_empleado=<?= $id_empleado; ?>">Jornadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoRegistroPartes.php?id_empleado=<?= $id_empleado; ?>">Registros
                    partes</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="text-end">
        <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
           data-bs-target="#empleadoAddJornada">+
            Registrar jornada</a>
    </div>
    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th width="120px" scope="col">Fecha</th>
                <th scope="col">Registros</th>
                <th width="120px" scope="col">Horas</th>
            </tr>
            </thead>
            <tbody>
            <?php

            $c1 = $mysqli->query("SELECT MIN(id), fecha FROM jornadas where id_empleado='$id_empleado' GROUP BY fecha");
            while ($row = $c1->fetch_assoc()) {
                $fecha = $row['fecha'];

                if ($c1->num_rows > 0) {
                    $getTotalJornada = mysqli_query($mysqli, "SELECT SUM(total) AS totalHoras FROM jornadas where fecha='$fecha' and id_empleado=$id_empleado");
                    $result = mysqli_fetch_assoc($getTotalJornada);
                    $totalHoras = $result['totalHoras'];

                    $totalHoras = gmdate("H:i", "$totalHoras");
                }
                ?>
                <tr>
                    <td><?= $fecha; ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <?php
                            if ($c1->num_rows > 0) {
                                $c2 = $mysqli->query("SELECT hora_inicio,hora_fin,id, TIMEDIFF(hora_fin, hora_inicio) AS difference FROM jornadas where fecha='$fecha' and id_empleado=$id_empleado order by hora_inicio");
                                while ($row = $c2->fetch_assoc()) {
                                    $id_jornada = $row['id'];
                                    $hora_inicio = $row['hora_inicio'];
                                    $hora_fin = $row['hora_fin'];

                                    // diff directamente en h min sec
                                    $difference = $row['difference'];

                                    include "../components/empleados/jornadaLine.php";
                                }
                            }
                            ?>
                        </div>
                    </td>
                    <td><?= $totalHoras; ?>h</td>
                </tr>
                <?php
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
<?php include "../components/modals/empleadoAddJornada.php"; ?>
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
            showMessage();
        });
    }

    const empleadoDeleteJornada = (id_jornada) => {
        $.ajax({
            method: "POST",
            url: "../backend/empleados/empleadoDeleteJornada.php",
            data: {
                id_jornada: id_jornada
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }
</script>
</html>