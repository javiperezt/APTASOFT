/**
 * Formatea números al estilo español: 1.224,50
 * Se aplica automáticamente a elementos con atributos data-format
 */

// Función para formatear números al estilo español
function formatNumberES(number) {
    if (number === null || number === undefined || number === '') return '';

    // Convertir a número si es string
    const num = typeof number === 'string' ? parseFloat(number.replace(/\./g, '').replace(',', '.')) : number;

    if (isNaN(num)) return number;

    // Formatear con separador de miles (.) y decimales (,)
    return new Intl.NumberFormat('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}

// Función para formatear automáticamente elementos con data-format="currency"
function autoFormatNumbers() {
    // Formatear elementos con data-format="currency"
    document.querySelectorAll('[data-format="currency"]').forEach(element => {
        const value = element.textContent.trim().replace('€', '').trim();
        if (value && value !== '-') {
            const formatted = formatNumberES(value);
            element.textContent = formatted + '€';
        }
    });

    // Formatear elementos con data-format="number"
    document.querySelectorAll('[data-format="number"]').forEach(element => {
        const value = element.textContent.trim();
        if (value && value !== '-') {
            element.textContent = formatNumberES(value);
        }
    });
}

// Ejecutar al cargar el DOM
document.addEventListener('DOMContentLoaded', autoFormatNumbers);

// Exportar funciones para uso manual
window.formatNumberES = formatNumberES;
window.autoFormatNumbers = autoFormatNumbers;
