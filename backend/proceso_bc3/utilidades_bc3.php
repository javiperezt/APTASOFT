<?php

class UtilidadesBC3 {

    /**
     * Validar formato de archivo BC3
     * 
     * @param string $rutaArchivo Ruta al archivo temporal
     * @param string $nombreOriginal Nombre original del archivo (opcional)
     * @return array Resultado de la validación
     */
    public static function validarFormatoBC3($rutaArchivo, $nombreOriginal = null) {
        if (!file_exists($rutaArchivo)) {
            return ['valido' => false, 'error' => 'Archivo no encontrado'];
        }

        // Usar el nombre original del archivo para verificar la extensión si está disponible
        $nombreParaExtension = $nombreOriginal ?? $rutaArchivo;
        $extension = pathinfo($nombreParaExtension, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'bc3') {
            return ['valido' => false, 'error' => 'El archivo debe tener extensión .bc3'];
        }

        // Leer primeras líneas para verificar formato
        $handle = fopen($rutaArchivo, 'r');
        if (!$handle) {
            return ['valido' => false, 'error' => 'No se puede leer el archivo'];
        }

        $primeraLinea = fgets($handle);
        fclose($handle);

        // Verificar que tiene formato BC3 (debería empezar con ~V)
        if (substr(trim($primeraLinea), 0, 3) !== '~V|') {
            return ['valido' => false, 'error' => 'El archivo no tiene formato BC3 válido'];
        }

        return ['valido' => true, 'error' => null];
    }

    /**
     * Limpiar y normalizar texto
     */
    public static function limpiarTexto($texto) {
        // Convertir a UTF-8 si es necesario
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');

        // Eliminar caracteres especiales no deseados
        $texto = preg_replace('/[^\p{L}\p{N}\s\-\.\,\(\)]/u', '', $texto);

        // Limpiar espacios múltiples
        $texto = preg_replace('/\s+/', ' ', $texto);

        return trim($texto);
    }

    /**
     * Convertir precio de formato BC3 a decimal
     */
    public static function convertirPrecio($precio_bc3) {
        // Eliminar caracteres no numéricos excepto punto y coma
        $precio = preg_replace('/[^\d\.,\-]/', '', $precio_bc3);

        // Manejar diferentes formatos de decimales
        if (strpos($precio, ',') !== false && strpos($precio, '.') !== false) {
            // Formato: 1.234,56
            $precio = str_replace('.', '', $precio);
            $precio = str_replace(',', '.', $precio);
        } elseif (substr_count($precio, ',') === 1) {
            // Formato: 1234,56
            $precio = str_replace(',', '.', $precio);
        }

        return (float) $precio;
    }

    /**
     * Mapear unidades BC3 a unidades del sistema
     */
    public static function mapearUnidad($unidad_bc3) {
        $mapeo = [
            // Área
            'm2' => 1, 'm²' => 1, 'M2' => 1, 'M²' => 1,

            // Volumen
            'm3' => 2, 'm³' => 2, 'M3' => 2, 'M³' => 2,

            // Líquidos
            'l' => 3, 'L' => 3, 'lt' => 3, 'LT' => 3,

            // Peso
            'kg' => 4, 'Kg' => 4, 'KG' => 4,
            'g' => 5, 'gr' => 5, 'G' => 5, 'GR' => 5,

            // Tiempo
            'h' => 6, 'H' => 6, 'hr' => 6, 'HR' => 6, 'hora' => 6, 'horas' => 6,

            // Unidades
            'ud' => 7, 'UD' => 7, 'u' => 7, 'U' => 7, 'un' => 7, 'UN' => 7,
            'unidad' => 7, 'unidades' => 7, 'UNIDAD' => 7, 'UNIDADES' => 7,

            // Por defecto
            '' => 7, null => 7
        ];

        $unidad_limpia = trim($unidad_bc3);
        return $mapeo[$unidad_limpia] ?? 7; // Por defecto: unidades
    }

    /**
     * Categorizar concepto automáticamente
     */
    public static function categorizarConcepto($descripcion, $codigo = '') {
        $descripcion_lower = strtolower($descripcion);
        $codigo_lower = strtolower($codigo);

        // Palabras clave para Mano de Obra
        $palabras_mo = ['mano de obra', 'oficial', 'peón', 'ayudante', 'instalación', 'colocación', 'montaje'];

        // Palabras clave para Material
        $palabras_mat = ['material', 'ladrillo', 'cemento', 'arena', 'grava', 'tubería', 'cable', 'azulejo'];

        // Palabras clave para Trabajo Externo
        $palabras_ext = ['subcontrata', 'externa', 'alquiler', 'transporte', 'grúa'];

        foreach ($palabras_mo as $palabra) {
            if (strpos($descripcion_lower, $palabra) !== false) {
                return 1; // Mano de obra
            }
        }

        foreach ($palabras_ext as $palabra) {
            if (strpos($descripcion_lower, $palabra) !== false) {
                return 3; // Trabajo externo
            }
        }

        // Por defecto: Material
        return 2;
    }

    /**
     * Generar resumen de importación
     */
    public static function generarResumenImportacion($resultado) {
        $resumen = "Importación completada:\n";
        $resumen .= "• Capítulos creados: " . $resultado['capitulos_creados'] . "\n";
        $resumen .= "• Partidas creadas: " . $resultado['partidas_creadas'] . "\n";
        $resumen .= "• Subpartidas creadas: " . $resultado['subpartidas_creadas'] . "\n";

        return $resumen;
    }

    /**
     * Log de errores durante importación
     */
    public static function logError($mensaje, $archivo_log = 'bc3_import.log') {
        $timestamp = date('Y-m-d H:i:s');
        $entrada_log = "[$timestamp] $mensaje" . PHP_EOL;

        $ruta_log = __DIR__ . '/logs/' . $archivo_log;

        // Crear directorio de logs si no existe
        $dir_logs = dirname($ruta_log);
        if (!is_dir($dir_logs)) {
            mkdir($dir_logs, 0755, true);
        }

        file_put_contents($ruta_log, $entrada_log, FILE_APPEND | LOCK_EX);
    }

    /**
     * Validar integridad de datos antes de insertar
     */
    public static function validarDatosCapitulo($datos) {
        $errores = [];

        if (empty($datos['descripcion']) || strlen($datos['descripcion']) > 500) {
            $errores[] = 'Descripción del capítulo inválida';
        }

        return $errores;
    }

    /**
     * Validar datos de partida
     */
    public static function validarDatosPartida($datos) {
        $errores = [];

        if (empty($datos['partida']) || strlen($datos['partida']) > 5500) {
            $errores[] = 'Nombre de partida inválido';
        }

        if (!is_numeric($datos['cantidad']) || $datos['cantidad'] < 0) {
            $errores[] = 'Cantidad inválida';
        }

        if (!is_numeric($datos['subtotal']) || $datos['subtotal'] < 0) {
            $errores[] = 'Subtotal inválido';
        }

        return $errores;
    }

    /**
     * Validar datos de subpartida
     */
    public static function validarDatosSubpartida($datos) {
        $errores = [];

        if (empty($datos['concepto']) || strlen($datos['concepto']) > 2500) {
            $errores[] = 'Concepto de subpartida inválido';
        }

        if (!is_numeric($datos['cantidad']) || $datos['cantidad'] < 0) {
            $errores[] = 'Cantidad inválida';
        }

        if (!is_numeric($datos['precio']) || $datos['precio'] < 0) {
            $errores[] = 'Precio inválido';
        }

        return $errores;
    }

    /**
     * Categorizar una subpartida según su código y descripción
     */
    public static function categorizarSubpartida($codigo, $descripcion) {
        return self::categorizarConcepto($descripcion, $codigo);
    }
}

?> 
