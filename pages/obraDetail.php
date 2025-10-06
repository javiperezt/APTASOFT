<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

require_once "../ConsecutiveNumbers.php";
//genera numero automatico en presupuesto
$consecutiveNumber = new ConsecutiveNumbers();

include "../backend/facturas/facturaGetConsecutiveNumber.php";
include "../backend/presupuestos/presupuestoGetConsecutiveNumber.php";


$c0 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c0->fetch_assoc()) {
    $id_contacto_obra = $row['id_contacto'];
    $id_empresa_obra = $row['id_empresa'];
    $id_canal_entrada = $row['id_canal_entrada'];
    $id_usuario_asignado = $row['id_usuario_asignado'];
    $id_estado = $row['id_estado'];
    $titulo = $row['titulo'];
    $referido = $row['referido'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_fin = $row['fecha_fin'];
    $localizacion = $row['localizacion'];
}

$c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto_obra");
while ($row = $c1->fetch_assoc()) {
    $nombre_contacto = $row['nombre'];
}

$c2 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa_obra");
while ($row = $c2->fetch_assoc()) {
    $empresa = $row['empresa'];
}
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
        <p class="text-black fw-bold fs-4 m-0">Detalle Obra</p>
    </div>
    <div>
        <!----------- NAV OBRAS ----------->
        <?php include "../components/obras/navObra.php"; ?>
        <!----------- END NAV OBRAS ----------->
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-6">
            <div class="form-floating">
                <input onfocusout="updateGenerico('obras','titulo',<?= $id_obra; ?>,this.value)" type="text"
                       class="form-control" placeholder="Título" value="<?= $titulo; ?>">
                <label>Título</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('obras','id_estado',<?= $id_obra; ?>,this.value)" class="form-select">
                    <option selected disabled hidden>Estado</option>
                    <?php
                    $c3 = $mysqli->query("SELECT * FROM obras_estados");
                    while ($row = $c3->fetch_assoc()) {
                        $id_obras_estados = $row['id'];
                        $estado = $row['estado'];
                        $selected = "";
                        if ($id_estado == $id_obras_estados) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_obras_estados'>$estado</option>";
                    }
                    ?>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('obras','fecha_inicio',<?= $id_obra; ?>,this.value)" type="date"
                       class="form-control" value="<?= $fecha_inicio; ?>">
                <label>Fecha</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('obras','fecha_fin',<?= $id_obra; ?>,this.value)" type="date"
                       class="form-control" value="<?= $fecha_fin; ?>">
                <label>Vencimiento</label>
            </div>
        </div>
        <div class="col-3">
            <select class="selectizeSearch selectizeSearchBig" id="id_contacto"
                    onchange="updateGenerico('obras','id_contacto',<?= $id_obra; ?>,this.value)">
                <option selected value="">Todos los clientes</option>
                <?php
                $s_contactos = $mysqli->query("SELECT * FROM contactos");
                while ($row = $s_contactos->fetch_assoc()) {
                    $s_id_contacto = $row['id'];
                    $s_nombre_contacto = $row['nombre'];

                    $selected = "";
                    if ($id_contacto_obra == $s_id_contacto) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_contacto'>$s_nombre_contacto</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('obras','id_canal_entrada',<?= $id_obra; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Canal de entrada</option>
                    <?php
                    $c4 = $mysqli->query("SELECT * FROM obras_canales_entrada");
                    while ($row = $c4->fetch_assoc()) {
                        $id_obras_canales_entrada = $row['id'];
                        $canal = $row['canal'];
                        $selected = "";
                        if ($id_obras_canales_entrada == $id_canal_entrada) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_obras_canales_entrada'>$canal</option>";
                    }
                    ?>
                </select>
                <label>Canal de entrada</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <div class="form-floating">
                    <select onchange="updateGenerico('obras','id_empresa',<?= $id_obra; ?>,this.value)"
                            class="form-select">
                        <option selected disabled hidden>Empresa</option>
                        <?php
                        $c_empresas = $mysqli->query("SELECT * FROM empresas");
                        while ($row = $c_empresas->fetch_assoc()) {
                            $s_id_empresa = $row['id'];
                            $s_empresa = $row['empresa'];
                            $selected = "";
                            if ($id_empresa_obra == $s_id_empresa) {
                                $selected = "selected";
                            }
                            echo "<option $selected value='$s_id_empresa'>$s_empresa</option>";
                        }
                        ?>
                    </select>
                    <label>Empresa</label>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('obras','localizacion',<?= $id_obra; ?>,this.value)" type="text"
                       class="form-control" placeholder="Localización"
                       value="<?= $localizacion; ?>">
                <label>Localización</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('obras','referido',<?= $id_obra; ?>,this.value)" type="text"
                       class="form-control" placeholder="Referido por" value="<?= $referido; ?>">
                <label>Referido por</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- PRESUPUESTOS ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-5 ">Presupuestos</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#presupuestoNew">+ Añadir
            presupuesto
        </button>
    </div>

    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Vence</th>
            <th scope="col">Referencia</th>
            <th scope="col">Cliente</th>
            <th scope="col">Obra</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Total</th>
            <th scope="col">Estado</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cPresupuestos = $mysqli->query("SELECT * FROM presupuestos where id_obra=$id_obra and is_active=1");
        while ($row = $cPresupuestos->fetch_assoc()) {
            $id_presupuesto = $row['id'];
            $id_contacto = $row['id_contacto'];
            $id_estado = $row['id_estado'];
            $pref_ref = $row['pref_ref'];
            $pref_ref_year = $row['pref_ref_year'];
            $ref = $row['ref'];
            $ref = $pref_ref . $pref_ref_year . $ref;
            $fecha_inicio = $row['fecha_inicio'];
            $fecha_vencimiento = $row['fecha_vencimiento'];

            // subtotal presupuesto
            $q = mysqli_query($mysqli, "SELECT SUM(cantidad*subtotal) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
            $result = mysqli_fetch_assoc($q);
            $subtotal = round($result['x'] ?? 0, 2);

            // total presupuesto
            $q = mysqli_query($mysqli, "SELECT SUM(cantidad*total) AS x FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
            $result = mysqli_fetch_assoc($q);
            $total = round($result['x'] ?? 0, 2);

            $c1_presupuestos = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
            while ($row = $c1_presupuestos->fetch_assoc()) {
                $nombre_contacto = $row['nombre'];
            }

            $c2_presupuestos = $mysqli->query("SELECT * FROM obras where id=$id_obra");
            while ($row = $c2_presupuestos->fetch_assoc()) {
                $titulo_obra = $row['titulo'];
            }

            if ($id_estado) {
                $c3_presupuestos = $mysqli->query("SELECT * FROM presupuestos_estados where id=$id_estado");
                while ($row = $c3_presupuestos->fetch_assoc()) {
                    $estado = $row['estado'];
                }
            }

            $classEtiq = "text-bg-warning";
            if ($id_estado == 1) {
                $classEtiq = "text-bg-warning";
            }
            if ($id_estado == 2) {
                $classEtiq = "text-bg-primary";
            }
            if ($id_estado == 3) {
                $classEtiq = "text-bg-danger";
            }
            include "../components/presupuestos/presupuestoLine2.php";
        }
        if ($cPresupuestos->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END PRESUPUESTOS ----------->


<!----------- CERTIFICACIONES ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-5 ">Certificaciones</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#certificacionNew">+ Añadir
            certificación
        </button>
    </div>

    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Código</th>
            <th width="50%" scope="col">Concepto</th>
            <th width="10%" scope="col">Estado</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cCertificaciones = $mysqli->query("SELECT * FROM certificaciones where id_obra=$id_obra and is_active=1");
        while ($row = $cCertificaciones->fetch_assoc()) {
            $id_certificacion = $row['id'];
            $id_obra = $row['id_obra'];
            $concepto = $row['concepto'];
            $codigo = $row['codigo'];
            $id_estado = $row['id_estado'];
            $fecha = $row['fecha'];

            if ($id_estado) {
                $c2_Certificaciones = $mysqli->query("SELECT * FROM certificaciones_estados where id=$id_estado");
                while ($row = $c2_Certificaciones->fetch_assoc()) {
                    $estado_certificacion = $row['estado'];
                }
            }

            $classEtiq = "text-bg-warning";
            if ($id_estado == 1) {
                $classEtiq = "text-bg-warning";
            }
            if ($id_estado == 2) {
                $classEtiq = "text-bg-primary";
            }

            include "../components/obras/certificacionLine.php";
        }
        if ($cCertificaciones->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END CERTIFICACIONES ----------->


<!----------- FACTURAS ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-5 ">Facturas</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#facturaNew">+ Añadir
            factura
        </button>
    </div>

    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Vence</th>
            <th scope="col">Código</th>
            <th scope="col">Cliente</th>
            <th scope="col">Obra</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Total</th>
            <th scope="col">Estado</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cFacturas = $mysqli->query("SELECT * FROM facturas where id_obra=$id_obra and is_active=1");
        while ($row = $cFacturas->fetch_assoc()) {
            $id_factura = $row['id'];
            $id_contacto = $row['id_contacto'];
            $id_obra = $row['id_obra'];
            $id_estado = $row['id_estado'];
            $pref_ref = $row['pref_ref'];
            $pref_ref_year = $row['pref_ref_year'];
            $ref = $row['ref'];
            $ref = $pref_ref . $pref_ref_year . $ref;
            $fecha_inicio = $row['fecha_inicio'];
            $fecha_vencimiento = $row['fecha_vencimiento'];

            // subtotal factura
            $q = mysqli_query($mysqli, "SELECT SUM(subtotal) AS x FROM facturas_partidas where id_factura='$id_factura'");
            $result = mysqli_fetch_assoc($q);
            $subtotal = round($result['x'] ?? 0, 2);

            // total factura
            $q = mysqli_query($mysqli, "SELECT SUM(total) AS x FROM facturas_partidas where id_factura='$id_factura'");
            $result = mysqli_fetch_assoc($q);
            $total = round($result['x'] ?? 0, 2);

            $c1_facturas = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
            while ($row = $c1_facturas->fetch_assoc()) {
                $nombre_contacto = $row['nombre'];
            }

            $c2_facturas = $mysqli->query("SELECT * FROM obras where id=$id_obra");
            while ($row = $c2_facturas->fetch_assoc()) {
                $titulo_obra = $row['titulo'];
            }

            if ($id_estado) {
                $c3_facturas = $mysqli->query("SELECT * FROM facturas_estados where id=$id_estado");
                while ($row = $c3_facturas->fetch_assoc()) {
                    $estado = $row['estado'];
                }
            }

            $classEtiq = "text-bg-warning";
            if ($id_estado == 1) {
                $classEtiq = "text-bg-warning";
            }
            if ($id_estado == 2) {
                $classEtiq = "text-bg-primary";
            }
            if ($id_estado == 3) {
                $classEtiq = "text-bg-danger";
            }
            include "../components/facturas/facturaLine.php";
        }
        if ($cFacturas->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END FACTURAS ----------->


<!----------- GASTOS ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-black fw-bold fs-5 ">Gastos</p>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gastoNew">+ Añadir
            gasto
        </button>
    </div>

    <!----------- TABLE ----------->
    <table id="tabla_proyectos" class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Empresa</th>
            <th scope="col">Fecha</th>
            <th scope="col">Vence</th>
            <th scope="col">Código</th>
            <th scope="col">Proveedor</th>
            <th scope="col">Obra</th>
            <th scope="col">Tipo</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Total</th>
            <th scope="col">Estado</th>
            <th width="10px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cGastos = $mysqli->query("SELECT * FROM gastos where is_active=1 and id_obra=$id_obra");
        while ($row = $cGastos->fetch_assoc()) {
            $id_gasto = $row['id'];
            $id_contacto = $row['id_contacto'];
            $id_categoria_gasto = $row['id_categoria_gasto'];
            $id_cuenta = $row['id_cuenta'];
            $id_obra = $row['id_obra'];
            $id_estado = $row['id_estado'];
            $id_empresa = $row['id_empresa'];
            $codigo = $row['codigo'];
            $fecha_inicio = $row['fecha_inicio'];
            $fecha_vencimiento = $row['fecha_vencimiento'];
            $comentario = $row['comentario'];
            $creation_date = $row['creation_date'];

            // subtotal gastos
            $q = mysqli_query($mysqli, "SELECT SUM(subtotal) AS x FROM gastos_lineas where id_gasto='$id_gasto'");
            $result = mysqli_fetch_assoc($q);
            $subtotal = round($result['x'] ?? 0, 2);

            // total gastos
            $q = mysqli_query($mysqli, "SELECT SUM(total) AS x FROM gastos_lineas where id_gasto='$id_gasto'");
            $result = mysqli_fetch_assoc($q);
            $total = round($result['x'] ?? 0, 2);

            if ($id_categoria_gasto) {
                $c1_gastos_categorias = $mysqli->query("SELECT * FROM gastos_categorias where id=$id_categoria_gasto");
                while ($row = $c1_gastos_categorias->fetch_assoc()) {
                    $categoria_gasto = $row['categoria'];
                }
            }

            if ($id_contacto) {
                $c1_gastos = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
                while ($row = $c1_gastos->fetch_assoc()) {
                    $nombre_contacto = $row['nombre'];
                }
            }

            $c2_gastos = $mysqli->query("SELECT * FROM obras where id=$id_obra");
            while ($row = $c2_gastos->fetch_assoc()) {
                $titulo_obra = $row['titulo'];
            }

            if ($id_estado) {
                $c3_gastos_estados = $mysqli->query("SELECT * FROM gastos_estados where id=$id_estado");
                while ($row = $c3_gastos_estados->fetch_assoc()) {
                    $estado = $row['estado'];
                }
            }

            $classEtiq = "text-bg-warning";
            if ($id_estado == 2) {
                $classEtiq = "text-bg-primary";
            }

            include "../components/gastos/gastoLine.php";
        }

        if ($cGastos->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END GASTOS ----------->


<div class="container text-end mt-3">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#obraDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

<?php include "../components/modals/presupuestoNew.php"; ?>
<?php include "../components/modals/certificacionNew.php"; ?>
<?php include "../components/modals/facturaNew.php"; ?>
<?php include "../components/modals/gastoNew.php"; ?>
<?php include "../components/modals/obraDelete.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>
</body>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/updateGenerico.js"></script>

<script>
    // Eliminar obra completa
    const obraDelete = (id_obra) => {
        $.ajax({
            method: "POST",
            url: "../backend/obras/obraDelete.php",
            data: {
                id_obra: id_obra
            }
        }).done(function () {
            location.href = "obras.php";
        });
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