<?php
/**
 * Helpers globales para formateo de números y moneda
 */

/**
 * Formatea un número como moneda española (1.234,56€)
 * @param float $number El número a formatear
 * @param bool $symbol Si debe incluir el símbolo €
 * @return string Número formateado
 */
function formatCurrency($number, $symbol = true) {
    if ($number === null || $number === '') return '-';
    $formatted = number_format(round($number, 2), 2, ',', '.');
    return $symbol ? $formatted . '€' : $formatted;
}

/**
 * Formatea un número español (1.234,56)
 * @param float $number El número a formatear
 * @param int $decimals Número de decimales
 * @return string Número formateado
 */
function formatNumber($number, $decimals = 2) {
    if ($number === null || $number === '') return '-';
    return number_format(round($number, $decimals), $decimals, ',', '.');
}
