<?php
session_start();

include "../conexion.php";
require_once "../authCookieSessionValidate.php";
require_once "../DateClass.php";

$dateClass = new DateClass();

if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_presupuestos_partidas = filter_input(INPUT_GET, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);
$id_obra = filter_input(INPUT_GET, 'id_obra', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos_partidas where id=$id_presupuestos_partidas");
while ($row = $c0->fetch_assoc()) {
    $id_capitulo = $row['id_capitulo'];
    $id_presupuesto = $row['id_presupuesto'];
    $partida = $row['partida'];
    $descripcion = $row['descripcion'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
    $cantidad_partida = $row['cantidad'];
}

$c1 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
while ($row = $c1->fetch_assoc()) {
    $capitulo = $row['capitulo'];
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
<div class="container mt-3 ">
    <div class="d-flex gap-2 align-items-center">
        <a href="obraPlanning.php?id_obra=<?= $id_obra; ?>"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Detalle planning</p>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="row g-5">
        <div class="col-7">
            <div>
                <p class="text-uppercase fw-bold fs-6"><?= $capitulo; ?></p>
                <p class="mt-1"><?= $partida; ?></p>
                <p class="mt-2"><?= $descripcion; ?></p>
            </div>
        </div>
        <div class="col-5 d-flex align-items-center justify-content-end gap-2">
            <div class="w-100">
                <div class="form-floating">
                    <input onchange="updateGenerico('presupuestos_partidas','fecha_inicio',<?= $id_presupuestos_partidas; ?>,this.value)"
                           type="date"
                           class="form-control" value="<?= $fecha_inicio; ?>">
                    <label>Fecha inicio</label>
                </div>
            </div>
            <div class="w-100">
                <div class="form-floating">
                    <input onchange="updateGenerico('presupuestos_partidas','fecha_vencimiento',<?= $id_presupuestos_partidas; ?>,this.value)"
                           type="date"
                           class="form-control" value="<?= $fecha_vencimiento; ?>">
                    <label>Fecha vencimiento</label>
                </div>
            </div>
        </div>
    </div>
    <!----------- END INFO ----------->


    <!----------- MANO DE OBRA ----------->
    <div class="bg-white container-md my-3 rounded-3 p-4">
        <p class="text-black fw-bold fs-5 mb-3">Tareas</p>

        <!----------- TABLE ----------->
        <table class="table tableComon">
            <thead>
            <tr>
                <th width="30px" scope="col"></th>
                <th scope="col">Concepto</th>
                <th scope="col">Descripcion</th>
                <th scope="col">Empleados</th>
                <th scope="col">Vencimiento</th>
                <th scope="col">Prox. Intervención</th>
                <th scope="col">Ppto</th>
                <th scope="col">Registro</th>
                <th width="40px" scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $c3 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas='$id_presupuestos_partidas' and id_categoria=1"); // 1=mano obra
            while ($row = $c3->fetch_assoc()) {
                $id_presupuestos_subpartidas = $row['id'];
                $concepto_subpartida = $row['concepto'];
                $descripcion_subpartida = $row['descripcion'];
                $id_unidad_subpartida = $row['id_unidad'];
                $cantidad_subpartida = $row['cantidad'];
                $precio_subpartida = $row['precio'];
                $descuento_subpartida = $row['descuento'];
                $id_iva_subpartida = $row['id_iva'];
                $subtotal_subpartida = $row['subtotal'];
                $total_subpartida = $row['total'];
                $fecha_vencimiento_subpartida = $row['fecha_vencimiento'];
                $fecha_prox_intervencion = $row['fecha_prox_intervencion'];
                $is_checked = $row['is_checked'];

                $cantidad_presupuesto = round($cantidad_partida * $cantidad_subpartida, 2);

                // para hacer la equivalencia por ejemplo de 2.50 a 2:30h
                $horas = "$cantidad_presupuesto";
                $horas = str_replace(".", ",", $horas);
                list($horas, $minutos) = explode(",", $horas);
                $minutos = round($minutos * 0.6);


                if ($id_unidad_subpartida) {
                    $c5 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad_subpartida");
                    while ($row = $c5->fetch_assoc()) {
                        $simbolo_ud_material = $row['simbolo'];
                    }
                }

                $horas_registradas = "";
                $c6 = $mysqli->query("SELECT * FROM obras_registro_partes where id_presupuestos_subpartidas='$id_presupuestos_subpartidas' and id_obra=$id_obra");
                while ($row = $c6->fetch_assoc()) {
                    $horas_registradas = $row['horas'];
                    $horas_registradas = $dateClass->getSecondsFromFormatedHour("$horas_registradas");
                    $total_horasRegistradas += $horas_registradas;
                }

                if ($total_horasRegistradas) {
                    // $total_horasRegistradas = gmdate("H:i", "$total_horasRegistradas");

                    $horas2 = floor($total_horasRegistradas / 3600); // Calcula las horas completas
                    $minutos2 = floor(($total_horasRegistradas % 3600) / 60); // Calcula los minutos restantes

                    $total_horasRegistradas = sprintf("%02d:%02d", $horas2, $minutos2);
                }
                include "../components/modals/obraAssignEmpleadoToTask.php";
                include "../components/obras/tareaLine.php";
            }
            if ($c3->num_rows == 0) {
                include "../components/noDataLine.php";
            }
            ?>
            </tbody>
        </table>
        <!----------- END TABLE ----------->
    </div>
    <!----------- END MANO DE OBRA ----------->
</div>
<!----------- MANO EXTERNA ---------->
<div class="bg-white container-md my-3 rounded-3 p-4 mt-4">
    <p class="text-black fw-bold fs-5 mb-3">Trabajo externo</p>

    <table class="table tableComon">
        <thead>
        <tr>
            <th width="30px" scope="col"></th>
            <th scope="col">Concepto</th>
            <th scope="col">Descripcion</th>
            <th scope="col">Contacto</th>
            <th width="150px" scope="col">Vencimiento</th>
            <!--<th width="100px" scope="col">Ppto</th>-->
        </tr>
        </thead>
        <tbody>
        <?php
        $c33 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas and id_categoria=3");
        while ($row = $c33->fetch_assoc()) {
            $id_presupuestos_subpartidas = $row['id'];
            $concepto_subpartida = $row['concepto'];
            $id_contacto = $row['id_contacto'];
            $descripcion_subpartida = $row['descripcion'];
            $id_unidad_subpartida = $row['id_unidad'];
            $cantidad_subpartida = $row['cantidad'];
            $precio_subpartida = $row['precio'];
            $descuento_subpartida = $row['descuento'];
            $id_iva_subpartida = $row['id_iva'];
            $subtotal_subpartida = $row['subtotal'];
            $total_subpartida = $row['total'];
            $fecha_vencimiento_subpartida = $row['fecha_vencimiento'];
            $is_checked = $row['is_checked'];

            if ($id_unidad_subpartida) {
                $c55 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad_subpartida");
                while ($row = $c55->fetch_assoc()) {
                    $simbolo_ud_material = $row['simbolo'];
                }
            }

            include "../components/obras/tareaLine2.php";
        }
        if ($c33->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
</div>
<!---------- END MANO EXTERNA ----------->


<?php include "../components/updateConfirmation.php"; ?>
</body>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/updateGenerico.js"></script>
</html>