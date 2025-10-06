<?php
session_start();

include "../conexion.php";
require_once "../DateClass.php";

require_once "../authCookieSessionValidate.php";
if (!$isLoggedIn) {
    header("Location: ../index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Planificación de Tareas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <?php include "../links_header.php"; ?>

    <style>
        body {
            background: #f5f5f7;
        }

        .planificacion-container {
            display: flex;
            gap: 16px;
            margin-top: 20px;
        }

        /* Panel lateral de tareas */
        .tareas-sidebar {
            width: 340px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            max-height: calc(100vh - 140px);
            overflow-y: auto;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e5e7;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }

        .sidebar-header h6 {
            margin: 0;
            font-weight: 600;
            color: #495057;
        }

        /* Grupo de obra */
        .obra-group {
            margin-bottom: 20px;
        }

        .obra-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            background: white;
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
            border-left: 4px solid;
        }

        .obra-header:hover {
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .obra-header i {
            font-size: 1.1rem;
        }

        .obra-title {
            flex: 1;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .obra-badge {
            background: #e9ecef;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Tareas arrastrables */
        .external-event {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: move;
            transition: all 0.2s;
            border-left: 3px solid;
        }

        .external-event:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transform: translateX(4px);
        }

        .external-event.dragging {
            opacity: 0.5;
        }

        .event-partida {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 4px;
        }

        .event-concepto {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .event-cantidad {
            font-size: 0.75rem;
            color: #6c757d;
        }

        /* Calendario Personalizado */
        .calendar-wrapper {
            flex: 1;
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            border: 1px solid #e5e5e7;
        }

        /* Header del calendario */
        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }

        .week-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .week-nav .btn {
            padding: 8px 16px;
            border-radius: 8px;
        }

        .current-week {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            min-width: 280px;
            text-align: center;
        }

        /* Grid de días */
        .week-grid {
            display: grid;
            grid-template-columns: 140px repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .employee-header {
            background: #ffffff;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.75rem;
            color: #86868b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
            border: 1px solid #e5e5e7;
        }

        .day-header {
            background: #ffffff;
            color: #1d1d1f;
            padding: 12px 8px;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
            position: sticky;
            top: 0;
            z-index: 10;
            border: 1px solid #e5e5e7;
            transition: all 0.2s;
        }

        .day-header.today {
            background: #007aff;
            color: white;
            border-color: #007aff;
            box-shadow: 0 2px 8px rgba(0, 122, 255, 0.25);
        }

        .day-name {
            font-size: 0.7rem;
            text-transform: uppercase;
            opacity: 0.6;
            margin-bottom: 3px;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .day-header.today .day-name {
            opacity: 0.85;
        }

        .day-number {
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* Filas de empleados */
        .employee-row {
            display: contents;
        }

        .employee-name-cell {
            background: #ffffff;
            padding: 12px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 0.85rem;
            color: #1d1d1f;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e5e5e7;
        }

        .employee-avatar-small {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #007aff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Celdas de días */
        .day-cell {
            background: #f5f5f7;
            border-radius: 10px;
            padding: 8px;
            min-height: 100px;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .day-cell.droppable {
            border: 2px dashed transparent;
        }

        .day-cell.drag-over {
            background: #e8f4fd;
            border-color: #007aff;
            transform: scale(1.01);
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.1);
        }

        /* Tarjetas de evento */
        .event-card {
            background: white;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 6px;
            cursor: grab;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e5e7;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }

        .event-card:active {
            cursor: grabbing;
        }

        .event-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border-color: #d1d1d6;
        }

        .event-card.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .event-obra-badge {
            display: inline-block;
            background: #f5f5f7;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.65rem;
            color: #86868b;
            margin-bottom: 6px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .event-title {
            font-weight: 600;
            font-size: 0.8rem;
            margin-bottom: 3px;
            color: #1d1d1f;
            line-height: 1.4;
        }

        .event-subtitle {
            font-size: 0.72rem;
            color: #86868b;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-notes {
            font-size: 0.65rem;
            color: #aeaeb2;
            line-height: 1.3;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #f5f5f7;
            font-style: italic;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-notes:empty {
            display: none;
        }

        /* Loading overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        /* Filtro de obra */
        .obra-filter {
            max-width: 350px;
        }

        /* Estado vacío */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<?php include "../components/navbar.php"; ?>

<!-- Header -->
<div class="container-fluid mt-3">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-calendar3"></i> Planificación de Tareas
        </h3>

        <!-- Filtros -->
        <div class="d-flex gap-2">
            <select id="obraFilter" class="form-select" style="min-width: 200px;">
                <option value="all">Todas las obras</option>
            </select>
            <select id="empleadoFilter" class="form-select" style="min-width: 200px;">
                <option value="all">Todos los empleados</option>
            </select>
        </div>
    </div>

    <!-- Contenedor Principal -->
    <div class="planificacion-container">
        <!-- Panel Lateral de Tareas -->
        <div class="tareas-sidebar">
            <div class="sidebar-header">
                <i class="bi bi-list-task"></i>
                <h6>Tareas Disponibles</h6>
            </div>
            <div id="tareasContainer">
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Cargando tareas...</p>
                </div>
            </div>
        </div>

        <!-- Calendario Personalizado -->
        <div class="calendar-wrapper">
            <!-- Header con navegación -->
            <div class="calendar-header">
                <div class="week-nav">
                    <button class="btn btn-outline-primary" id="prevWeek">
                        <i class="bi bi-chevron-left"></i> Anterior
                    </button>
                    <div class="current-week" id="currentWeekLabel">
                        Semana del ...
                    </div>
                    <button class="btn btn-outline-primary" id="nextWeek">
                        Siguiente <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
                <button class="btn btn-primary" id="todayBtn">
                    <i class="bi bi-calendar-today"></i> Hoy
                </button>
            </div>

            <!-- Grid del calendario -->
            <div id="calendarGrid" class="week-grid">
                <!-- Se genera dinámicamente -->
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Asignación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Obra:</strong> <span id="modalObra"></span></p>
                <p><strong>Tarea:</strong> <span id="modalTarea"></span></p>
                <p><strong>Empleado:</strong> <span id="modalEmpleado"></span></p>
                <p><strong>Fechas:</strong> <span id="modalFechas"></span></p>
                <div class="mb-3">
                    <label for="modalInfo" class="form-label">Notas:</label>
                    <textarea id="modalInfo" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteEventBtn">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
                <button type="button" class="btn btn-primary" id="saveNotesBtn">
                    <i class="bi bi-save"></i> Guardar Notas
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    let empleadosData = [];
    let tareasData = [];
    let asignacionesData = [];
    let currentEventId = null;
    let currentObraFilter = 'all';
    let currentEmpleadoFilter = 'all';

    // Control de semana
    let currentWeekStart = new Date();
    currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1); // Lunes
    currentWeekStart.setHours(0, 0, 0, 0);

    document.addEventListener('DOMContentLoaded', async function() {
        await initApp();
    });

    async function initApp() {
        showLoading(true);
        await loadObras();
        await loadEmpleados();
        await loadTareas();
        await loadAsignaciones();
        populateEmpleadoFilter();
        renderCalendar();
        renderTareasSidebar();
        showLoading(false);

        // Event listeners
        document.getElementById('obraFilter').addEventListener('change', handleObraFilterChange);
        document.getElementById('empleadoFilter').addEventListener('change', handleEmpleadoFilterChange);
        document.getElementById('deleteEventBtn').addEventListener('click', deleteEvent);
        document.getElementById('saveNotesBtn').addEventListener('click', saveNotes);
        document.getElementById('prevWeek').addEventListener('click', () => changeWeek(-1));
        document.getElementById('nextWeek').addEventListener('click', () => changeWeek(1));
        document.getElementById('todayBtn').addEventListener('click', goToToday);
    }

    function populateEmpleadoFilter() {
        const select = document.getElementById('empleadoFilter');
        empleadosData.forEach(emp => {
            const option = document.createElement('option');
            option.value = emp.id;
            option.textContent = emp.nombre;
            select.appendChild(option);
        });
    }

    async function loadObras() {
        try {
            const response = await fetch('../backend/calendario/getObras.php');
            const obras = await response.json();

            const select = document.getElementById('obraFilter');
            obras.forEach(obra => {
                const option = document.createElement('option');
                option.value = obra.id;
                option.textContent = obra.titulo;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error cargando obras:', error);
        }
    }

    async function loadEmpleados() {
        try {
            const response = await fetch('../backend/calendario/getEmpleados.php');
            empleadosData = await response.json();
        } catch (error) {
            console.error('Error cargando empleados:', error);
        }
    }

    async function loadTareas() {
        try {
            const url = `../backend/calendario/getTareasMO.php?id_obra=${currentObraFilter}`;
            const response = await fetch(url);
            tareasData = await response.json();
        } catch (error) {
            console.error('Error cargando tareas:', error);
        }
    }

    async function loadAsignaciones() {
        try {
            const weekEnd = new Date(currentWeekStart);
            weekEnd.setDate(weekEnd.getDate() + 6);

            const url = `../backend/calendario/getAsignaciones.php?id_obra=${currentObraFilter}&fecha_inicio=${formatDate(currentWeekStart)}&fecha_fin=${formatDate(weekEnd)}`;
            const response = await fetch(url);
            asignacionesData = await response.json();

            // Filtrar por empleado si hay filtro activo
            if (currentEmpleadoFilter !== 'all') {
                asignacionesData = asignacionesData.filter(asig => asig.id_empleado == currentEmpleadoFilter);
            }
        } catch (error) {
            console.error('Error cargando asignaciones:', error);
        }
    }

    function changeWeek(offset) {
        currentWeekStart.setDate(currentWeekStart.getDate() + (offset * 7));
        renderCalendar();
        loadAsignaciones().then(() => renderCalendar());
    }

    function goToToday() {
        currentWeekStart = new Date();
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay() + 1);
        currentWeekStart.setHours(0, 0, 0, 0);
        loadAsignaciones().then(() => renderCalendar());
    }

    function renderCalendar() {
        const grid = document.getElementById('calendarGrid');
        const weekLabel = document.getElementById('currentWeekLabel');

        // Calcular fechas de la semana
        const weekDays = [];
        for (let i = 0; i < 7; i++) {
            const day = new Date(currentWeekStart);
            day.setDate(day.getDate() + i);
            weekDays.push(day);
        }

        // Actualizar label de la semana
        const firstDay = weekDays[0];
        const lastDay = weekDays[6];
        weekLabel.textContent = `${formatDateLabel(firstDay)} - ${formatDateLabel(lastDay)}`;

        // Filtrar empleados si hay filtro activo
        let empleadosFiltrados = empleadosData;
        if (currentEmpleadoFilter !== 'all') {
            empleadosFiltrados = empleadosData.filter(emp => emp.id == currentEmpleadoFilter);
        }

        // Generar HTML del grid
        let html = '';

        // Header row
        html += '<div class="employee-header">Empleado</div>';
        weekDays.forEach(day => {
            const isTodayFlag = isTodayDate(day);
            html += `
                <div class="day-header ${isTodayFlag ? 'today' : ''}">
                    <div class="day-name">${getDayName(day)}</div>
                    <div class="day-number">${day.getDate()}</div>
                </div>
            `;
        });

        // Employee rows
        empleadosFiltrados.forEach(empleado => {
            html += `<div class="employee-row">`;

            // Nombre del empleado
            const iniciales = getIniciales(empleado.nombre);
            html += `
                <div class="employee-name-cell">
                    <div class="employee-avatar-small">${iniciales}</div>
                    <span>${empleado.nombre}</span>
                </div>
            `;

            // Celdas de cada día
            weekDays.forEach(day => {
                const dayStr = formatDate(day);
                const eventos = getEventosForDay(empleado.id, dayStr);

                html += `
                    <div class="day-cell droppable"
                         data-empleado-id="${empleado.id}"
                         data-fecha="${dayStr}"
                         ondrop="handleDrop(event)"
                         ondragover="handleDragOver(event)"
                         ondragleave="handleDragLeave(event)">
                        ${eventos.map(ev => renderEventCard(ev)).join('')}
                    </div>
                `;
            });

            html += `</div>`;
        });

        grid.innerHTML = html;

        // Agregar event listeners a las tarjetas
        grid.querySelectorAll('.event-card').forEach(card => {
            card.addEventListener('dragstart', handleEventDragStart);
            card.addEventListener('dragend', handleEventDragEnd);
            card.addEventListener('click', (e) => {
                const eventId = card.dataset.eventId;
                const event = asignacionesData.find(ev => ev.id == eventId);
                if (event) showEventDetails(event);
            });
        });
    }

    function getEventosForDay(empleadoId, fecha) {
        return asignacionesData.filter(ev => {
            return ev.id_empleado == empleadoId &&
                   ev.fecha_inicio <= fecha &&
                   ev.fecha_fin >= fecha;
        });
    }

    function renderEventCard(evento) {
        const notes = evento.info ? `<div class="event-notes">${evento.info}</div>` : '';

        // Filtrar conceptos genéricos que no aportan información
        const conceptosIgnorados = ['mano obra', 'mano de obra', 'mo'];
        const concepto = evento.concepto && !conceptosIgnorados.includes(evento.concepto.toLowerCase().trim())
            ? `<div class="event-subtitle">${evento.concepto}</div>`
            : '';

        return `
            <div class="event-card"
                 draggable="true"
                 data-event-id="${evento.id}"
                 data-empleado-id="${evento.id_empleado}">
                <div class="event-obra-badge">${evento.titulo_obra}</div>
                <div class="event-title">${evento.partida}</div>
                ${concepto}
                ${notes}
            </div>
        `;
    }

    // Funciones de drag & drop
    let currentDraggedElement = null;
    let currentDraggedType = null; // 'tarea' o 'event'
    let currentDraggedData = null;

    function handleEventDragStart(e) {
        currentDraggedElement = e.target;
        currentDraggedType = 'event';
        currentDraggedData = {
            eventId: e.target.dataset.eventId,
            empleadoId: e.target.dataset.empleadoId
        };
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    }

    function handleEventDragEnd(e) {
        e.target.classList.remove('dragging');
        currentDraggedElement = null;
        currentDraggedType = null;
        currentDraggedData = null;
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    async function handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');

        const empleadoId = e.currentTarget.dataset.empleadoId;
        const fecha = e.currentTarget.dataset.fecha;

        if (currentDraggedType === 'tarea') {
            // Drop de tarea nueva desde el sidebar
            const tareaId = currentDraggedData.tareaId;
            const tarea = tareasData.find(t => t.id == tareaId);

            if (!tarea) return;

            // Guardar asignación para el empleado de la celda
            showLoading(true);
            await saveAsignacion(tarea, empleadoId, fecha);
            await loadAsignaciones();
            renderCalendar();
            showLoading(false);

        } else if (currentDraggedType === 'event') {
            // Mover evento existente
            const eventId = currentDraggedData.eventId;

            showLoading(true);
            await updateEventDate(eventId, empleadoId, fecha);
            await loadAsignaciones();
            renderCalendar();
            showLoading(false);
        }
    }

    async function updateEventDate(eventId, newEmpleadoId, newFecha) {
        try {
            const formData = new FormData();
            formData.append('id', eventId);
            formData.append('id_empleado', newEmpleadoId);
            formData.append('fecha_inicio', newFecha);
            formData.append('fecha_fin', newFecha);

            const response = await fetch('../backend/calendario/updateAsignacion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            return result.success;
        } catch (error) {
            console.error('Error:', error);
            return false;
        }
    }

    async function deleteEventById(eventId) {
        if (!confirm('¿Eliminar esta asignación?')) return;

        showLoading(true);
        try {
            const formData = new FormData();
            formData.append('id', eventId);

            const response = await fetch('../backend/calendario/deleteAsignacion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                await loadAsignaciones();
                renderCalendar();
            } else {
                alert(result.error || 'Error al eliminar');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar la asignación');
        }
        showLoading(false);
    }

    // Utilidades de fechas
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function formatDateLabel(date) {
        const day = date.getDate();
        const month = date.toLocaleString('es-ES', { month: 'short' });
        return `${day} ${month}`;
    }

    function getDayName(date) {
        const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        return days[date.getDay()];
    }

    function isTodayDate(date) {
        const today = new Date();
        return date.getDate() === today.getDate() &&
               date.getMonth() === today.getMonth() &&
               date.getFullYear() === today.getFullYear();
    }

    function renderTareasSidebar() {
        const container = document.getElementById('tareasContainer');

        if (tareasData.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>No hay tareas MO disponibles</p>
                </div>
            `;
            return;
        }

        // Agrupar tareas por obra
        const tareasPorObra = {};
        tareasData.forEach(tarea => {
            if (!tareasPorObra[tarea.id_obra]) {
                tareasPorObra[tarea.id_obra] = {
                    titulo: tarea.titulo_obra,
                    color: tarea.color_obra,
                    tareas: []
                };
            }
            tareasPorObra[tarea.id_obra].tareas.push(tarea);
        });

        let html = '';
        Object.keys(tareasPorObra).forEach(idObra => {
            const grupo = tareasPorObra[idObra];
            html += `
                <div class="obra-group">
                    <div class="obra-header" style="border-left-color: ${grupo.color}">
                        <i class="bi bi-building" style="color: ${grupo.color}"></i>
                        <div class="obra-title">${grupo.titulo}</div>
                        <span class="obra-badge">${grupo.tareas.length}</span>
                    </div>
                    ${grupo.tareas.map(tarea => `
                        <div class="external-event"
                             draggable="true"
                             data-tarea-id="${tarea.id}"
                             data-presupuesto-id="${tarea.id_presupuesto}"
                             data-obra-id="${tarea.id_obra}"
                             style="border-left-color: ${tarea.color_obra}">
                            <div class="event-partida">${tarea.partida}</div>
                            <div class="event-concepto">${tarea.concepto}</div>
                            <div class="event-cantidad">${tarea.cantidad} ${tarea.simbolo_unidad}</div>
                        </div>
                    `).join('')}
                </div>
            `;
        });

        container.innerHTML = html;

        // Hacer draggable con HTML5 drag API
        container.querySelectorAll('.external-event').forEach(el => {
            el.addEventListener('dragstart', (e) => {
                currentDraggedType = 'tarea';
                currentDraggedData = {
                    tareaId: el.dataset.tareaId
                };
                el.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'copy';
            });

            el.addEventListener('dragend', (e) => {
                el.classList.remove('dragging');
            });
        });
    }

    function getIniciales(nombre) {
        const palabras = nombre.trim().split(' ');
        if (palabras.length === 1) {
            return palabras[0].substring(0, 2).toUpperCase();
        }
        return (palabras[0][0] + palabras[palabras.length - 1][0]).toUpperCase();
    }

    async function saveAsignacion(tarea, empleadoId, fechaInicio) {
        try {
            const formData = new FormData();
            formData.append('id_obra', tarea.id_obra);
            formData.append('id_presupuesto', tarea.id_presupuesto);
            formData.append('id_presupuestos_subpartidas', tarea.id);
            formData.append('id_empleado', empleadoId);
            formData.append('fecha_inicio', fechaInicio);

            const response = await fetch('../backend/calendario/saveAsignacion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                return true;
            } else {
                alert(result.error || 'Error al guardar la asignación');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar la asignación');
            return false;
        }
    }

    function showEventDetails(event) {
        currentEventId = event.id;

        document.getElementById('modalObra').textContent = event.titulo_obra;
        document.getElementById('modalTarea').textContent = `${event.partida} - ${event.concepto}`;
        document.getElementById('modalEmpleado').textContent = event.nombre_empleado;

        const fechaInicio = new Date(event.fecha_inicio).toLocaleDateString('es-ES');
        const fechaFin = new Date(event.fecha_fin).toLocaleDateString('es-ES');
        const fechaTexto = fechaInicio === fechaFin ? fechaInicio : `${fechaInicio} - ${fechaFin}`;

        document.getElementById('modalFechas').textContent = fechaTexto;
        document.getElementById('modalInfo').value = event.info || '';

        new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    async function saveNotes() {
        if (!currentEventId) return;

        const notes = document.getElementById('modalInfo').value;

        showLoading(true);
        try {
            const formData = new FormData();
            formData.append('id', currentEventId);
            formData.append('info', notes);

            const response = await fetch('../backend/calendario/updateNotes.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                await loadAsignaciones();
                renderCalendar();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            } else {
                alert(result.error || 'Error al guardar las notas');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar las notas');
        }
        showLoading(false);
    }

    async function deleteEvent() {
        if (!currentEventId) return;

        showLoading(true);
        try {
            const formData = new FormData();
            formData.append('id', currentEventId);

            const response = await fetch('../backend/calendario/deleteAsignacion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                await loadAsignaciones();
                renderCalendar();
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            } else {
                alert(result.error || 'Error al eliminar');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar la asignación');
        }
        showLoading(false);
    }

    async function handleObraFilterChange(e) {
        currentObraFilter = e.target.value;
        showLoading(true);
        await loadTareas();
        await loadAsignaciones();
        renderTareasSidebar();
        renderCalendar();
        showLoading(false);
    }

    async function handleEmpleadoFilterChange(e) {
        currentEmpleadoFilter = e.target.value;
        showLoading(true);
        await loadAsignaciones();
        renderCalendar();
        showLoading(false);
    }

    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.toggle('show', show);
    }
</script>

</body>
</html>
