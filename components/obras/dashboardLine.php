<tr data-bs-toggle="collapse" href="#collapse">
    <td><?= $capitulo; ?></td>
    <td><?= $partida; ?></td>

    <!-- MANO DE OBRA -->
    <td class="text-danger fw-light"><?= $costeManoObra; ?>€</td>
    <!-- HORAS REGISTRADAS POR TRABAJADORES * PRECIO ESTIMADO (20€/h) (PLANNING) -->
    <td class="text-success fw-light"><?= $ingresosManoObra; ?>€</td> <!-- INGRESOS -->
    <td class="text-primary fw-light"><?= $totalManoObra; ?>€</td> <!-- TOTAL -->

    <!-- MATERIAL -->
    <td class="text-danger fw-light"><?= $costeMaterial; ?>€</td> <!-- FACTURA DE GASTOS -->
    <td class="text-success fw-light"><?= $ingresosMaterial; ?>€</td> <!-- PARTIDAS PRESUPUESTOS MATERIAL -->
    <td class="text-primary fw-light"><?= $totalMaterial; ?>€</td> <!-- TOTAL -->

    <!-- OTROS PROVEEDORES -->
    <td class="text-danger fw-light"><?= $costeOtros; ?>€</td> <!-- FACTURAS DE GASTOS (OTROS) -->
    <td class="text-success fw-light"><?= $ingresosOtros; ?>€</td> <!-- PRESUPUESTO (OTROS PROV) -->
    <td class="text-primary fw-light"><?= $totalOtros; ?>€</td> <!-- TOTAL -->

    <!-- RESULTADO -->
    <td class="text-danger fw-bold"><?= $totalCostes; ?>€</td>
    <td class="text-success fw-bold"><?= $totalIngresos; ?>€</td>
    <td class="text-primary fw-bold"><?= $totalResultado; ?>€</td>
</tr>