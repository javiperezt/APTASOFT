<?php
session_start();

include "../../conexion.php";
require_once "../../authCookieSessionValidate.php";
require_once "../../DateClass.php";
$dateClass = new DateClass();

if (!$isLoggedIn) {
    header("Location: ../index.php");
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
    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="jornadas.php" class="text-black"><i class="bi bi-arrow-left fs-4"></i></a>
        <p class="fw-bold fs-5">Incidencias</p>
    </div>
    <textarea name="" id="incidencia" class="form-control" style="height: 150px"
              placeholder="Escribir mensaje"></textarea>
    <button class="btn btn-primary w-100 mt-3" onclick="sendIncidencia()">Enviar incidencia</button>
    <p class="mt-3 text-success letraPeq" id="response"></p>
</main>
</body>

<script>
    const sendIncidencia = () => {
        incidencia = $("#incidencia").val();
        if (incidencia) {
            $.ajax({
                method: "POST",
                url: "../backend/sendIncidencia.php",
                data: {
                    incidencia: incidencia,
                    id_user: <?= $ID_USER; ?>
                }
            }).done(function () {
                $("#response").text('Incidencia enviada ✅');
            });
        }else{
            $("#response").text('Introduce una incidencia')
        }
    }
</script>
</html>