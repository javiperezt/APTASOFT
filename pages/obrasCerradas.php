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
    <div class="d-flex gap-2 align-items-center">
        <a href="obras.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-3 m-0">Obras Cerradas</p>
    </div>
    <div>
        <a href="obras.php" class="btn btn-sm btn-outline-secondary" >< Volver a obras Activas</a>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="row align-items-center g-2">
        <div class="d-flex align-items-center gap-2">
            <p>Filtro por fechas:</p>
            <input style="width: 200px" type="text" id="min" name="min" class="form-control form-control-sm"
                   placeholder="Desde">
            <input style="width: 200px" type="text" id="max" name="max" class="form-control form-control-sm"
                   placeholder="Hasta">
        </div>
    </div>

    <!----------- TABLE ----------->
    <div class="table-responsive mt-3">
        <table id="tabla_proyectos" class="table tableComon">
            <thead>
            <tr>
                <th scope="col">Empresa</th>
                <th scope="col">Obra</th>
                <th scope="col">Cliente</th>
                <th scope="col">Inicio</th>
                <th scope="col">Fin</th>
                <th width="140px" scope="col">Progreso</th>
                <th scope="col">Estado</th>
                <th width="20px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c0 = $mysqli->query("SELECT * FROM obras where is_active=1 and (id_estado=7 or id_estado=8)");
            while ($row = $c0->fetch_assoc()) {
                $id_obra = $row['id'];
                $id_contacto = $row['id_contacto'];
                $id_empresa = $row['id_empresa'];
                $id_canal_entrada = $row['id_canal_entrada'];
                $id_usuario_asignado = $row['id_usuario_asignado'];
                $id_estado = $row['id_estado'];
                $titulo = $row['titulo'];
                $fecha_inicio = $row['fecha_inicio'];
                $fecha_fin = $row['fecha_fin'];
                $localizacion = $row['localizacion'];

                $c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                while ($row = $c1->fetch_assoc()) {
                    $nombre_contacto = $row['nombre'];
                }

                $c1222 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
                while ($row = $c1222->fetch_assoc()) {
                    $empresa = $row['empresa'];
                }


                if ($id_estado) {
                    $c3 = $mysqli->query("SELECT * FROM obras_estados where id=$id_estado");
                    while ($row = $c3->fetch_assoc()) {
                        $estado = $row['estado'];
                    }
                }

                $classEtiq = "text-bg-primary";
                if ($id_estado == 1) {
                    $classEtiq = "text-bg-secondary";
                }
                if ($id_estado == 7) {
                    $classEtiq = "text-bg-success";
                }
                if ($id_estado == 8) {
                    $classEtiq = "text-bg-danger";
                }

                $id_presupuesto = "";

                $c4 = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra");
                while ($row = $c4->fetch_assoc()) {
                    $id_presupuesto = $row['id'];

                    if ($id_presupuesto) {
                        $porcentaje = 0;
                        $countSubpartidas = "";
                        $countSubpartidasChecked = "";
                        $c5 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto");
                        while ($row = $c5->fetch_assoc()) {
                            $id_presupuestos_partidas = $row['id'];

                            // para calcular el porcentaje
                            $countSubpartidas = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria!=2");
                            $countSubpartidas = $countSubpartidas->num_rows;
                            $countSubpartidasTotal += $countSubpartidas;

                            // contamos los checks en mano de obra (excluimos material (2))
                            $countSubpartidasChecked = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and is_checked=1 and id_categoria!=2");
                            $countSubpartidasChecked = $countSubpartidasChecked->num_rows;
                            $countSubpartidasCheckedTotal += $countSubpartidasChecked;
                        }
                    }
                }
                if ($countSubpartidasCheckedTotal) {
                    $porcentaje = round($countSubpartidasCheckedTotal / $countSubpartidasTotal * 100, 2);
                } else {
                    $porcentaje = 0;
                }

                include "../components/obras/obraLine.php";
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
<?php include "../components/modals/obraNew.php"; ?>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>

<script>
    const filterHandler = () => {
        var id_empresa = $("input[name='btnradio']:checked").val();
        var id_estado = $('#id_estado').val();
        var id_contacto = $('#id_contacto').val();

        location.href = "obras.php?id_empresa=" + id_empresa + "&id_estado=" + id_estado + "&id_contacto=" + id_contacto;
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