<?php
session_start();

include "../../conexion.php";
require_once "../../authCookieSessionValidate.php";
require_once "../../DateClass.php";
$dateClass = new DateClass();

if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$c1 = $mysqli->query("SELECT * FROM obras_trabajadores_subpartidas where id_empleado='$ID_USER'");
while ($row = $c1->fetch_assoc()) {
    $id_jornada = $row['id'];
    $hora_inicio = $row['hora_inicio'];
    $is_active = $row['is_active'];
}
?>
<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body style="background-color: #F3F4F6">

<header style="background-color: #212936" class="p-2">
    <img width="70px" src="../img/logoApta.png" alt="">
</header>

<main class="container mt-4">
    <div class="d-flex align-items-center gap-2">
        <a href="jornadas.php" class="text-black"><i class="bi bi-arrow-left fs-4"></i></a>
        <p class="fw-bold fs-5">Tareas</p>
    </div>

    <!---------- TAREAS ------------>
    <div class="mt-4 d-flex flex-column gap-2">
        <?php
        $c1 = $mysqli->query("SELECT * FROM obras_trabajadores_subpartidas where id_empleado='$ID_USER'");
        while ($row = $c1->fetch_assoc()) {
            $id_obras_trabajadores_subpartidas = $row['id'];
            $id_obra = $row['id_obra'];
            $id_presupuesto = $row['id_presupuesto'];
            $id_presupuestos_subpartidas = $row['id_presupuestos_subpartidas'];

            $is_active = "";
            $c334 = $mysqli->query("SELECT * FROM obras where id='$id_obra'");
            while ($row = $c334->fetch_assoc()) {
                $is_active = $row['is_active'];
            }

            $c2 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id='$id_presupuestos_subpartidas'");
            while ($row = $c2->fetch_assoc()) {
                $id_presupuesto_partidas = $row['id_presupuesto_partidas'];
                $id_partida = $row['id_partida'];
                $id_subpartida = $row['id_subpartida'];
                $id_categoria = $row['id_categoria'];
                $concepto_subpartida = $row['concepto'];
                $descripcion_subpartida = $row['descripcion'];
                $id_unidad = $row['id_unidad'];
                $cantidad_subpartida = $row['cantidad'];
                $precio_subpartida = $row['precio'];
                $descuento_subpartida = $row['descuento'];
                $id_iva_subpartida = $row['id_iva'];
                $subtotal_subpartida = $row['subtotal'];
                $total_subpartida = $row['total'];
                $fecha_vencimiento_subpartida = $row['fecha_vencimiento'];
                $is_checked = $row['is_checked'];
            }

            $c5 = $mysqli->query("SELECT * FROM unidades where id='$id_unidad'");
            while ($row = $c5->fetch_assoc()) {
                $simbolo = $row['simbolo'];
            }

            $c3 = $mysqli->query("SELECT * FROM presupuestos_partidas where id='$id_presupuesto_partidas'");
            while ($row = $c3->fetch_assoc()) {
                $partida = $row['partida'];
                $descripcion_partida = $row['descripcion'];
                $cantidad_partida = $row['cantidad'];
                $subtotal_partida = $row['subtotal'];
                $total_partida = $row['total'];
            }

            $c4 = $mysqli->query("SELECT * FROM obras where id='$id_obra'");
            while ($row = $c4->fetch_assoc()) {
                $titulo_obra = $row['titulo'];
            }
            if ($is_checked == 0 && $is_active == 1) {
                include "../components/tareas.php";
                include "../components/modals/imputarHoras.php";
            }
        }
        if ($c1->num_rows == 0) {
            echo "<p class='text-center'>No tienes tareas asignadas</p>";
        }
        ?>
    </div>
    <!---------- END AREAS ------------>
</main>
</body>

<script>
    const updateJornada = (id_user) => {
        $.ajax({
            method: "POST",
            url: "../backend/udpateJornada.php",
            data: {
                id_user: id_user
            }
        }).done(function () {
            $("#jornada").load(location.href + " #jornada");
        });
    }

</script>
</html>