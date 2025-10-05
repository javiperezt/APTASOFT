<?php
/**
 * Script de importación BC3 usando la clase ImportadorBC3 corregida
 * 
 * Este script utiliza la clase ImportadorBC3 que implementa el parseador
 * robusto según el estándar FIEBDC-3/2024 con manejo correcto de jerarquías.
 * 
 * @author STC21
 * @version 2.0
 */

// Configurar tiempo de ejecución y memoria para archivos BC3 grandes
set_time_limit(300); // 5 minutos
ini_set('memory_limit', '512M');

// Activar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Asegurar que siempre se devuelva JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Validar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Validar parámetros
    $id_presupuesto = (int)($_POST['id_presupuesto'] ?? 0);
    if (!$id_presupuesto) {
        throw new Exception('ID de presupuesto inválido');
    }

    // Validar archivo subido
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
        throw new Exception('Error al subir el archivo');
    }

    // Validar extensión del archivo
    $fileName = $_FILES["file"]["name"];
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if ($extension !== 'bc3') {
        throw new Exception('El archivo debe tener extensión .bc3');
    }

    // Incluir dependencias
    require_once "../../conexion.php";
    require_once "ImportadorBC3.php";

    // Verificar conexión a la base de datos
    if (!isset($mysqli) || !$mysqli || $mysqli->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . ($mysqli->connect_error ?? 'Conexión no disponible'));
    }

    // Log de inicio
    error_log("BC3: Iniciando importación - Archivo: $fileName, Presupuesto: $id_presupuesto");

    // Crear instancia del importador
    $importador = new ImportadorBC3($mysqli, $id_presupuesto);

    // Importar archivo BC3
    $resultado = $importador->importarArchivo($_FILES["file"]["tmp_name"], $fileName);

    // Log del resultado
    if ($resultado['success']) {
        error_log("BC3: Importación exitosa - " . json_encode($resultado['stats']));
    } else {
        error_log("BC3: Error en importación - " . $resultado['message']);
    }

    // Devolver resultado
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    // Log del error
    error_log("BC3: Excepción capturada - " . $e->getMessage());
    error_log("BC3: Stack trace - " . $e->getTraceAsString());

    // Devolver error en formato JSON
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'errors' => [$e->getMessage()]
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Error $e) {
    // Capturar errores fatales
    error_log("BC3: Error fatal - " . $e->getMessage());
    
    $response = [
        'success' => false,
        'message' => 'Error interno del servidor',
        'errors' => ['Error interno del servidor']
    ];

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// Finalizar script
error_log("BC3: Script finalizado");
?>