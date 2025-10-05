<?php
/**
 * ImportadorBC3 - Clase para importar archivos BC3 (FIEBDC-3/2024)
 * 
 * Esta clase implementa un parseador robusto para archivos BC3 según el estándar FIEBDC-3/2024,
 * con soporte para todos los tipos de registros (~V, ~K, ~C, ~D, etc.) y manejo de errores mejorado.
 * 
 * @author STC21
 * @version 1.0
 */

class ImportadorBC3 {
    private $db;
    private $idPresupuesto;
    protected $filePath;
    protected $fileName;
    protected $fileContent;
    private $records = [];
    protected $conceptos = [];
    protected $descompuestos = [];
    private $codeToIdMap = [];
    protected $errors = [];
    private $decimales = 2; // Por defecto 2 decimales según FIEBDC
    protected $stats = [
        'capitulos' => 0,
        'partidas' => 0,
        'subpartidas' => 0,
        'total' => 0
    ];

    // Para rastrear qué capítulos se han procesado como subcapítulos
    private $procesadoComoSubcapitulo = [];

    /**
     * Constructor
     * 
     * @param mysqli $db Conexión a la base de datos
     * @param int $idPresupuesto ID del presupuesto al que se asociarán los datos
     */
    public function __construct($db, $idPresupuesto) {
        $this->db = $db;
        $this->idPresupuesto = (int)$idPresupuesto;

        if (!$this->db) {
            throw new Exception('Error: No se proporcionó una conexión válida a la base de datos');
        }

        if (!$this->idPresupuesto) {
            throw new Exception('Error: ID de presupuesto inválido');
        }
    }

    /**
     * Importa un archivo BC3
     * 
     * @param string $filePath Ruta al archivo BC3
     * @param string $originalFileName Nombre original del archivo (opcional)
     * @return array Resultado de la importación
     */
    public function importarArchivo($filePath, $originalFileName = null) {
        try {
            $this->filePath = $filePath;
            $this->fileName = $originalFileName ?? basename($filePath);

            // Reinicializar configuración para cada archivo
            $this->decimales = 2; // Valor por defecto según FIEBDC
            $this->records = [];
            $this->conceptos = [];
            $this->descompuestos = [];
            $this->codeToIdMap = [];
            $this->errors = [];
            $this->stats = [
                'capitulos' => 0,
                'partidas' => 0,
                'subpartidas' => 0,
                'total' => 0
            ];

            $this->logError("=== INICIANDO IMPORTACIÓN DE ARCHIVO: " . $this->fileName . " ===");

            // Validar archivo
            $this->validarArchivo();

            // Leer contenido
            $this->leerContenido();

            // Parsear registros
            $this->parsearRegistros();

            // Procesar datos en la base de datos
            $this->procesarDatos();

            return [
                'success' => true,
                'message' => 'Archivo BC3 importado correctamente',
                'stats' => $this->stats,
                'total_formateado' => number_format($this->stats['total'], 2, ',', '.') . '€'
            ];

        } catch (Exception $e) {
            $this->logError('Error en importación: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $this->errors
            ];
        }
    }

    /**
     * Valida que el archivo sea un BC3 válido
     */
    private function validarArchivo() {
        if (!file_exists($this->filePath)) {
            throw new Exception('El archivo no existe');
        }

        // Usar el nombre original del archivo para verificar la extensión
        $extension = pathinfo($this->fileName, PATHINFO_EXTENSION);
        if (strtolower($extension) !== 'bc3') {
            throw new Exception('El archivo debe tener extensión .bc3');
        }

        // Verificar que el archivo comienza con ~V
        $handle = fopen($this->filePath, 'r');
        if (!$handle) {
            throw new Exception('No se puede leer el archivo');
        }

        $primeraLinea = fgets($handle);
        fclose($handle);

        if (substr(trim($primeraLinea), 0, 3) !== '~V|') {
            throw new Exception('El archivo no tiene formato BC3 válido (debe comenzar con ~V)');
        }
    }

    /**
     * Lee el contenido del archivo y lo convierte a UTF-8
     */
    protected function leerContenido() {
        $this->fileContent = file_get_contents($this->filePath);

        // Convertir a UTF-8 si es necesario
        if (!mb_check_encoding($this->fileContent, 'UTF-8')) {
            $this->fileContent = mb_convert_encoding($this->fileContent, 'UTF-8', 'ISO-8859-1');
        }
    }

    /**
     * Parsea los registros del archivo BC3
     */
    protected function parsearRegistros() {
        $lineas = explode("\n", $this->fileContent);

        foreach ($lineas as $numLinea => $linea) {
            $linea = trim($linea);
            if (empty($linea)) continue;

            try {
                $partes = explode('|', $linea);
                if (count($partes) < 2) continue;

                $tipoRegistro = substr($partes[0], 0, 2);

                switch ($tipoRegistro) {
                    case '~V': // Versión
                        $this->procesarRegistroV($partes);
                        break;

                    case '~K': // Constantes
                        $this->procesarRegistroK($partes);
                        break;

                    case '~C': // Conceptos
                        $this->procesarRegistroC($partes);
                        break;

                    case '~D': // Descomposiciones
                        $this->procesarRegistroD($partes);
                        break;

                    case '~T': // Textos
                        $this->procesarRegistroT($partes);
                        break;

                    case '~Y': // Descomposiciones adicionales
                        $this->procesarRegistroY($partes);
                        break;

                    case '~R': // Residuos
                        $this->procesarRegistroR($partes);
                        break;
                }

                // Guardar registro procesado
                $this->records[] = [
                    'tipo' => $tipoRegistro,
                    'partes' => $partes
                ];

            } catch (Exception $e) {
                $this->logError("Error en línea " . ($numLinea + 1) . ": " . $e->getMessage());
            }
        }
    }

    /**
     * Procesa un registro de tipo V (Versión)
     */
    private function procesarRegistroV($partes) {
        if (count($partes) < 9) {
            throw new Exception('Registro ~V incompleto');
        }

        $this->records['version'] = [
            'empresa' => $partes[1] ?? '',
            'formato' => $partes[2] ?? '',
            'fecha' => $partes[3] ?? '',
            'programa' => $partes[4] ?? '',
            'cabecera' => $partes[5] ?? '',
            'charset' => $partes[6] ?? '',
            'comentario' => $partes[7] ?? '',
            'tipo_info' => $partes[8] ?? ''
        ];
    }

    /**
     * Procesa un registro de tipo K (Constantes)
     */
    private function procesarRegistroK($partes) {
        $this->logError("Procesando registro ~K: " . implode('|', $partes));
        
        // El registro ~K define los decimales para diferentes campos numéricos
        // Las cantidades en el archivo BC3 ya están en valor real, no necesitan escalado
        if (count($partes) >= 2 && !empty($partes[1])) {
            $constantes = explode('\\', $partes[1]);
            $this->logError("Constantes encontradas: " . count($constantes) . " elementos");
            
            if (count($constantes) >= 4) {
                $this->decimales = (int)($constantes[3] ?? 2);
                $this->logError("Decimales para cantidades (solo informativo): " . $this->decimales);
                
                // Mostrar configuración para debug
                $this->logError("Configuración completa del registro ~K:");
                $this->logError("  - DC (Precios costes): " . ($constantes[0] ?? 'N/A'));
                $this->logError("  - DNP (Precios no descompuestos): " . ($constantes[1] ?? 'N/A'));
                $this->logError("  - DS (Precios suministros): " . ($constantes[2] ?? 'N/A'));
                $this->logError("  - DR (Cantidades): " . ($constantes[3] ?? 'N/A'));
                $this->logError("  - DIVISA: " . ($constantes[7] ?? 'N/A'));
            } else {
                $this->logError("ADVERTENCIA: Registro ~K incompleto");
            }
        } else {
            $this->logError("ADVERTENCIA: Registro ~K vacío o malformado");
        }
        
        // Guardar registro procesado
        $this->records['constantes'] = $partes;
    }

    /**
     * Procesa un registro de tipo C (Conceptos)
     */
    private function procesarRegistroC($partes) {
        if (count($partes) < 5) {
            throw new Exception('Registro ~C incompleto');
        }

        $codigo = $partes[1];
        $this->conceptos[$codigo] = [
            'unidad' => $partes[2] ?? '',
            'descripcion' => $partes[3] ?? '',
            'precio' => (float)($partes[4] ?? 0),
            'fecha' => $partes[5] ?? '',
            'tipo' => $partes[6] ?? '0'
        ];
    }

    /**
     * Procesa un registro de tipo D (Descomposiciones)
     */
    private function procesarRegistroD($partes) {
        if (count($partes) < 3) {
            throw new Exception('Registro ~D incompleto');
        }

        $codigo = $partes[1];
        $elementos = [];

        $this->logError("Procesando descomposición para: " . $codigo);

        // Procesar elementos de la descomposición desde el campo 2 en adelante
        for ($i = 2; $i < count($partes); $i++) {
            if (empty($partes[$i])) continue;

            $elementosStr = $partes[$i];
            $this->logError("  Campo " . $i . ": " . $elementosStr);

            // Los elementos están separados por \ y siguen el patrón: codigo\factor\cantidad\
            // Dividir por backslash pero manejar decimales correctamente
            $partesElementos = $this->parseDescomposicionElementos($elementosStr);

            foreach ($partesElementos as $elemento) {
                if (!empty($elemento['codigo'])) {
                    $elementos[] = $elemento;
                    $this->logError("    Elemento: " . $elemento['codigo'] . " (factor: " . $elemento['factor'] . ", cantidad: " . $elemento['cantidad'] . ")");
                }
            }
        }

        if (!empty($elementos)) {
            $this->descompuestos[$codigo] = $elementos;
            $this->logError("  Total elementos añadidos: " . count($elementos));
        }
    }

    /**
     * Parsea los elementos de una descomposición manejando decimales correctamente
     */
    private function parseDescomposicionElementos($elementosStr) {
        $elementos = [];
        
        // Dividir por backslash
        $tokens = explode('\\', $elementosStr);
        
        $i = 0;
        while ($i < count($tokens)) {
            $codigo = trim($tokens[$i]);
            
            // Saltar tokens vacíos
            if (empty($codigo)) {
                $i++;
                continue;
            }
            
            // Buscar factor (siguiente token no vacío)
            $factor = 1.0;
            $cantidad = 1.0;
            
            if ($i + 1 < count($tokens) && !empty(trim($tokens[$i + 1]))) {
                $factor = (float)str_replace(',', '.', trim($tokens[$i + 1]));
                
                // Buscar cantidad (siguiente token)
                if ($i + 2 < count($tokens)) {
                    $cantidadStr = trim($tokens[$i + 2]);
                    
                    // Manejar casos como "0.02" donde el decimal puede estar en el siguiente token
                    if (empty($cantidadStr) && $i + 3 < count($tokens)) {
                        $siguienteToken = trim($tokens[$i + 3]);
                        if (is_numeric($siguienteToken)) {
                            $cantidad = (float)str_replace(',', '.', $siguienteToken);
                            $i += 4; // Saltar 4 tokens (codigo, factor, cantidad_vacia, cantidad_real)
                        } else {
                            $cantidad = 0.0;
                            $i += 3; // Saltar 3 tokens (codigo, factor, cantidad_vacia)
                        }
                    } else if (!empty($cantidadStr)) {
                        $cantidad = (float)str_replace(',', '.', $cantidadStr);
                        $i += 3; // Saltar 3 tokens (codigo, factor, cantidad)
                    } else {
                        $cantidad = 0.0;
                        $i += 3; // Saltar 3 tokens (codigo, factor, cantidad_vacia)
                    }
                } else {
                    $i += 2; // Solo código y factor
                }
            } else {
                $i += 1; // Solo código
            }
            
            // CORRECCIÓN: Las cantidades en el archivo BC3 ya están en valor real
            // NO necesitan ser escaladas por el factor del registro ~K
            $cantidadReal = $cantidad;
            
            // Log de transformación para debugging
            if ($cantidad != 0) {
                $this->logError("    Cantidad procesada - Código: $codigo, Factor: $factor, Cantidad: $cantidadReal");
            }
            
            $elementos[] = [
                'codigo' => $codigo,
                'factor' => $factor,
                'cantidad' => $cantidadReal
            ];
        }
        
        return $elementos;
    }

    /**
     * Procesa un registro de tipo T (Textos)
     */
    private function procesarRegistroT($partes) {
        // Implementación básica, se puede expandir según necesidades
        if (count($partes) < 3) {
            throw new Exception('Registro ~T incompleto');
        }

        $codigo = $partes[1];
        $texto = $partes[2] ?? '';

        if (isset($this->conceptos[$codigo])) {
            $this->conceptos[$codigo]['texto_largo'] = $texto;
        }
    }

    /**
     * Procesa un registro de tipo Y (Descomposiciones adicionales)
     */
    private function procesarRegistroY($partes) {
        // Implementación básica, similar a procesarRegistroD
        // Se puede expandir según necesidades
    }

    /**
     * Procesa un registro de tipo R (Residuos)
     */
    private function procesarRegistroR($partes) {
        // Implementación básica, se puede expandir según necesidades
    }

    /**
     * Procesa los datos en la base de datos
     */
    protected function procesarDatos() {
        // Iniciar transacción
        $this->db->autocommit(FALSE);

        try {
            // Limpiar datos existentes
            $this->limpiarDatosExistentes();

            // Resetear contadores de estadísticas
            $this->stats = [
                'capitulos' => 0,
                'partidas' => 0,
                'subpartidas' => 0,
                'total' => 0
            ];

            $this->logError("=== INICIANDO PROCESAMIENTO DE DATOS BC3 ===");
            $this->logError("Total conceptos encontrados: " . count($this->conceptos));
            $this->logError("Total descomposiciones encontradas: " . count($this->descompuestos));

            // Buscar el presupuesto raíz (códigos con ##)
            $presupuestoRaiz = null;
            foreach ($this->conceptos as $codigo => $datos) {
                if (strpos($codigo, '##') !== false) {
                    $presupuestoRaiz = $codigo;
                    $this->logError("Presupuesto raíz (total informativo): " . number_format($datos['precio'], 2, ',', '.') . "€");
                    break;
                }
            }

            if (!$presupuestoRaiz) {
                throw new Exception('No se encontró el presupuesto raíz (código con ##)');
            }

            $this->logError("Presupuesto raíz encontrado: " . $presupuestoRaiz . " - " . $this->conceptos[$presupuestoRaiz]['descripcion']);

            // Procesar desde el presupuesto raíz
            $this->procesarElemento($presupuestoRaiz, null, 0, 1);

            // Confirmar transacción
            $this->db->commit();
            $this->db->autocommit(TRUE);

            $this->logError("=== PROCESAMIENTO COMPLETADO ===");
            $this->logError("=== TOTAL CALCULADO SUMANDO PARTIDAS: " . number_format($this->stats['total'], 2, ',', '.') . "€ ===");
            $this->logError("Estadísticas finales: " . json_encode($this->stats));

        } catch (Exception $e) {
            $this->db->rollback();
            $this->db->autocommit(TRUE);
            throw $e;
        }
    }

    /**
     * Procesa un elemento y sus hijos recursivamente
     */
    private function procesarElemento($codigo, $idPadre, $nivel, $cantidadPadre = 1) {
        // Buscar el concepto, si no lo encuentra, intentar variantes
        $concepto = null;
        $codigoFinal = $codigo;
        
        if (isset($this->conceptos[$codigo])) {
            $concepto = $this->conceptos[$codigo];
        } else {
            // Si no se encuentra el código exacto, intentar variantes comunes
            $variantes = [];

            // 1) Variante genérica: si el código NO lleva # probar añadiéndolo
            if (strpos($codigo, '#') === false) {
                $variantes[] = $codigo . '#';
            }

            // 2) Variante para códigos numéricos simples (1, 2, 3) añadiendo #
            if (preg_match('/^\d+$/', $codigo)) {
                $variantes[] = $codigo . '#';
            }

            // 3) Variante para códigos con punto (11.1) añadiendo #
            if (preg_match('/^\d+\.\d+$/', $codigo)) {
                $variantes[] = $codigo . '#';
            }

            // Eliminar posibles duplicados
            $variantes = array_unique($variantes);

            // Buscar variantes
            foreach ($variantes as $variante) {
                if (isset($this->conceptos[$variante])) {
                    $concepto = $this->conceptos[$variante];
                    $codigoFinal = $variante;
                    $this->logError("  Código mapeado: $codigo -> $variante");
                    break;
                }
            }
        }
        
        if (!$concepto) {
            $this->logError("ADVERTENCIA: Concepto no encontrado: " . $codigo);
            return null;
        }

        $indentacion = str_repeat("  ", $nivel);
        
        $this->logError($indentacion . "Procesando [" . $codigoFinal . "]: " . substr($concepto['descripcion'], 0, 60));

        $tipoElemento = $this->determinarTipoElemento($codigoFinal, $concepto);
        $idElemento = null;

        switch ($tipoElemento) {
            case 'presupuesto_raiz':
                // El presupuesto raíz no se inserta como capítulo, solo procesamos sus hijos
                $this->logError($indentacion . "-> PRESUPUESTO RAÍZ");
                break;

            case 'capitulo':
                // CAPÍTULOS PRINCIPALES: Son agrupadores, NO suman al total
                $nombreCapitulo = substr($concepto['descripcion'], 0, 500);
                $idElemento = $this->crearCapitulo($nombreCapitulo);
                $this->stats['capitulos']++;
                $this->logError($indentacion . "-> CAPÍTULO creado con ID: " . $idElemento . " (NO suma al total - es agrupador)");
                break;

            case 'subcapitulo':
                $nombreSubcapitulo = substr($concepto['descripcion'], 0, 500);
                $idElemento = $this->crearCapitulo($nombreSubcapitulo);
                $this->stats['capitulos']++;
                $this->logError($indentacion . "-> SUBCAPÍTULO creado con ID: " . $idElemento);
                break;

            case 'partida':
                // PARTIDAS REALES: Solo estas suman al total del presupuesto
                if ($concepto['precio'] > 0) {
                    // Usar la cantidad del padre (de la descomposición) en lugar de 1
                    $cantidad = $cantidadPadre;
                    
                    // Si no hay padre, crear un capítulo temporal o usar null
                    $idCapituloPadre = $idPadre;
                    if (!$idCapituloPadre) {
                        // Para partidas sin padre, crear capítulo general
                        $nombreCapituloTemp = "Partidas Generales";
                        $idCapituloPadre = $this->crearCapitulo($nombreCapituloTemp);
                        $this->logError($indentacion . "-> Capítulo general creado para partida: ID " . $idCapituloPadre);
                    }
                    
                    $idElemento = $this->crearPartida(
                        $idCapituloPadre,
                        $concepto['descripcion'],
                        $cantidad,
                        $concepto['precio'],
                        $concepto['unidad']
                    );
                    $this->stats['partidas']++;
                    $this->logError($indentacion . "-> PARTIDA REAL creada con ID: " . $idElemento . " (cantidad: " . $cantidad . ", precio: " . $concepto['precio'] . "€) ✅ SUMA AL TOTAL");
                } else {
                    // Partidas sin precio - crear como capítulo
                    $nombrePartida = substr($concepto['descripcion'], 0, 500);
                    $idElemento = $this->crearCapitulo($nombrePartida);
                    $this->stats['capitulos']++;
                    $this->logError($indentacion . "-> PARTIDA SIN PRECIO (creada como capítulo) con ID: " . $idElemento);
                }
                break;

            case 'subpartida':
            case 'material':
            case 'mano_obra':
                if ($idPadre) {
                    // Usar la cantidad del padre (de la descomposición) en lugar de 1
                    $cantidad = $cantidadPadre;
                    $idElemento = $this->crearSubpartida(
                        $idPadre,
                        $codigoFinal,
                        $concepto['descripcion'],
                        $cantidad,
                        $concepto['precio'],
                        $concepto['unidad']
                    );
                    $this->stats['subpartidas']++;
                    $this->logError($indentacion . "-> SUBPARTIDA/MATERIAL creada con ID: " . $idElemento . " (cantidad: " . $cantidad . ")");
                }
                break;

            default:
                $this->logError($indentacion . "-> TIPO DESCONOCIDO: " . $tipoElemento);
                break;
        }

        // Procesar descomposiciones (hijos) - usar el código final mapeado
        if (isset($this->descompuestos[$codigoFinal])) {
            $hijos = $this->descompuestos[$codigoFinal];
            $this->logError($indentacion . "Tiene " . count($hijos) . " elementos en descomposición");

            foreach ($hijos as $hijo) {
                $codigoHijo = $hijo['codigo'];
                $cantidadHijo = $hijo['cantidad'];
                $factorHijo = $hijo['factor'];

                $this->logError($indentacion . "  -> Hijo: " . $codigoHijo . " (cantidad: " . $cantidadHijo . ", factor: " . $factorHijo . ")");

                // Determinar el ID padre apropiado para el hijo
                $idPadreHijo = $idElemento;
                
                // Si el elemento actual es presupuesto raíz, no tiene ID, seguimos sin padre
                if ($tipoElemento === 'presupuesto_raiz') {
                    $idPadreHijo = null;
                }

                // Procesar el hijo recursivamente pasando la cantidad real
                $idHijo = $this->procesarElemento($codigoHijo, $idPadreHijo, $nivel + 1, $cantidadHijo);

                // NO necesitamos actualizar la cantidad aquí porque ya se pasa al crear el elemento
            }
        } else {
            $this->logError($indentacion . "Sin descomposiciones");
        }

        return $idElemento;
    }

    /**
     * Determina el tipo de elemento basado en el código y la descripción
     */
    private function determinarTipoElemento($codigo, $concepto) {
        // Presupuesto raíz (códigos con ##)
        if (strpos($codigo, '##') !== false) {
            return 'presupuesto_raiz';
        }

        // CAPÍTULOS Y SUBCAPÍTULOS: Códigos con # son CONTENEDORES, no suman al total
        if (strpos($codigo, '#') !== false) {
            // Subcapítulos (formato X.Y# como 11.1#, 11.2#)
            if (preg_match('/^\\d+\\.\\d+#/', $codigo)) {
                return 'subcapitulo';
            }
            // Capítulos principales (formato X# como 1#, 2#, INST.01#)
            // ESTOS SON AGRUPADORES - NO SUMAN AL TOTAL
            return 'capitulo';
        }

        // PARTIDAS REALES: Solo códigos SIN # que tienen precio y suman al total
        // Partidas numéricas (formato XX.XX como 01.01, 02.01)
        if (preg_match('/^\\d{2}\\.\\d{2}$/', $codigo)) {
            return 'partida';
        }

        // Materiales y mano de obra (códigos alfanuméricos)
        if (preg_match('/^[a-zA-Z]/', $codigo)) {
            // Mano de obra (códigos que empiezan con 'mo' o 'O01')
            if (strpos($codigo, 'mo') === 0 || strpos($codigo, 'O01') === 0) {
                return 'mano_obra';
            }
            // Materiales (códigos que empiezan con 'mt', 'P11', 'mq', etc.)
            if (strpos($codigo, 'mt') === 0 || strpos($codigo, 'P11') === 0 || 
                strpos($codigo, 'mq') === 0 || strpos($codigo, '%') === 0) {
                return 'material';
            }
        }

        // Subpartidas (otros códigos numéricos o alfanuméricos)
        return 'subpartida';
    }

    /**
     * Limpia los datos existentes del presupuesto
     */
    private function limpiarDatosExistentes() {
        $result = $this->db->query("SELECT id FROM presupuestos_partidas WHERE id_presupuesto = {$this->idPresupuesto}");
        $partidasIds = [];

        while ($row = $result->fetch_assoc()) {
            $partidasIds[] = $row['id'];
        }

        if (!empty($partidasIds)) {
            $idsStr = implode(',', $partidasIds);
            $this->db->query("DELETE FROM presupuestos_subpartidas WHERE id_presupuesto_partidas IN ($idsStr)");
        }

        $this->db->query("DELETE FROM presupuestos_partidas WHERE id_presupuesto = {$this->idPresupuesto}");
        $this->db->query("DELETE FROM presupuestos_capitulos WHERE id_presupuesto = {$this->idPresupuesto}");
    }

    /**
     * Crea un capítulo en la base de datos
     */
    private function crearCapitulo($nombre) {
        $nombreEscapado = $this->db->real_escape_string($nombre);

        $result = $this->db->query("SELECT id FROM capitulos WHERE capitulo = '$nombreEscapado' LIMIT 1");

        if ($result->num_rows > 0) {
            $idCapitulo = $result->fetch_assoc()['id'];
        } else {
            $this->db->query("INSERT INTO capitulos (capitulo, is_active) VALUES ('$nombreEscapado', 1)");
            $idCapitulo = $this->db->insert_id;
        }

        $this->db->query("INSERT IGNORE INTO presupuestos_capitulos (id_presupuesto, id_capitulo) VALUES ({$this->idPresupuesto}, $idCapitulo)");

        // No incrementamos el contador aquí, ya lo hemos ajustado en procesarDatos()

        return $idCapitulo;
    }

    /**
     * Crea una partida en la base de datos
     */
    private function crearPartida($idCapitulo, $descripcion, $cantidad, $precioUnitario, $unidad) {
        require_once "utilidades_bc3.php";

        $nombrePartida = substr($descripcion, 0, 300);
        $descripcionCompleta = substr($descripcion, 0, 500);
        $idUnidad = UtilidadesBC3::mapearUnidad($unidad);

        // Calcular el importe total de la partida: precio × cantidad
        $importeTotal = $precioUnitario * $cantidad;

        $nombreEscapado = $this->db->real_escape_string($nombrePartida);
        $descripcionEscapada = $this->db->real_escape_string($descripcionCompleta);

        $this->logError("  Creando partida: $nombrePartida");
        $this->logError("    Precio unitario: " . number_format($precioUnitario, 2, ',', '.') . "€");
        $this->logError("    Cantidad: " . number_format($cantidad, 2, ',', '.'));
        $this->logError("    Importe total: " . number_format($importeTotal, 2, ',', '.') . "€");

        // CORRECCIÓN: subtotal = precio unitario, total = importe total
        $query = "INSERT INTO presupuestos_partidas 
            (id_presupuesto, id_capitulo, partida, descripcion, cantidad, subtotal, total, id_unidad) 
            VALUES ({$this->idPresupuesto}, $idCapitulo, 
            '$nombreEscapado', 
            '$descripcionEscapada', 
            $cantidad, 
            $precioUnitario, 
            $importeTotal, 
            $idUnidad)";

        if ($this->db->query($query)) {
            $idPartida = $this->db->insert_id;

            if ($idPartida) {
                $this->stats['partidas']++;
                
                // SOLO SUMAR AL TOTAL SI LA PARTIDA TIENE PRECIO REAL (> 0)
                if ($precioUnitario > 0) {
                    $this->stats['total'] += $importeTotal;
                    $this->logError("    ✓ Partida creada con ID: $idPartida - Importe añadido al total: " . number_format($importeTotal, 2, ',', '.') . "€");
                    $this->logError("    ✓ Total acumulado: " . number_format($this->stats['total'], 2, ',', '.') . "€");
                } else {
                    $this->logError("    ✓ Partida creada con ID: $idPartida - NO SUMA AL TOTAL (precio = 0, es agrupador)");
                }

                return $idPartida;
            }
        }

        return null;
    }

    /**
     * Crea una subpartida en la base de datos
     */
    private function crearSubpartida($idPartida, $codigo, $descripcion, $cantidad, $precioUnitario, $unidad) {
        require_once "utilidades_bc3.php";

        $nombreSubpartida = substr($descripcion, 0, 300);
        $descripcionCompleta = substr($descripcion, 0, 500);
        $idUnidad = UtilidadesBC3::mapearUnidad($unidad);
        $idCategoria = UtilidadesBC3::categorizarSubpartida($codigo, $nombreSubpartida);

        // Calcular el importe total de la subpartida: precio × cantidad
        $importeTotal = $precioUnitario * $cantidad;

        $nombreEscapado = $this->db->real_escape_string($nombreSubpartida);
        $descripcionEscapada = $this->db->real_escape_string($descripcionCompleta);

        $this->logError("    Creando subpartida: $codigo - $nombreSubpartida");
        $this->logError("      Precio unitario: " . number_format($precioUnitario, 2, ',', '.') . "€");
        $this->logError("      Cantidad: " . number_format($cantidad, 2, ',', '.'));
        $this->logError("      Importe total: " . number_format($importeTotal, 2, ',', '.') . "€ (NO suma al total presupuesto)");

        $query = "INSERT INTO presupuestos_subpartidas 
            (id_presupuesto_partidas, id_categoria, concepto, descripcion, cantidad, precio, subtotal, total, id_unidad, id_iva) 
            VALUES ($idPartida, $idCategoria, 
            '$nombreEscapado', 
            '$descripcionEscapada', 
            $cantidad, 
            $precioUnitario, 
            $importeTotal, 
            $importeTotal, 
            $idUnidad, 1)";

        if ($this->db->query($query)) {
            $this->stats['subpartidas']++;
            
            // LAS SUBPARTIDAS NO SUMAN AL TOTAL DEL PRESUPUESTO
            // Ya están incluidas en el precio de la partida padre
            
            return $this->db->insert_id;
        }

        return null;
    }

    /**
     * Registra un error en el log
     */
    private function logError($mensaje) {
        $this->errors[] = $mensaje;

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $mensaje" . PHP_EOL;

        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/bc3_import.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

        error_log("BC3: $mensaje");
    }
}
