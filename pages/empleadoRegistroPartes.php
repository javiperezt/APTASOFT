<?php
session_start();

include "../conexion.php";
require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_empleado = filter_input(INPUT_GET, 'id_empleado', FILTER_SANITIZE_SPECIAL_CHARS);

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
        <p class="text-black fw-bold fs-4 m-0">Registro de partes</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoDetail.php?id_empleado=<?= $id_empleado; ?>">Ficha</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-black" href="empleadoJornadas.php?id_empleado=<?= $id_empleado; ?>">Jornadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="empleadoRegistroPartes.php?id_empleado=<?= $id_empleado; ?>">Registros
                    partes</a>
            </li>
        </ul>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table class="table tableComon">
            <thead>
            <tr>
                <th width="30px" scope="col"></th>
                <th width="120px" scope="col">Fecha</th>
                <th scope="col">Obra</th>
                <th scope="col">Capítulo</th>
                <th scope="col">Partida</th>
                <th scope="col">Detalle</th>
                <th width="120px" scope="col">Total horas</th>
                <th width="100px" scope="col">Estado</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php

            $c1 = $mysqli->query("SELECT * FROM obras_registro_partes where id_empleado=$id_empleado order by fecha,id_empleado");
            while ($row = $c1->fetch_assoc()) {
                $id_obras_registro_partes = $row['id'];
                $id_obra = $row['id_obra'];
                $id_presupuesto = $row['id_presupuesto'];
                $id_presupuestos_subpartidas = $row['id_presupuestos_subpartidas'];
                $horas = $row['horas'];
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

                $c3 = $mysqli->query("SELECT * FROM obras WHERE id=$id_obra");
                while ($row = $c3->fetch_assoc()) {
                    $titulo_obra = $row['titulo'];
                }

                $c4 = $mysqli->query("SELECT * FROM presupuestos_subpartidas WHERE id=$id_presupuestos_subpartidas");
                while ($row = $c4->fetch_assoc()) {
                    $id_presupuesto_partidas = $row['id_presupuesto_partidas'];
                    $concepto_subpartida = $row['concepto'];
                }

                $c5 = $mysqli->query("SELECT * FROM presupuestos_partidas WHERE id=$id_presupuesto_partidas");
                while ($row = $c5->fetch_assoc()) {
                    $partida = $row['partida'];
                    $id_capitulo = $row['id_capitulo'];
                }

                $c6 = $mysqli->query("SELECT * FROM capitulos WHERE id=$id_capitulo");
                while ($row = $c6->fetch_assoc()) {
                    $capitulo = $row['capitulo'];
                }

                include "../components/empleados/empleadoParteLine.php";
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