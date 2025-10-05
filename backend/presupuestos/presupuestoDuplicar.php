<?php
include "../../conexion.php";


function duplicateBudget($originalBudgetId, $mysqli)
{
    // Comienza una transacción
    $mysqli->begin_transaction();

    try {
        // 1. Duplica la entrada en la tabla presupuestos
        $query = "INSERT INTO presupuestos (id_contacto, id_cuenta, id_obra, id_estado, id_empresa, pref_ref, pref_ref_year, ref, asunto, nota, fecha_inicio, fecha_vencimiento, creation_date, is_active) SELECT id_contacto, id_cuenta, id_obra, id_estado, id_empresa, pref_ref, pref_ref_year, ref, asunto, nota, fecha_inicio, fecha_vencimiento, NOW(), is_active FROM presupuestos WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalBudgetId);
        $stmt->execute();

        $newBudgetId = $mysqli->insert_id;

        // 2. Duplica los capítulos asociados
        $query = "INSERT INTO presupuestos_capitulos (id_presupuesto, id_capitulo) SELECT ?, id_capitulo FROM presupuestos_capitulos WHERE id_presupuesto = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ii', $newBudgetId, $originalBudgetId);
        $stmt->execute();

        // 3. Duplica las partidas asociadas
        $query = "SELECT id FROM presupuestos_partidas WHERE id_presupuesto = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalBudgetId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $originalPartidaId = $row['id'];

            $query = "INSERT INTO presupuestos_partidas (id_presupuesto,id_capitulo,id_partida,partida,descripcion,cantidad,subtotal,total,id_estado,fecha_inicio,fecha_vencimiento,id_unidad) SELECT ?, id_capitulo, id_partida, partida, descripcion, cantidad, subtotal, total, id_estado, fecha_inicio, fecha_vencimiento,id_unidad FROM presupuestos_partidas WHERE id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ii', $newBudgetId, $originalPartidaId);
            $stmt->execute();
            $newPartidaId = $mysqli->insert_id;

            // 4. Duplica las subpartidas asociadas
            $query = "INSERT INTO presupuestos_subpartidas (id_presupuesto_partidas,id_partida,id_subpartida,id_categoria,concepto,descripcion,id_unidad,cantidad,precio,descuento,id_iva,subtotal,total,fecha_vencimiento,fecha_prox_intervencion,id_contacto,is_checked) SELECT ?,id_partida,id_subpartida,id_categoria,concepto,descripcion,id_unidad,cantidad,precio,descuento,id_iva,subtotal,total,fecha_vencimiento,fecha_prox_intervencion,id_contacto,is_checked FROM presupuestos_subpartidas WHERE id_presupuesto_partidas = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ii', $newPartidaId, $originalPartidaId);
            $stmt->execute();
        }
        $mysqli->commit();
        return $newBudgetId;
    } catch (Exception $e) {
        // Si algo sale mal, rollback la transacción
        $mysqli->rollback();
        throw $e;  // O maneja el error como prefieras
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['originalBudgetId'])) {
    try {
        $newBudgetId = duplicateBudget($_POST['originalBudgetId'], $mysqli);
        echo json_encode(['status' => 'success', 'newBudgetId' => $newBudgetId]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
