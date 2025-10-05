<?php
session_start();

include "../../conexion.php";
require_once "../../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}
?>
<html lang="es">
<head>
    <title></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php include "../links_header.php"; ?>

    <style>
        .fc-event-title {
            font-weight: bold;
        }

        .fc-event-main {
            white-space: pre-line; /* Permite que las nuevas líneas dentro de la propiedad 'title' se muestren correctamente */
        }

        small {
            font-size: 0.8em;
        }
    </style>
</head>
<body>
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
<div class="mt-5">
<div id='calendar'></div>
</div>
</body>

<script>
    function isMobileDevice() {
        return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
    }

    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var isMobile = window.matchMedia("(max-width: 767px)").matches;

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: isMobile ? 'dayGridDay' : 'dayGridWeek', // Cambiamos la vista a una que no muestre las horas
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridWeek,dayGridDay'
            },
            events: '../../backend/calendario/calendarioTrabajadores.php',
            eventClick: function (info) {
                if (isMobileDevice()) {
                    info.jsEvent.preventDefault(); // previene la navegación
                }
            },
            locale: 'es', // Localización en español
            firstDay: 1,  // Empieza la semana en lunes
            eventTimeFormat: { // Ocultamos la hora en los eventos
                hour: 'numeric',
                minute: '2-digit',
                meridiem: false
            }
        });

        calendar.render();
    });
</script>

</html>