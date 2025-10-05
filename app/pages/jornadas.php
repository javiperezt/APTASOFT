<?php
session_start();

include "../../conexion.php";
require_once "../../authCookieSessionValidate.php";
require_once "../../DateClass.php";
$dateClass = new DateClass();

if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$c1 = $mysqli->query("SELECT * FROM jornadas where id_empleado='$ID_USER' and is_active=1");

// si jornada = 0  es que no hay jornada activa, si jornada da 1 es que hay una jornada activa
$jornada = $c1->num_rows;

$fecha = date("Y-m-d");
$hora = date("H:i:s");

$getTotalJornada = mysqli_query($mysqli, "SELECT SUM(total) AS totalHoras FROM jornadas where fecha='$fecha' and id_empleado=$ID_USER");
$result = mysqli_fetch_assoc($getTotalJornada);
$totalHoras = $result['totalHoras'];

if ($totalHoras) {
    $totalHoras = gmdate("H:i", "$totalHoras");
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
    <div class="d-flex align-items-center justify-content-between">
        <img width="70px" src="../img/logoApta.png" alt="">
        <div class="d-flex gap-3">
            <p class="text-white"><?= $NOMBRE_USUARIO; ?></p>
            <div class="d-flex align-self-center gap-2 ms-4">
                <a class="text-warning" href="soporte.php">Buzón Incidencias</a>
                <i onclick="location.href='soporte.php'" class="bi bi-envelope-fill text-warning fs-4 pointer"></i>
            </div>
        </div>
    </div>

</header>

<main class="container mt-4">
    <div class="bg-white text-center w-100 rounded-2">
        <a href="tareas.php"
           class="fw-bold mainColor border border-primary border-2 rounded-3 d-flex w-100 py-3 justify-content-center text-decoration-none">
            Ver Tareas Asignadas
        </a>
    </div>
    <?php
    if($ID_USER==14 || $ID_USER==5){
        ?>
        <div class="bg-white text-center w-100 rounded-2 mt-3">
            <a href="calendario.php"
               class="fw-bold mainColor border border-success border-2 rounded-3 d-flex w-100 py-3 justify-content-center text-decoration-none">
                Ver Calendario
            </a>
        </div>
    <?php
    }
    ?>

    <div class="bg-white rounded-2 mt-4 py-3 px-2">
        <div class="d-flex align-items-center justify-content-between">
            <p class="mainColor fw-bold">Registro de jornada</p>
            <p id="totalHoras"><?= $totalHoras; ?>h</p>
        </div>
        <div id="jornada">
            <button onclick="updateJornada(<?= $ID_USER; ?>)"
                    class="btn btn-lg w-100  <?= $jornada == 0 ? "btn-primary" : "btn-danger"; ?> py-2 text-white fw-bold letraPeq mt-3">
                <?= $jornada == 0 ? "Empezar jornada" : "Detener"; ?>
            </button>
        </div>
    </div>
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
            $("#totalHoras").load(location.href + " #totalHoras");
        });
    }

</script>
</html>