<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_gasto = filter_input(INPUT_GET, 'id_gasto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM gastos where id=$id_gasto");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_categoria_gasto = $row['id_categoria_gasto'];
    $id_cuenta = $row['id_cuenta'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $codigo = $row['codigo'];
    $retencion = $row['retencion'];
    $fecha_inicio = $row['fecha_inicio'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
    $comentario = $row['comentario'];
    $creation_date = $row['creation_date'];
}

$c1 = $mysqli->query("SELECT * FROM empresas where id=$id_empresa");
while ($row = $c1->fetch_assoc()) {
    $empresa = $row['empresa'];
}

//Calculamos totales y subtotales de todas las lineas de gasto
$getSubtotalGastoGeneral = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalGastoGeneral FROM gastos_lineas where id_gasto='$id_gasto'");
$result = mysqli_fetch_assoc($getSubtotalGastoGeneral);
$subtotalGastoGeneral = $result['subtotalGastoGeneral'] ?? 0;

$getTotalGastoGeneral = mysqli_query($mysqli, "SELECT SUM(total) AS totalGastoGeneral FROM gastos_lineas where id_gasto='$id_gasto'");
$result = mysqli_fetch_assoc($getTotalGastoGeneral);
$totalGastoGeneral = $result['totalGastoGeneral'] ?? 0;

//para saber cantidad descontada
$getImporteSinDescuento = mysqli_query($mysqli, "SELECT SUM(cantidad*precio) AS importeSinDescuento FROM gastos_lineas where id_gasto='$id_gasto'");
$result = mysqli_fetch_assoc($getImporteSinDescuento);
$importeSinDescuento = $result['importeSinDescuento'] ?? 0;

$dto = $importeSinDescuento - $subtotalGastoGeneral;


$getTotalPagado = mysqli_query($mysqli, "SELECT SUM(importe) AS x FROM gastos_pagos where estado='pagado' and id_gasto=$id_gasto");
$result = mysqli_fetch_assoc($getTotalPagado);
$totalPagado = $result['x'] ?? 0;
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
        <a href="<?= $_SERVER['HTTP_REFERER']; ?>"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Gasto detalle</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-secondary btn-sm me-5" id="duplicateExpenseButton" data-expense-id="<?= $id_gasto; ?>">
            Duplicar Gasto
        </button>

        <form class="m-0" id="gastoDocs" action="../backend/gastos/gastoUploadDocs.php" method="post"
              enctype="multipart/form-data">
            <label class="btn btn-outline-secondary btn-sm" for="uploadFile">Importar documentos<i
                        class="bi bi-upload ms-2"></i></label>
            <input type="file" class="d-none" id="uploadFile" name="upload[]" multiple>
            <input type="hidden" value="<?= $ID_USER; ?>" name="uploaded_by">
            <input type="hidden" value="<?= $id_gasto; ?>" name="id_gasto">
        </form>
        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#gastoAddPago">+
            Añadir
            pago
        </button>
        <button onclick="gastoAddLine(<?= $id_gasto; ?>)" class="btn btn-primary btn-sm">+ Añadir línea</button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-4">
            <select onchange="updateGenerico('gastos','id_contacto',<?= $id_gasto; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <option selected disabled hidden>Contacto</option>
                <?php
                $getContactos = $mysqli->query("SELECT * FROM contactos");
                while ($row = $getContactos->fetch_assoc()) {
                    $s_id_contacto = $row['id'];
                    $s_nombre_contacto = $row['nombre'];

                    $selected = "";
                    if ($s_id_contacto == $id_contacto) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_contacto'>$s_nombre_contacto</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-4">
            <select onchange="updateGenerico('gastos','id_obra',<?= $id_gasto; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <?php
                $getObras = $mysqli->query("SELECT * FROM obras");
                while ($row = $getObras->fetch_assoc()) {
                    $s_id_obra = $row['id'];
                    $s_titulo = $row['titulo'];

                    $selected = "";
                    if ($id_obra == $s_id_obra) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_obra'>$s_titulo</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('gastos','fecha_inicio',<?= $id_gasto; ?>,this.value)" required
                       type="date" class="form-control" value="<?= $fecha_inicio; ?>">
                <label>Fecha</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('gastos','fecha_vencimiento',<?= $id_gasto; ?>,this.value)" type="date"
                       class="form-control" value="<?= $fecha_vencimiento; ?>">
                <label>Vencimiento</label>
            </div>
        </div>
        <div class="col-3">
            <select onchange="updateGenerico('gastos','id_cuenta',<?= $id_gasto; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <option selected disabled hidden>Asignar cuenta</option>
                <?php
                $getExpensesTypes = $mysqli->query("SELECT * FROM cuentas_gasto where is_active=1");
                while ($row = $getExpensesTypes->fetch_assoc()) {
                    $id_ingresos_cuentas = $row['id'];
                    $referencia_ingresos = $row['referencia'];
                    $tipo_ingresos = $row['nombre'];

                    $selected = "";
                    if ($id_cuenta == $id_ingresos_cuentas) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$id_ingresos_cuentas'>$referencia_ingresos. $tipo_ingresos</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('gastos','id_estado',<?= $id_gasto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Estado</option>
                    <?php
                    $getEstados = $mysqli->query("SELECT * FROM gastos_estados");
                    while ($row = $getEstados->fetch_assoc()) {
                        $s_id_gastos_estados = $row['id'];
                        $s_gastos_estado = $row['estado'];

                        $selected = "";
                        if ($s_id_gastos_estados == $id_estado) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$s_id_gastos_estados'>$s_gastos_estado</option>";
                    }
                    ?>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('gastos','id_empresa',<?= $id_gasto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Empresa</option>
                    <?php
                    $getEmpresas = $mysqli->query("SELECT * FROM empresas");
                    while ($row = $getEmpresas->fetch_assoc()) {
                        $id_empresa2 = $row['id'];
                        $empresa = $row['empresa'];

                        $selected = "";
                        if ($id_empresa == $id_empresa2) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_empresa2'>$empresa</option>";
                    }
                    ?>
                </select>
                <label>Empresa</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <input onfocusout="updateGenerico('gastos','codigo',<?= $id_gasto; ?>,this.value)" type="text"
                       class="form-control" value="<?= $codigo; ?>">
                <label>Código</label>
            </div>
        </div>
        <div class="col-4">
            <div class="form-floating">
                <select onchange="updateGenerico('gastos','id_categoria_gasto',<?= $id_gasto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Categoria</option>
                    <?php
                    $getCat = $mysqli->query("SELECT * FROM gastos_categorias");
                    while ($row = $getCat->fetch_assoc()) {
                        $s_id_gastos_categorias = $row['id'];
                        $s_categoria = $row['categoria'];

                        $selected = "";
                        if ($s_id_gastos_categorias == $id_categoria_gasto) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$s_id_gastos_categorias'>$s_categoria</option>";
                    }
                    ?>
                </select>
                <label>Categoria</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('gastos','retencion',<?= $id_gasto; ?>,this.value)"
                        class="form-select">
                    <option selected disabled hidden>Retención</option>
                    <option <?= $retencion == 0 ? "selected" : ""; ?> value='0'>0%</option>
                    <option <?= $retencion == 7 ? "selected" : ""; ?> value='7'>7%</option>
                    <option <?= $retencion == 15 ? "selected" : ""; ?> value='15'>15%</option>
                    <option <?= $retencion == 19 ? "selected" : ""; ?> value='19'>19%</option>
                </select>
                <label>Retención</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <textarea onfocusout="updateGenerico('gastos','comentario',<?= $id_gasto; ?>,this.value)"
                          class="form-control" placeholder="Comentario"
                          style="height: 100px"><?= $comentario; ?></textarea>
                <label>Comentario</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- MAIN ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">


    <!----------- TABLE ----------->
    <table id="gastoLines" class="table tableComon">
        <thead>
        <tr>
            <th width="25%" scope="col">Partida</th>
            <th scope="col">Concepto</th>
            <th scope="col">Descripción</th>
            <th width="80px" scope="col">Cant</th>
            <th width="120px" scope="col">Precio</th>
            <th width="75px" scope="col">Dto</th>
            <th width="90px" scope="col">IVA</th>
            <th scope="col">Subt</th>
            <th scope="col">Total</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $c3 = $mysqli->query("SELECT * FROM gastos_lineas where id_gasto=$id_gasto");
        while ($row = $c3->fetch_assoc()) {
            $id_gasto_linea = $row['id'];
            $id_presupuestos_partidas = $row['id_presupuestos_partidas'];
            $id_capitulo_presupuesto = $row['id_capitulo_presupuesto'] ?? null;
            $id_iva = $row['id_iva'];
            $concepto = $row['concepto'];
            $descripcion = $row['descripcion'];
            $cantidad = $row['cantidad'];
            $descuento = $row['descuento'];
            $precio = $row['precio'];
            $subtotal = $row['subtotal'];
            $total = $row['total'];

            include "../components/gastos/gastoDetailLine.php";
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


<div class="container">
    <div class="row">
        <!----------- PAGOS ----------->
        <div class="bg-white  rounded-3 p-4 col-8">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <p class="text-black fw-bold fs-5 ">Pagos</p>
                <div id="pagos2" class="d-flex align-items-center gap-4">
                    <p class="text-success"><span class="text-black">Pagado:</span> <?= $totalPagado; ?>€</p>
                    <p class="text-danger"><span
                                class="text-black">Pendiente:</span> <?= round(($totalGastoGeneral ?? 0) - ($totalPagado ?? 0), 2); ?>
                        €</p>
                </div>
            </div>

            <!----------- TABLE ----------->
            <table id="pagos" class="table tableComon">
                <thead>
                <tr>
                    <th width="100px" scope="col">Fecha</th>
                    <th scope="col">Comentario</th>
                    <th scope="col">Forma pago</th>
                    <th width="130px" scope="col">Estado</th>
                    <th scope="col">Importe</th>
                    <th width="40px" scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $c2 = $mysqli->query("SELECT * FROM gastos_pagos where id_gasto=$id_gasto order by fecha");
                while ($row = $c2->fetch_assoc()) {
                    $id_pago = $row['id'];
                    $importe = $row['importe'];
                    $fecha = $row['fecha'];
                    $comentario = $row['comentario'];
                    $forma_pago = $row['forma_pago'];
                    $estado = $row['estado'];

                    include "../components/gastos/gastoPagoLine.php";
                }
                if ($c2->num_rows == 0) {
                    include "../components/noDataLine.php";
                }
                ?>
                </tbody>
            </table>
            <!----------- END TABLE ----------->
        </div>
        <!----------- END PAGOS ----------->


        <!----------- SUBIDA ARCHIVOS ----------->
        <div id="docs" class="col-4">
            <div class="bg-white rounded-3 p-4 d-flex flex-column gap-3 h-100">
                <p class="text-black fw-bold fs-5 mb-3">Archivos</p>
                <?php
                $docs = $mysqli->query("SELECT * FROM gastos_archivos where id_gasto=$id_gasto");
                while ($row = $docs->fetch_assoc()) {
                    $id_archivo = $row['id'];
                    $titulo_archivo = $row['titulo'];
                    $src = $row['src'];

                    include "../components/gastos/gastoDocLine.php";
                }
                if ($docs->num_rows == 0) {
                    echo "<p class='mb-3'>NO HAY DOCUMENTOS SUBIDOS</p>";
                }
                ?>
            </div>
        </div>
        <!----------- END SUBIDA ARCHIVOS ----------->
    </div>
</div>


<!----------- RESUMEN ----------->
<div id="result" class="container-md my-3">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 ms-auto" style="width: fit-content">
        <div>
            <p class="fs-3 text-black fw-bold" id="resumenSubtotal"><?= formatCurrency($subtotalGastoGeneral); ?></p>
            <p class="letraPeq" style="color: #AEAEAE">Subtotal</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold" id="resumenDto"><?= formatCurrency($dto); ?></p>
            <p class="letraPeq" style="color: #AEAEAE">Dto</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold" id="resumenIva"><?= formatCurrency($totalGastoGeneral - $subtotalGastoGeneral); ?></p>
            <p class="letraPeq" style="color: #AEAEAE">IVA</p>
        </div>
        <div>
            <?php $retencionResult = round(($subtotalGastoGeneral ?? 0) * ($retencion ?? 0) / 100, 2); ?>
            <p class="fs-3 text-black fw-bold" id="resumenRetencion" data-retencion="<?= $retencion; ?>"><?= formatCurrency($retencionResult); ?></p>
            <p class="letraPeq" style="color: #AEAEAE">Retención</p>
        </div>
        <div>
            <p class="fs-3 text-black fw-bold" id="resumenTotal"><?= formatCurrency($totalGastoGeneral - $retencionResult); ?></p>
            <p class="letraPeq" style="color: #AEAEAE">Total</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->


<div class="container text-end mt-3">
    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#gastoDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar
    </button>
</div>

<?php include "../components/modals/gastoDelete.php"; ?>
<?php include "../components/modals/gastoAddPago.php"; ?>
<?php include "../components/updateConfirmation.php"; ?>

</body>
<script src="../js/selectizeFunction.js"></script>
<script src="../js/datePicker.js"></script>
<script src="../js/updateConfirmation.js"></script>
<script src="../js/formatNumbers.js"></script>

<script>

    $(document).ready(function () {
        $('#duplicateExpenseButton').click(function () {
            console.log('click');
            var originalExpenseId = $(this).data('expense-id');
            console.log(originalExpenseId);  // Verificar el valor de originalExpenseId
            $.post('../backend/gastos/gastoDuplicar.php', {originalExpenseId: originalExpenseId}, function (response) {
                console.log(response);
                if (response.status === 'success') {
                    alert('Gasto duplicado exitosamente!');
                    // Redireccionar a la página de detalle del nuevo gasto
                    window.location.href = 'gastoDetail.php?id_gasto=' + response.newExpenseId;
                } else {
                    alert('Error duplicando el gasto: ' + response.message);
                }
            }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
                console.error('Error: ' + textStatus);  // Verificar cualquier error
                console.error('Error Thrown: ' + errorThrown);  // Verificar cualquier error lanzado
                console.error('Response: ' + jqXHR.responseText);  // Verificar la respuesta del servidor
            });
        });
    });


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
            } else {
                showMessage();
            }
            if (columna == "estado") {
                $("#estado" + fila).load(location.href + " #estado" + fila);
                $("#pagos2").load(location.href + " #pagos2");
            }
        });
    }

    // Calcular importe subtotal y total de la linea de gasto editada
    const calculateTotal = (id_gasto_linea) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/calculateTotal.php",
            dataType: 'json',
            data: {
                id_gasto_linea: id_gasto_linea
            }
        }).done(function (data) {
            // Actualizar línea específica con formato español
            $("#subtotal" + id_gasto_linea).text(formatNumberES(data.subtotalGasto) + "€");
            $("#total" + id_gasto_linea).text(formatNumberES(data.totalGasto) + "€");

            // Actualizar resumen general
            const retencion = parseFloat($("#resumenRetencion").data("retencion")) || 0;
            const subtotalGastoGeneral = parseFloat(data.subtotalGastoGeneral) || 0;
            const totalGastoGeneral = parseFloat(data.totalGastoGeneral) || 0;
            const dto = parseFloat(data.dto) || 0;
            const iva = totalGastoGeneral - subtotalGastoGeneral;
            const retencionResult = Math.round((subtotalGastoGeneral * retencion / 100) * 100) / 100;
            const totalFinal = totalGastoGeneral - retencionResult;

            $("#resumenSubtotal").text(formatNumberES(subtotalGastoGeneral) + "€");
            $("#resumenDto").text(formatNumberES(dto) + "€");
            $("#resumenIva").text(formatNumberES(iva) + "€");
            $("#resumenRetencion").text(formatNumberES(retencionResult) + "€");
            $("#resumenTotal").text(formatNumberES(totalFinal) + "€");

            showMessage();
        });
    }

    // Añadir linea de gasto
    const gastoAddLine = (id_gasto) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoAddLine.php",
            data: {
                id_gasto: id_gasto
            }
        }).done(function () {
            $("#gastoLines").load(location.href + " #gastoLines");
        });
    }

    // Eliminar linea de gasto
    const gastoDeleteLine = (id_gasto_linea) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoDeleteLine.php",
            data: {
                id_gasto_linea: id_gasto_linea
            }
        }).done(function () {
            $("#gastoLines").load(location.href + " #gastoLines");
            $("#result").load(location.href + " #result");
        });
    }

    // Eliminar un pago registrado
    const gastoDeletePago = (id_pago) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoDeletePago.php",
            data: {
                id_pago: id_pago
            }
        }).done(function () {
            $("#pagos").load(location.href + " #pagos");
            $("#pagos2").load(location.href + " #pagos2");
        });
    }

    // Asignar una partida a una linea de gasto
    const gastoAssignPartida = (id_presupuestos_partidas, id_gasto_linea) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoAssignPartida.php",
            data: {
                id_presupuestos_partidas: id_presupuestos_partidas,
                id_gasto_linea: id_gasto_linea
            }
        }).done(function () {
            showMessage();
        });
    }

    // ELiminar un documento subido
    const gastoDeleteDoc = (id_documento) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoDeleteDoc.php",
            data: {
                id_documento: id_documento
            }
        }).done(function () {
            $("#docs").load(location.href + " #docs");
        });
    }

    // Eliminar gasto completo
    const gastoDelete = (id_gasto) => {
        $.ajax({
            method: "POST",
            url: "../backend/gastos/gastoDelete.php",
            data: {
                id_gasto: id_gasto
            }
        }).done(function () {
            location.href = "gastos.php";
        });
    }

    // submit form automaticamente cuando se seleccionan los archivos
    document.getElementById("uploadFile").onchange = function () {
        document.getElementById("gastoDocs").submit();
    };


</script>
</html>