<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_presupuestos_partidas = filter_input(INPUT_GET, 'id_presupuestos_partidas', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos_partidas where id=$id_presupuestos_partidas");
while ($row = $c0->fetch_assoc()) {
    $id_partida = $row['id_partida'];
    $id_presupuesto = $row['id_presupuesto'];
    $id_capitulo = $row['id_capitulo'];
    $id_unidad = $row['id_unidad'];
    $partida = $row['partida'];
    $descripcion = $row['descripcion'];
    $cantidadPartida = $row['cantidad'];
    $subtotal = $row['subtotal'];
    $total = $row['total'];
    $subtotal_x_cantidad = $cantidadPartida * $subtotal;
    $subtotalPartida += $subtotal;
    $totalPartida += $total;
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
<div class="container mt-3">
    <div class="d-flex gap-2 align-items-center">
        <a class="pointer" onclick="location.href=document.referrer"><i
                    class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Editar partida</p>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-3">
            <div class="form-floating">
                <input disabled readonly type="text" class="form-control" placeholder="Capítulo"
                       value="<?= $capitulo; ?>">
                <label>Capítulo</label>
            </div>
        </div>
        <div class="col-9">
            <div class="form-floating">
                <input onfocusout="updateGenerico('presupuestos_partidas','partida',<?= $id_presupuestos_partidas; ?>,this.value)"
                       type="text" class="form-control" placeholder="Partida"
                       value="<?= $partida; ?>">
                <label>Partida</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onfocusout="updateGenerico('presupuestos_partidas','cantidad',<?= $id_presupuestos_partidas; ?>,this.value)"
                       type="number" class="form-control" placeholder="Cantidad"
                       value="<?= $cantidadPartida; ?>">
                <label>Cantidad</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('presupuestos_partidas','id_unidad',<?= $id_presupuestos_partidas; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Unidad</option>
                    <?php
                    $c2 = $mysqli->query("SELECT * FROM unidades");
                    while ($row = $c2->fetch_assoc()) {
                        $id_unidades_partida = $row['id'];
                        $unidad = $row['unidad'];
                        $simbolo = $row['simbolo'];

                        $selected = "";
                        if ($id_unidades_partida == $id_unidad) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_unidades_partida'>$simbolo</option>";
                    }
                    ?>
                </select>
                <label>Unidad</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <textarea
                        onfocusout="updateGenerico('presupuestos_partidas','descripcion',<?= $id_presupuestos_partidas; ?>,this.value)"
                        class="form-control" placeholder="Descripción"
                        style="height: 100px"><?= $descripcion; ?></textarea>
                <label>Descripción</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- CONTAINER ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <button onclick="presupuestoAddSubpartida(<?= $id_presupuestos_partidas; ?>,<?= $id_partida; ?>)"
            class="btn btn-sm btn-outline-primary d-flex ms-auto mb-3">+ Añadir línea
    </button>
    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th width="90px" scope="col">Categoría</th>
            <th scope="col">Concepto</th>
            <th scope="col">Descripción</th>
            <th width="90px" scope="col">Ud</th>
            <th width="100px" scope="col">Cantidad</th>
            <th width="100px" scope="col">Precio</th>
            <th width="90px" scope="col">IVA</th>
            <th width="100px" scope="col">Beneficio (%)</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Total</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $c3 = $mysqli->query("SELECT * FROM presupuestos_subpartidas where id_presupuesto_partidas=$id_presupuestos_partidas");
        while ($row = $c3->fetch_assoc()) {
            $id_presupuestos_subpartidas = $row['id'];
            $id_categoria = $row['id_categoria'];
            $concepto = $row['concepto'];
            $descripcion_subpartida = $row['descripcion'];
            $id_unidad_subpartida = $row['id_unidad'];
            $cantidad = $row['cantidad'];
            $id_iva_subpartida = $row['id_iva'];
            $precio = $row['precio'];
            $subtotal = $row['subtotal'];
            $descuento = $row['descuento'];
            $total = $row['total'];

            include "../components/presupuestos/subpartidaLine.php";
        }
        if ($c3->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>

        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END CONTAINER ----------->


<!----------- RESUMEN ----------->
<div id="result" class="container-md">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 my-3 ms-auto" style="width: fit-content">
        <div>
            <p id="subtotal" class="fs-3 text-black fw-bold"><?= round($subtotalPartida, 2); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Precio Ud</p>
        </div>
        <div>
            <p id="total" class="fs-3 text-black fw-bold"><?= round($subtotalPartida * $cantidadPartida, 2); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Subtotal</p>
        </div>
        <div>
            <p id="iva"
               class="fs-3 text-black fw-bold"><?= round(($totalPartida - $subtotalPartida) * $cantidadPartida, 2); ?>
                €</p>
            <p class="letraPeq" style="color: #AEAEAE">IVA</p>
        </div>
        <div>
            <p id="total" id="subtotal_x_cantidad"
               class="fs-3 text-black fw-bold"><?= round($totalPartida * $cantidadPartida, 2); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Total.</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->

<div class="container text-end">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#presupuestoDeletePartida"><i
                class="bi bi-trash-fill me-2"></i>Eliminar partida
    </button>
</div>

</body>
<?php include "../components/modals/presupuestoDeletePartida.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>

<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>

<script>
    const updateGenerico = (tabla, columna, fila, valor) => {
        $.ajax({
            method: "POST",
            url: "../backend/updateGenerico.php",
            data: {
                tabla: tabla,
                columna: columna,
                fila: fila,
                valor: valor
            }
        }).done(function () {
            if (columna == "cantidad" || columna == "precio" || columna == "id_iva" || columna == "descuento") {
                calculateTotal(fila);
                $("#result").load(location.href + " #result");
            } else {
                showMessage();
            }
        });
    }

    const presupuestoAddSubpartida = (id_presupuestos_partidas, id_partida) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/presupuestoAddSubpartida.php",
            data: {
                id_presupuestos_partidas: id_presupuestos_partidas,
                id_partida: id_partida,
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }

    const presupuestoDeletePartida = (id_presupuestos_partidas) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/presupuestoDeletePartida.php",
            data: {
                id_presupuestos_partidas: id_presupuestos_partidas
            }
        }).done(function () {
            location.href = "presupuestoDetail.php?id_presupuesto=<?=$id_presupuesto;?>";
        });
    }

    const presupuestoDeleteSubpartida = (id_presupuestos_subpartidas) => {
        $.ajax({
            method: "POST",
            dataType: 'json',
            url: "../backend/presupuestos/presupuestoDeleteSubpartida.php",
            data: {
                <?php if($id_partida){ ?>id_partida: <?=$id_partida;?>, <?php } ?>
                id_presupuestos_subpartidas: id_presupuestos_subpartidas
            }
        }).done(function (data) {
            $(".tableComon").load(location.href + " .tableComon");

            totalPartida = data['totalPartida'];
            subtotalPartida = data['subtotalPartida'];
            $("#subtotal").text(subtotalPartida + "€");
            $("#total").text(totalPartida + "€");
        });
    }

    // Calcular subtotal y total de las subpartidas y la partida genérica
    const calculateTotal = (id_presupuestos_subpartidas) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/calculateTotal.php",
            dataType: 'json',
            data: {
                id_presupuestos_subpartidas: id_presupuestos_subpartidas
            }
        }).done(function (data) {
            subtotalSubpartida = data['subtotalSubpartida'];
            totalSubpartida = data['totalSubpartida'];
            totalPartida = data['totalPartida'];
            subtotalPartida = data['subtotalPartida'];

            $("#subtotal" + id_presupuestos_subpartidas).text(subtotalSubpartida + "€");
            $("#total" + id_presupuestos_subpartidas).text(totalSubpartida + "€");
            $("#result").load(location.href + " #result");
        });
    }

    // Calcular el resultado de cantidad x total->(viene de las subpartidas)
    const calculateTotalxCantidad = (id_presupuestos_partidas, cantidad) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/calculateTotalxCantidad.php",
            dataType: 'json',
            data: {
                id_presupuestos_partidas: id_presupuestos_partidas,
                cantidad: cantidad
            }
        }).done(function (data) {
            subtotalPresupuesto = data['subtotalPresupuesto'];
            totalPresupuesto = data['totalPresupuesto'];
            total_x_cantidad = data['total_x_cantidad'];
            subtotal_x_cantidad = data['subtotal_x_cantidad'];

            $("#subtotal_x_cantidad" + id_presupuestos_partidas).text(subtotal_x_cantidad + "€");
            $("#result").load(location.href + " #result");
        });
    }
</script>
</html>