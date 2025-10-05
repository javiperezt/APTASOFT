<?php
include "../../conexion.php";

function duplicateExpense($originalExpenseId, $mysqli)
{
    $mysqli->begin_transaction();

    try {
        // Duplicar entrada en la tabla gastos
        $query = "INSERT INTO gastos (
    id_contacto,
    retencion,
    id_categoria_gasto,
    id_cuenta,
    id_obra,
    id_estado,
    id_empresa,
    codigo,
    fecha_inicio,
    fecha_vencimiento,
    comentario,
    creation_date,
    is_active
) 
SELECT 
    id_contacto,
    retencion,
    id_categoria_gasto,
    id_cuenta,
    id_obra,
    id_estado,
    id_empresa,
    codigo,
    fecha_inicio,
    fecha_vencimiento,
    comentario,
    NOW(),
    is_active
FROM 
    gastos 
WHERE 
    id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalExpenseId);
        $stmt->execute();
        $newExpenseId = $mysqli->insert_id;

        // Duplicar líneas de gasto
        // Duplicar líneas de gasto
        $query = "SELECT id FROM gastos_lineas WHERE id_gasto = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalExpenseId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $originalLineaId = $row['id'];

            $query = "INSERT INTO gastos_lineas (
        id_gasto, 
        id_presupuestos_partidas, 
        id_iva, 
        concepto, 
        descripcion, 
        cantidad, 
        descuento, 
        precio, 
        subtotal, 
        total
    ) 
    SELECT 
        ?, 
        id_presupuestos_partidas, 
        id_iva, 
        concepto, 
        descripcion, 
        cantidad, 
        descuento, 
        precio, 
        subtotal, 
        total
    FROM 
        gastos_lineas
    WHERE 
        id = ?";

            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ii', $newExpenseId, $originalLineaId);
            $stmt->execute();
        }

        $result->close();


        // Duplicar pagos registrados
        $query = "SELECT * FROM gastos_pagos WHERE id_gasto = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalExpenseId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $query = "INSERT INTO gastos_pagos (
        id_gasto,
        importe,
        fecha,
        comentario,
        forma_pago,
        estado
    ) VALUES (
        ?, ?, ?, ?, ?, ?
    )";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param(
                'idsiss',
                $newExpenseId,
                $row['importe'],
                $row['fecha'],
                $row['comentario'],
                $row['forma_pago'],
                $row['estado']
            );
            $stmt->execute();
        }

        $result->close();


        // Duplicar archivos adjuntos y sus archivos en el servidor
        $query = "SELECT * FROM gastos_archivos WHERE id_gasto = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $originalExpenseId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $originalFileId = $row['id'];
            $originalFilePath = "../../docs/gastos/" . $row['src'];  // Añadido "docs/gastos/" antes de la ruta

            // Descomponer la ruta del archivo original en sus componentes
            $pathInfo = pathinfo($originalFilePath);
            $newFileName = $pathInfo['filename'] . "_copy$newExpenseId." . $pathInfo['extension'];
            $newFilePath = $pathInfo['dirname'] . '/' . $newFileName;

            // Copia el archivo original al nuevo destino
            if (!copy($originalFilePath, $newFilePath)) {
                throw new Exception("Failed to copy file from $originalFilePath to $newFilePath");
            }

            // Registra el archivo duplicado en la base de datos
            $dbNewFilePath = str_replace("../../docs/gastos/", "", $newFilePath);
            $query = "INSERT INTO gastos_archivos (id_gasto, titulo, src, id_empleado, creation_date) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('issi', $newExpenseId, $newFileName, $dbNewFilePath, $row['id_empleado']);
            if ($stmt->execute()) {
                error_log("Registro insertado correctamente en gastos_archivos");  // Log de éxito
            } else {
                error_log("Error insertando registro en gastos_archivos: " . $stmt->error);  // Log de error
            }
        }


        $mysqli->commit();
        return $newExpenseId;  // Devuelve el ID del nuevo gasto
    } catch (Exception $e) {
        $mysqli->rollback();
        throw $e;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['originalExpenseId'])) {
    try {
        $newExpenseId = duplicateExpense($_POST['originalExpenseId'], $mysqli);  // Guarda el ID del nuevo gasto
        $response = array(
            'status' => 'success',
            'newExpenseId' => $newExpenseId
        );
        echo json_encode($response);
    } catch (Exception $e) {
        // Maneja cualquier excepción que ocurra dentro de duplicateExpense
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
