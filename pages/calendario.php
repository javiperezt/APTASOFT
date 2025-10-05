<?php
session_start();

include "../conexion.php";
require_once "../DateClass.php";

require_once "../authCookieSessionValidate.php";
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
<?php include "../components/navbar.php"; ?>

<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <p class="text-black fw-bold fs-3">Calendario</p>
</div>
<!----------- END HEAD PAGE ----------->

<div id='calendar'></div>
</body>

<script>
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
            events: '../backend/calendario/calendarioTrabajadores.php',
            eventClick: function (info) {
                alert(info.event.extendedProps.description);
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