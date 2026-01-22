/**
 * Specialist bookings management.
 * Handles status updates and filtering for the specialist panel.
 */

import { confirmAction } from "../shared/components/confirm-dialog.js";
import { notification } from "../shared/components/toast.js";

document.addEventListener('DOMContentLoaded', () => {
    // Helper to update status
    const updateStatus = async (id, status) => {
        const confirmed = await confirmAction(`¿Estás seguro de marcar esta reserva como ${status}?`);
        if (!confirmed) return;

        try {
            const response = await fetch(`/specialist/api/reservas/${id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: status })
            });

            const result = await response.json();

            if (result.success) {
                notification(`Reserva marcada como ${status}`, "success");
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                notification(result.error || 'No se pudo actualizar la reserva', "error");
            }
        } catch (error) {
            console.error('Error:', error);
            notification('Error crítico al conectar con el servidor', "error");
        }
    };

    // Event Delegation for status buttons
    document.querySelectorAll('.btn-complete').forEach(btn => {
        btn.addEventListener('click', () => updateStatus(btn.dataset.id, 'Completada'));
    });

    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', () => updateStatus(btn.dataset.id, 'Cancelada'));
    });

    // Filters Logic
    const btnApplyFilters = document.getElementById('btnApplyFilters');
    const btnClearFilters = document.getElementById('btnClearFilters');

    if (btnApplyFilters) {
        btnApplyFilters.addEventListener('click', () => {
            const estado = document.getElementById('filterEstado').value;
            const cliente = document.getElementById('filterCliente').value;
            const desde = document.getElementById('filterFechaDesde').value;
            const hasta = document.getElementById('filterFechaHasta').value;

            const params = new URLSearchParams();
            if (estado) params.set('estado', estado);
            if (cliente) params.set('cliente', cliente);
            if (desde) params.set('fecha_desde', desde);
            if (hasta) params.set('fecha_hasta', hasta);

            window.location.href = `/specialist/bookings?${params.toString()}`;
        });
    }

    if (btnClearFilters) {
        btnClearFilters.addEventListener('click', () => {
            window.location.href = '/specialist/bookings';
        });
    }
});
