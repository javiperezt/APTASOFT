<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";

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
    <p class="text-black fw-bold fs-3">Incidencias</p>
</div>
<!----------- END HEAD PAGE ----------->

<div class="bg-white container-md my-3 rounded-3 p-4">


    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th width="20px" scope="col"></th>
                <th scope="col">Empleado</th>
                <th scope="col">Fecha</th>
                <th width="80%" scope="col">Incidencia</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT *, date(creation_date) as creation_date from incidencias order by creation_date");
            while ($row = $c0->fetch_assoc()) {
                $id_incidencia = $row['id'];
                $id_empleado = $row['id_empleado'];
                $incidencia = $row['incidencia'];
                $creation_date = $row['creation_date'];
                $is_checked = $row['is_checked'];

                if ($id_empleado) {
                    $c1 = $mysqli->query("SELECT * FROM empleados where id=$id_empleado");
                    while ($row = $c1->fetch_assoc()) {
                        $nombre = $row['nombre'];
                    }
                }

                $classEtiq = "text-bg-primary";
                if ($id_tipo_contacto == 2) {
                    $classEtiq = "text-bg-success";
                }

                echo '
                        <tr>
                            <td>
                                <input ' . ($is_checked ? 'checked' : '') . '
                                    onchange="updateGenerico(\'incidencias\', \'is_checked\', ' . $id_incidencia . ', this.checked ? 1 : 0)"
                                    type="checkbox" class="btn-check"
                                    id="btn-check-outlined' . $id_incidencia . '" autocomplete="off">
                                <label style="padding: 3px !important;" class="btn btn-outline-primary btn-sm"
                                    for="btn-check-outlined' . $id_incidencia . '"><i class="bi bi-check-lg"></i></label>
                            </td>
                            <td>' . $nombre . '</td>
                            <td>' . $creation_date . '</td>
                            <td>' . $incidencia . '</td>
                            <td><bi onclick="deleteIncidencia(' . $id_incidencia . ')" class="bi bi-x-lg pointer"></bi></td>
                        </tr>
                    ';
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
<?php include "../components/modals/contactoNew.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
<script src="../js/updateGenerico.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>


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
            format: 'MMMM Do YYYY'
        });
        maxDate = new DateTime($('#max'), {
            format: 'MMMM Do YYYY'
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

    const deleteIncidencia = (id_incidencia) => {
        $.ajax({
            method: "POST",
            url: "../backend/incidencias/deleteIncidencia.php",
            data: {
                id_incidencia: id_incidencia
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }

</script>
</html>