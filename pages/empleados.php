<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";


?>

<html lang="es">
<head>
    <title>Empleados</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <?php include "../links_header.php"; ?>
</head>
<body>
<?php include "../components/navbar.php"; ?>
<!----------- HEAD PAGE ----------->
<div class="container mt-3 d-flex align-items-center justify-content-between">
    <p class="text-black fw-bold fs-3">Empleados</p>
    <div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#empleadoNew">Nuevo empleado<i
                    class="bi bi-person-fill ms-2"></i></button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">

    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Nif</th>
                <th scope="col">Correo</th>
                <th scope="col">Telefono</th>
                <th scope="col">Estado</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM empleados where is_active=1");
            while ($row = $c0->fetch_assoc()) {
                $id_empleado = $row['id'];
                $nombre = $row['nombre'];
                $correo = $row['correo'];
                $tel = $row['tel'];
                $nif = $row['nif'];
                $is_active = $row['is_active'];

                $classEtiq = "text-bg-primary";
                if ($is_active == 0) {
                    $classEtiq = "text-bg-secondary";
                }

                include "../components/empleados/empleadoLine.php";
            }
            if ($c0->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!----------- END TABLE ----------->


</div>


</body>
<?php include "../components/modals/empleadoNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const filterHandler = () => {
        var id_empleado = $('#id_empleado').val();

        location.href = "empleados.php?id=" + id_empleado;
    }

    var minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[1]);
            if (
                (min === null && max === null) ||
                (min === null && date <= max) ||
                (min <= date && max === null) ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        }
    );

    $(document).ready(function () {
        // Create date inputs
        minDate = new DateTime($('#min'), {
            format: 'YYYY/MM/DD'
        });
        maxDate = new DateTime($('#max'), {
            format: 'YYYY/MM/DD'
        });

        // DataTables initialisation
        var table = $('#tabla_proyectos').DataTable({
            language: {
                url: '../espanol.json'
            },
            pageLength: 50
        });

        // Refilter the table
        $('#min, #max').on('change', function () {
            table.draw();
        });
    });
    $.fn.dataTable.ext.errMode = 'none'; // Desactiva alertas de error de DataTables
</script>
</html>