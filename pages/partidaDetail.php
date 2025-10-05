<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_partida = filter_input(INPUT_GET, 'id_partida', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gestor_partidas where id=$id_partida");
while ($row = $c0->fetch_assoc()) {
    $partida = $row['partida'];
    $codigo = $row['codigo'];
    $id_unidad = $row['id_unidad'];
    $descripcion = $row['descripcion'];
    $subtotal = $row['subtotal'];
    $total = $row['total'];
    $subtotalPartida += $subtotal;
    $totalPartida += $total;
}

if ($id_unidad) {
    $c1 = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
    while ($row = $c1->fetch_assoc()) {
        $simbolo = $row['simbolo'];
        $unidad = $row['unidad'];
    }
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
        <div class="col-7">
            <div class="form-floating">
                <input onfocusout="updateGenerico('gestor_partidas','partida',<?= $id_partida; ?>,this.value)"
                       type="text" class="form-control" placeholder="Partida" value="<?= $partida; ?>">
                <label>Partida</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('gestor_partidas','codigo',<?= $id_partida; ?>,this.value)"
                       type="text" class="form-control" placeholder="Código" value="<?= $codigo; ?>">
                <label>Código</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <select onchange="updateGenerico('gestor_partidas','id_unidad',<?= $id_partida; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Unidad</option>
                    <?php
                    $c2 = $mysqli->query("SELECT * FROM unidades");
                    while ($row = $c2->fetch_assoc()) {
                        $id_unidades = $row['id'];
                        $unidad = $row['unidad'];
                        $simbolo = $row['simbolo'];

                        $selected = "";
                        if ($id_unidades == $id_unidad) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_unidades'>$simbolo</option>";
                    }
                    ?>
                </select>
                <label>Unidad</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <textarea onfocusout="updateGenerico('gestor_partidas','descripcion',<?= $id_partida; ?>,this.value)"
                          class="form-control" placeholder="Descripción"
                          style="height: 100px"><?= $descripcion; ?></textarea>
                <label>Descripción</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->




<!----------- MAIN ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <div class="text-end mb-3">
        <button onclick="subpartidaAddLine(<?= $id_partida; ?>)" class="btn btn-primary btn-sm">+ Añadir
            línea
        </button>
    </div>

    <!----------- TABLE ----------->
    <table class="table tableComon">
        <thead>
        <tr>
            <th scope="col">Categoría</th>
            <th scope="col">Concepto</th>
            <th scope="col">Descripción</th>
            <th width="80px" scope="col">Ud</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Precio</th>
            <th width="90px" scope="col">IVA</th>
            <th scope="col">Subtotal</th>
            <th scope="col">Total</th>
            <th width="40px" scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $c3 = $mysqli->query("SELECT * FROM gestor_subpartidas where id_partida=$id_partida");
        while ($row = $c3->fetch_assoc()) {
            $id_subpartida = $row['id'];
            $id_categoria = $row['id_categoria'];
            $concepto = $row['concepto'];
            $descripcion_subpartida = $row['descripcion'];
            $id_unidad_subpartida = $row['id_unidad'];
            $cantidad = $row['cantidad'];
            $id_iva_subpartida = $row['id_iva'];
            $precio = $row['precio'];
            $subtotal = $row['subtotal'];
            $total = $row['total'];

            include "../components/gestorPartidas/subpartidaLine.php";
        }
        if ($c3->num_rows == 0) {
            include "../components/noDataLine.php";
        }
        ?>
        </tbody>
    </table>
    <!----------- END TABLE ----------->
</div>
<!----------- END MAIN ----------->




<!----------- RESUMEN ----------->
<div class="container-md">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 ms-auto" style="width: fit-content">
        <div>
            <p id="subtotal" class="fs-3 text-black fw-bold"><?= $subtotalPartida; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Subtotal</p>
        </div>
        <div>
            <p id="total" class="fs-3 text-black fw-bold"><?= $totalPartida; ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Total</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->




<div class="container text-end mt-3">
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#partidaDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>




<?php include "../components/updateConfirmation.php"; ?>
<?php include "../components/modals/partidaDelete.php"; ?>
</body>
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
            if (columna == "cantidad" || columna == "precio" || columna == "id_iva") {
                calculateTotal(fila);
            } else {
                showMessage();
            }
        });
    }

    const subpartidaAddLine = (id_partida) => {
        $.ajax({
            method: "POST",
            url: "../backend/gestorPartidas/subpartidaAddLine.php",
            data: {
                id_partida: id_partida
            }
        }).done(function () {
            $(".tableComon").load(location.href + " .tableComon");
        });
    }

    const partidaDelete = (id_partida) => {
        $.ajax({
            method: "POST",
            url: "../backend/gestorPartidas/partidaDelete.php",
            data: {
                id_partida: id_partida
            }
        }).done(function () {
            location.href = "gestorPartidas.php";
        });
    }

    const subpartidaDeleteLine = (id_subpartida) => {
        $.ajax({
            method: "POST",
            dataType: 'json',
            url: "../backend/gestorPartidas/subpartidaDeleteLine.php",
            data: {
                id_subpartida: id_subpartida,
                id_partida: <?=$id_partida;?>
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
    const calculateTotal = (id_subpartida) => {
        $.ajax({
            method: "POST",
            url: "../backend/gestorPartidas/calculateTotal.php",
            dataType: 'json',
            data: {
                id_subpartida: id_subpartida
            }
        }).done(function (data) {
            subtotalSubpartida = data['subtotalSubpartida'];
            totalSubpartida = data['totalSubpartida'];
            totalPartida = data['totalPartida'];
            subtotalPartida = data['subtotalPartida'];
            $("#subtotal" + id_subpartida).text(subtotalSubpartida + "€");
            $("#total" + id_subpartida).text(totalSubpartida + "€");
            $("#subtotal").text(subtotalPartida + "€");
            $("#total").text(totalPartida + "€");
        });
    }
</script>
</html>