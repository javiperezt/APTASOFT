<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";

// Initialize filter variables
$f_id_empresa = isset($_GET['id_empresa']) ? $_GET['id_empresa'] : '';
$f_id_estado = isset($_GET['id_estado']) ? $_GET['id_estado'] : '';
$f_id_contacto = isset($_GET['id_contacto']) ? $_GET['id_contacto'] : '';

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
    <p class="text-black fw-bold fs-3">Obras</p>
    <div>
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#obraNew">Nueva obra<i
                    class="bi bi-tools ms-2"></i></button>
        <a href="obrasCerradas.php" class="btn btn-sm btn-outline-secondary" >Ver obras cerradas ></a>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<div class="bg-white container-md my-3 rounded-3 p-4">
    <!----------- FILTROS ----------
    <div class="d-flex align-items-center">
        <div class="row align-items-center g-4">
            <?php include "../components/empresaFilter.php"; ?>

            <select id="id_estado" onchange="filterHandler()" class="form-select col">
                <option selected value="">Todos los estados</option>
                <?php
                $s_obras_estados = $mysqli->query("SELECT * FROM obras_estados");
                while ($row = $s_obras_estados->fetch_assoc()) {
                    $s_id_estado = $row['id'];
                    $s_estado = $row['estado'];
                    $selected = "";
                    if ($f_id_estado == $s_id_estado) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_estado'>$s_estado</option>";
                }
                ?>
            </select>
        </div>
        <div style="min-width: 200px" class="ms-auto align-items-center d-flex gap-2">
            <select class="selectizeSearch" id="id_contacto" onchange="filterHandler()">
                <option selected value="">Todos los clientes</option>
                <?php
                $s_contactos = $mysqli->query("SELECT * FROM contactos where is_active=1");
                while ($row = $s_contactos->fetch_assoc()) {
                    $s_id_contacto = $row['id'];
                    $s_nombre_contacto = $row['nombre'];

                    $selected = "";
                    if ($f_id_contacto == $s_id_contacto) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_contacto'>$s_nombre_contacto</option>";
                }
                ?>
            </select>
        </div>
    </div>
   --------- END FILTROS ----------->

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
            $c0 = $mysqli->query("SELECT * FROM obras where is_active=1 and (id_estado=1 or id_estado=3 or id_estado=6) order by fecha_inicio desc");
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
                $countSubpartidasTotal = 0;
                $countSubpartidasCheckedTotal = 0;

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