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
    <p class="text-black fw-bold fs-3">Contactos</p>
    <div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#contactoNew">Nuevo contacto<i
                    class="bi bi-file-earmark-person-fill ms-2"></i></button>
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
                <th scope="col">Correo</th>
                <th scope="col">CIF/NIF</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Población</th>
                <th scope="col">Tipo</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM contactos where is_active=1");
            while ($row = $c0->fetch_assoc()) {
                $id_contacto = $row['id'];
                $id_tipo_contacto = $row['id_tipo_contacto'];
                $nombre = $row['nombre'];
                $correo = $row['correo'];
                $tel = $row['tel'];
                $poblacion = $row['poblacion'];
                $nif = $row['nif'];

                if ($id_tipo_contacto) {
                    $c1 = $mysqli->query("SELECT * FROM contactos_tipos where id=$id_tipo_contacto");
                    while ($row = $c1->fetch_assoc()) {
                        $tipo_contacto = $row['tipo_contacto'];
                    }
                }

                $classEtiq = "text-bg-primary";
                if ($id_tipo_contacto == 2) {
                    $classEtiq = "text-bg-success";
                }

                include "../components/contactos/contactoLine.php";
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
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const filterHandler = () => {
        var id_tipo_contacto = $('#id_tipo_contacto').val();
        var id = $('#id_contacto').val();

        location.href = "contactos.php?id_tipo_contacto=" + id_tipo_contacto + "&id=" + id;
    }

    var minDate, maxDate;

    // Custom filtering function which will search data in column four between two values
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = minDate.val();
            var max = maxDate.val();
            var date = new Date(data[2]);
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
</script>
</html>