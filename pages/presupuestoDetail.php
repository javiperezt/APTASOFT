<?php
session_start();

include "../conexion.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}

$id_presupuesto = filter_input(INPUT_GET, 'id_presupuesto', FILTER_SANITIZE_SPECIAL_CHARS);

$c0 = $mysqli->query("SELECT * FROM presupuestos where id=$id_presupuesto");
while ($row = $c0->fetch_assoc()) {
    $id_contacto = $row['id_contacto'];
    $id_cuenta = $row['id_cuenta'];
    $id_obra = $row['id_obra'];
    $id_estado = $row['id_estado'];
    $id_empresa = $row['id_empresa'];
    $pref_ref = $row['pref_ref'];
    $pref_ref_year = $row['pref_ref_year'];
    $ref = $row['ref'];
    $ref = $pref_ref . $pref_ref_year . $ref;
    $asunto = $row['asunto'];
    $fecha_inicio = $row['fecha_inicio'];
    $nota = $row['nota'];
    $fecha_vencimiento = $row['fecha_vencimiento'];
}

$c1 = $mysqli->query("SELECT * FROM contactos where id=$id_contacto");
while ($row = $c1->fetch_assoc()) {
    $nombre_contacto = $row['nombre'];
}

$c2 = $mysqli->query("SELECT * FROM obras where id=$id_obra");
while ($row = $c2->fetch_assoc()) {
    $titulo_obra = $row['titulo'];
}

if ($id_estado) {
    $c3 = $mysqli->query("SELECT * FROM presupuestos_estados where id=$id_estado");
    while ($row = $c3->fetch_assoc()) {
        $estado = $row['estado'];
    }
}

$getSubtotalPresupuesto = mysqli_query($mysqli, "SELECT SUM(subtotal) AS subtotalPresupuesto FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
$result = mysqli_fetch_assoc($getSubtotalPresupuesto);
$subtotalPresupuesto = round($result['subtotalPresupuesto'], 2);

$getTotalPresupuesto = mysqli_query($mysqli, "SELECT SUM(total) AS totalPresupuesto FROM presupuestos_partidas where id_presupuesto='$id_presupuesto'");
$result = mysqli_fetch_assoc($getTotalPresupuesto);
$totalPresupuesto = round($result['totalPresupuesto'], 2);

$getIva21 = mysqli_query($mysqli, "SELECT SUM((ps.total-ps.subtotal)) AS iva21 FROM presupuestos_partidas pp INNER JOIN presupuestos_subpartidas ps on pp.id = ps.id_presupuesto_partidas where pp.id_presupuesto=$id_presupuesto and ps.id_iva=1");
$result = mysqli_fetch_assoc($getIva21);
$iva21 = round($result['iva21'], 2);

$getIva10 = mysqli_query($mysqli, "SELECT SUM((ps.total-ps.subtotal)) AS iva10 FROM presupuestos_partidas pp INNER JOIN presupuestos_subpartidas ps on pp.id = ps.id_presupuesto_partidas where pp.id_presupuesto=$id_presupuesto and ps.id_iva=2");
$result = mysqli_fetch_assoc($getIva10);
$iva10 = round($result['iva10'], 2);

$getIva4 = mysqli_query($mysqli, "SELECT SUM((ps.total-ps.subtotal)) AS iva4 FROM presupuestos_partidas pp INNER JOIN presupuestos_subpartidas ps on pp.id = ps.id_presupuesto_partidas where pp.id_presupuesto=$id_presupuesto and ps.id_iva=3");
$result = mysqli_fetch_assoc($getIva4);
$iva4 = round($result['iva4'], 2);

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
        <a href="presupuestos.php"><i class="bi bi-arrow-left fs-5 text-black"></i></a>
        <p class="text-black fw-bold fs-4 m-0">Presupuesto <?= $ref; ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-secondary btn-sm me-5" id="duplicateButton" data-id="<?=$id_presupuesto;?>">Duplicar Presupuesto</button>
        <form class="m-0" id="uploadCsv" action="../backend/presupuestos/presupuestoImportarCsv.php" method="post"
              enctype="multipart/form-data">
            <label class="btn btn-outline-secondary btn-sm" for="uploadFile">Importar CSV<i
                        class="bi bi-upload ms-2"></i></label>
            <input type="file" class="d-none" id="uploadFile" name="file" accept=".csv">
            <input type="hidden" value="<?= $id_presupuesto; ?>" name="id_presupuesto">
        </form>
        <form class="m-0" id="uploadBc3" action="../backend/proceso_bc3/importar_bc3.php" method="post"
              enctype="multipart/form-data">
            <label class="btn btn-outline-secondary btn-sm" for="uploadFileBc3">Importar BC3<i
                        class="bi bi-upload ms-2"></i></label>
            <input type="file" class="d-none" id="uploadFileBc3" name="file" accept=".bc3">
            <input type="hidden" value="<?= $id_presupuesto; ?>" name="id_presupuesto">
        </form>
        <a href="presupuestoPdf.php?id_presupuesto=<?= $id_presupuesto; ?>" target="_blank"
           class="btn btn-outline-secondary btn-sm">Descargar PDF<i
                    class="bi bi-file-earmark-text-fill ms-2"></i></a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#presupuestoAddCapitulo">+
            Capítulo
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#presupuestoAddPartida">+
            Partida
        </button>
    </div>
</div>
<!----------- END HEAD PAGE ----------->


<!----------- INFO ----------->
<div class="bg-white container-md my-3 rounded-3 p-4">
    <p class="text-black fw-bold fs-5 mb-3">Información general</p>
    <div class="row g-2">
        <div class="col-4">
            <select class="selectizeSearch selectizeSearchBig" id="id_contacto"
                    onchange="updateGenerico('presupuestos','id_contacto',<?= $id_presupuesto; ?>,this.value)">
                <option selected value="">Todos los clientes</option>
                <?php
                $s_contactos = $mysqli->query("SELECT * FROM contactos");
                while ($row = $s_contactos->fetch_assoc()) {
                    $s_id_contacto = $row['id'];
                    $s_nombre_contacto = $row['nombre'];

                    $selected = "";
                    if ($id_contacto == $s_id_contacto) {
                        $selected = "selected";
                    }
                    echo "<option $selected value='$s_id_contacto'>$s_nombre_contacto</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-4">
            <select onchange="updateGenerico('presupuestos','id_obra',<?= $id_presupuesto; ?>,this.value)"
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
                <input onchange="updateGenerico('presupuestos','fecha_inicio',<?= $id_presupuesto; ?>,this.value)"
                       required type="date" class="form-control" value="<?= $fecha_inicio; ?>">
                <label>Fecha</label>
            </div>
        </div>
        <div class="col-2">
            <div class="form-floating">
                <input onchange="updateGenerico('presupuestos','fecha_vencimiento',<?= $id_presupuesto; ?>,this.value)"
                       required type="date" class="form-control" value="<?= $fecha_vencimiento; ?>">
                <label>Vencimiento</label>
            </div>
        </div>
        <div class="col-6">
            <select onchange="updateGenerico('presupuestos','id_cuenta',<?= $id_presupuesto; ?>,this.value)"
                    class="selectizeSearch selectizeSearchBig">
                <option selected disabled hidden>Asignar cuenta</option>
                <?php
                $getExpensesTypes = $mysqli->query("SELECT * FROM cuentas_ingreso where is_active=1");
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
                <select onchange="updateGenerico('presupuestos','id_estado',<?= $id_presupuesto; ?>,this.value)"
                        class="form-select" id="floatingSelect">
                    <option selected disabled hidden>Estado</option>
                    <?php
                    $c4 = $mysqli->query("SELECT * FROM presupuestos_estados");
                    while ($row = $c4->fetch_assoc()) {
                        $id_presupuestos_estados = $row['id'];
                        $estado = $row['estado'];

                        $selected = "";
                        if ($id_presupuestos_estados == $id_estado) {
                            $selected = "selected";
                        }
                        echo "<option $selected value='$id_presupuestos_estados'>$estado</option>";
                    }
                    ?>
                </select>
                <label>Estado</label>
            </div>
        </div>
        <div class="col-3">
            <div class="form-floating">
                <select onchange="updateGenerico('presupuestos','id_empresa',<?= $id_presupuesto; ?>,this.value)"
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
                <input onfocusout="aplicarBeneficio(<?= $id_presupuesto; ?>, this.value)"
                       type="number" class="form-control" placeholder="Beneficio" step="any" min="0"
                       value="<?= $beneficio; ?>">
                <label>Beneficio (%)</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input onfocusout="updateGenerico('presupuestos','asunto',<?= $id_presupuesto; ?>,this.value)"
                       type="text" class="form-control" placeholder="Asunto" value="<?= $asunto; ?>">
                <label>Asunto</label>
            </div>
        </div>
        <div class="col-12">
            <div class="form-floating">
                <input onfocusout="updateGenerico('presupuestos','nota',<?= $id_presupuesto; ?>,this.value)"
                       type="text" class="form-control" placeholder="Nota" value="<?= $nota; ?>">
                <label>Nota</label>
            </div>
        </div>
    </div>
</div>
<!----------- END INFO ----------->


<!----------- CAPÍTULOS ----------->
<?php
$c5 = $mysqli->query("SELECT * FROM presupuestos_capitulos where id_presupuesto=$id_presupuesto");
while ($row = $c5->fetch_assoc()) {
    $id_capitulo = $row['id_capitulo'];

    $c6 = $mysqli->query("SELECT * FROM capitulos where id=$id_capitulo");
    while ($row = $c6->fetch_assoc()) {
        $capitulo = $row['capitulo'];
    }

    include "../components/modals/presupuestoDeleteCapitulo.php";
    ?>
    <div class="bg-white container-md mt-3 rounded-3 p-4">
        <div class="d-flex align-items-center justify-content-between">
            <p class="text-black fw-bold fs-5 "><?= $capitulo; ?></p>
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                    data-bs-target="#presupuestoDeleteCapitulo<?= $id_capitulo; ?>"><i
                        class="bi bi-trash-fill"></i></button>
        </div>

        <!----------- TABLE ----------->
        <div class="table-responsive mt-3">
            <table class="table tableComon">
                <thead>
                <tr>
                    <th width="20%" scope="col">Partida</th>
                    <th scope="col">Descripción</th>
                    <th width="50px" scope="col">Ud</th>
                    <th width="100px" scope="col">Cantidad</th>
                    <th width="90px" scope="col">Precio Ud.</th>
                    <th width="90px" scope="col">Subtotal</th>
                    <!--<th width="90px" scope="col">Total</th>-->
                    <th width="40px" scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $c7 = $mysqli->query("SELECT * FROM presupuestos_partidas where id_presupuesto=$id_presupuesto and id_capitulo=$id_capitulo");
                while ($row = $c7->fetch_assoc()) {
                    $id_presupuestos_partidas = $row['id'];
                    $id_partida = $row['id_partida'];
                    $id_unidad = $row['id_unidad'];
                    $partida = $row['partida'];
                    $descripcion = $row['descripcion'];
                    $cantidad = $row['cantidad'];
                    $subtotal = $row['subtotal'];
                    $total = $row['total'];
                    $subtotal_x_cantidad = round($cantidad * $subtotal, 2);
                    $total_x_cantidad = round($cantidad * $total, 2);

                    $unidades = $mysqli->query("SELECT * FROM unidades where id=$id_unidad");
                    while ($row = $unidades->fetch_assoc()) {
                        $unidad = $row['simbolo'];
                    }

                    include "../components/presupuestos/partidaLine.php";
                }
                if ($c7->num_rows == 0) {
                    include "../components/noDataLine.php";
                }
                ?>
                </tbody>
            </table>
        </div>
        <!----------- END TABLE ----------->
    </div>
    <?php
}
?>
<!----------- END CAPÍTULOS ----------->


<!----------- RESUMEN ----------->
<div id="result" class="container mt-3">
    <div class="d-flex gap-4 bg-white p-4 rounded-3 ms-auto" style="width: fit-content">
        <div>
            <p id="subtotal" class="fs-3 text-black fw-bold"><?= number_format(round($subtotalPresupuesto, 2), 2, ',', '.'); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Subtotal</p>
        </div>
        <div>
            <p id="iva" class="fs-3 text-black fw-bold"><?= number_format(round($iva21, 2), 2, ',', '.'); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">IVA 21%</p>
        </div>
        <?php if ($iva10 > 0) { ?>
            <div>
                <p id="iva" class="fs-3 text-black fw-bold"><?= number_format(round($iva10, 2), 2, ',', '.'); ?>€</p>
                <p class="letraPeq" style="color: #AEAEAE">IVA 10%</p>
            </div>
        <?php } ?>
        <?php if ($iva4 > 0) { ?>
            <div>
                <p id="iva" class="fs-3 text-black fw-bold"><?= number_format(round($iva4, 2), 2, ',', '.'); ?>€</p>
                <p class="letraPeq" style="color: #AEAEAE">IVA 4%</p>
            </div>
        <?php } ?>
        <div>
            <p id="total" class="fs-3 text-black fw-bold"><?= number_format(round($totalPresupuesto, 2), 2, ',', '.'); ?>€</p>
            <p class="letraPeq" style="color: #AEAEAE">Total</p>
        </div>
    </div>
</div>
<!----------- END RESUMEN ----------->


<div class="container text-end mt-3">
    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#presupuestoDelete"><i
                class="bi bi-trash-fill me-2"></i>Eliminar presupuesto
    </button>
</div>


</body>

<!-- Modal de Procesamiento BC3 -->
<div id="processingModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.8); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); max-width: 400px; width: 90%;">
        <!-- Spinner animado -->
        <div style="margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; margin: 0 auto; border: 6px solid rgba(255, 255, 255, 0.3); border-top: 6px solid #ffffff; border-radius: 50%; animation: spin 1s linear infinite;"></div>
        </div>
        
        <!-- Título -->
        <h3 style="color: white; margin-bottom: 15px; font-weight: bold; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
            🔄 Procesando archivo BC3
        </h3>
        
        <!-- Descripción -->
        <p style="color: rgba(255, 255, 255, 0.9); margin-bottom: 20px; font-size: 16px; line-height: 1.5;">
            Analizando e importando datos del archivo BC3...<br>
            <small style="opacity: 0.8;">Este proceso puede tomar unos momentos</small>
        </p>
        
        <!-- Barra de progreso animada -->
        <div style="width: 100%; height: 4px; background-color: rgba(255, 255, 255, 0.3); border-radius: 2px; overflow: hidden;">
            <div style="width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.6), transparent); animation: slide 2s infinite;"></div>
        </div>
    </div>
</div>

<style>
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    @keyframes slide {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Efecto de pulso suave para el modal */
    #processingModal > div {
        animation: modalPulse 3s ease-in-out infinite;
    }
    
    @keyframes modalPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
    }
</style>

<?php include "../components/modals/presupuestoAddCapitulo.php"; ?>
<?php include "../components/modals/presupuestoAddPartida.php"; ?>
<?php include "../components/modals/presupuestoDelete.php"; ?>
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
            if (columna == "total_x_cantidad") {
                calculateTotalxCantidad(fila, valor);
            } else {
                showMessage();
            }
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
            subtotal_x_cantidad = data['subtotal_x_cantidad'];
            subtotal_x_cantidad = parseFloat(subtotal_x_cantidad).toFixed(2);
            total_x_cantidad = data['total_x_cantidad'];
            total_x_cantidad = parseFloat(total_x_cantidad).toFixed(2);

            $("#subtotal_x_cantidad" + id_presupuestos_partidas).text(subtotal_x_cantidad + "€");
            $("#total_x_cantidad" + id_presupuestos_partidas).text(total_x_cantidad + "€");
            $("#result").load(location.href + " #result");
        });
    }


    const presupuestoDeleteCapitulo = (id_capitulo, id_presupuesto) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/presupuestoDeleteCapitulo.php",
            data: {
                id_capitulo: id_capitulo,
                id_presupuesto: id_presupuesto
            }
        }).done(function () {
            location.reload();
        });
    }

    const aplicarBeneficio = (id_presupuesto, beneficio) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/aplicarBeneficio.php",
            data: {
                beneficio: beneficio,
                id_presupuesto: id_presupuesto
            }
        }).done(function () {
            location.reload();
        });
    }


    const presupuestoDelete = (id_presupuesto) => {
        $.ajax({
            method: "POST",
            url: "../backend/presupuestos/presupuestoDelete.php",
            data: {
                id_presupuesto: id_presupuesto
            }
        }).done(function () {
            location.href = "presupuestos.php";
        });
    }

    // submit form automaticamente cuando se seleccionan los archivos
    document.getElementById("uploadFile").onchange = function () {
        document.getElementById("uploadCsv").submit();
    };


    // submit form automaticamente cuando se seleccionan los archivos BC3 con AJAX
    document.getElementById("uploadFileBc3").onchange = function () {
        const formData = new FormData();
        const file = this.files[0];
        
        if (!file) return;
        
        // Validar que sea un archivo BC3
        if (!file.name.toLowerCase().endsWith('.bc3')) {
            alert('Por favor selecciona un archivo con extensión .bc3');
            return;
        }
        
        // Mostrar modal de procesamiento
        showProcessingModal();
        
        formData.append('file', file);
        formData.append('id_presupuesto', '<?= $id_presupuesto; ?>');
        
        fetch('../backend/proceso_bc3/importar_bc3.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.text(); // Cambiar a text() para ver qué recibimos
        })
        .then(text => {
            console.log('Raw response:', text);
            
            // Ocultar modal de procesamiento
            hideProcessingModal();
            
            // Intentar parsear JSON
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                alert('❌ Error: El servidor no devolvió JSON válido.\n\nRespuesta recibida:\n' + text.substring(0, 500));
                return;
            }
            
            if (data.success) {
                // Recargar la página automáticamente sin mostrar mensaje
                location.reload();
            } else {
                let mensaje = '❌ Error importando archivo BC3:\n\n' + data.message;
                
                // Mostrar debug/log info
                if (data.debug && data.debug.length > 0) {
                    mensaje += '\n\n📋 Debug info:\n' + data.debug.join('\n');
                } else if (data.log && data.log.length > 0) {
                    mensaje += '\n\n📋 Log info:\n' + data.log.join('\n');
                }
                
                alert(mensaje);
            }
        })
        .catch(error => {
            // Ocultar modal de procesamiento en caso de error
            hideProcessingModal();
            
            console.error('Error:', error);
            alert('❌ Error inesperado al importar el archivo. Revisa la consola para más detalles.');
        });
        
        // Limpiar el input para permitir subir el mismo archivo de nuevo
        this.value = '';
    };

    // Funciones para mostrar/ocultar modal de procesamiento
    function showProcessingModal() {
        document.getElementById('processingModal').style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Bloquear scroll
    }

    function hideProcessingModal() {
        document.getElementById('processingModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Restaurar scroll
    }

    // jQuery
    $(document).ready(function(){
        $('#duplicateButton').on('click', function() {
            var originalBudgetId = $(this).data('id');
            $.post('../backend/presupuestos/presupuestoDuplicar.php', { originalBudgetId: originalBudgetId }, function(response) {
                if (response.status === 'success') {
                    alert('Presupuesto duplicado exitosamente!');
                    // Redirige al nuevo presupuesto
                    window.location.href = 'presupuestoDetail.php?id_presupuesto=' + response.newBudgetId;
                } else {
                    alert('Error duplicando el presupuesto: ' + response.message);
                }
            }, 'json');
        });
    });


</script>
</html>