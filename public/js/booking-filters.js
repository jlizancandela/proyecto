// Filtros de reservas
function selectEstado(estado) {
  // Actualizar el input hidden
  document.getElementById("estadoInput").value = estado;

  // Enviar el formulario
  document.getElementById("filterForm").submit();
}

function clearFecha() {
  // Limpiar el campo de fecha
  document.getElementById("fecha").value = "";

  // Enviar el formulario
  document.getElementById("filterForm").submit();
}
