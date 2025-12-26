// Manages service creation, editing, and status toggling in the admin panel

const editServiceIdInput = document.getElementById("editServiceId");
const editNombreServicioInput = document.getElementById("editNombreServicio");
const editDescripcionInput = document.getElementById("editDescripcion");
const editDuracionInput = document.getElementById("editDuracion");
const editPrecioInput = document.getElementById("editPrecio");
const editActivoInput = document.getElementById("editActivo");
const editServiceModal = document.getElementById("editServiceModal");
const editServiceForm = document.getElementById("editServiceForm");

const createNombreServicioInput = document.getElementById("createNombreServicio");
const createDescripcionInput = document.getElementById("createDescripcion");
const createDuracionInput = document.getElementById("createDuracion");
const createPrecioInput = document.getElementById("createPrecio");
const createServiceForm = document.getElementById("createServiceForm");
const createServiceModal = document.getElementById("createServiceModal");

/**
 * Fetches and displays service data in the edit modal.
 * @param {string} serviceId - The ID of the service to edit.
 */
const editService = (serviceId) => {
  fetch("/admin/api/services/" + serviceId)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        const service = result.data;
        editServiceIdInput.value = service.id;
        editNombreServicioInput.value = service.nombre_servicio;
        editDescripcionInput.value = service.descripcion;
        editDuracionInput.value = service.duracion_minutos;
        editPrecioInput.value = service.precio;
        editActivoInput.checked = service.activo;

        const modal = new bootstrap.Modal(editServiceModal);
        modal.show();
      } else {
        alert("Error al cargar servicio: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Toggles service active status.
 * @param {string} serviceId - The ID of the service to toggle.
 * @param {string} currentStatus - Current status (0 or 1).
 */
const toggleServiceStatus = (serviceId, currentStatus) => {
  const isActive = currentStatus === "1";
  const endpoint = isActive
    ? "/admin/api/services/" + serviceId + "/deactivate"
    : "/admin/api/services/" + serviceId + "/activate";

  fetch(endpoint, {
    method: "POST",
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        globalThis.location.reload();
      } else {
        alert("Error: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Handles document click events for edit and toggle status buttons.
 */
const handleDocumentClick = (e) => {
  if (e.target.closest(".btn-edit-service")) {
    const serviceId = e.target.closest(".btn-edit-service").dataset.serviceId;
    editService(serviceId);
  }

  if (e.target.closest(".btn-toggle-status")) {
    const badge = e.target.closest(".btn-toggle-status");
    const serviceId = badge.dataset.serviceId;
    const currentStatus = badge.dataset.currentStatus;
    toggleServiceStatus(serviceId, currentStatus);
  }
};

/**
 * Handles create service form submission.
 */
const handleCreateServiceFormSubmit = (e) => {
  e.preventDefault();

  const formData = {
    nombre_servicio: createNombreServicioInput.value,
    descripcion: createDescripcionInput.value,
    duracion_minutos: Number.parseInt(createDuracionInput.value),
    precio: Number.parseFloat(createPrecioInput.value),
  };

  fetch("/admin/api/services", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Servicio creado correctamente");
        bootstrap.Modal.getInstance(createServiceModal).hide();
        globalThis.location.reload();
      } else {
        alert("Error: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Handles edit service form submission.
 */
const handleEditServiceFormSubmit = (e) => {
  e.preventDefault();

  const serviceId = editServiceIdInput.value;

  const formData = {
    nombre_servicio: editNombreServicioInput.value,
    descripcion: editDescripcionInput.value,
    duracion_minutos: Number.parseInt(editDuracionInput.value),
    precio: Number.parseFloat(editPrecioInput.value),
    activo: editActivoInput.checked,
  };

  fetch("/admin/api/services/" + serviceId, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Servicio actualizado correctamente");
        bootstrap.Modal.getInstance(editServiceModal).hide();
        globalThis.location.reload();
      } else {
        alert("Error: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Handles create service modal hidden event to reset the form.
 */
const handleCreateServiceModalHidden = () => {
  createServiceForm.reset();
};

/**
 * Handles edit service modal hidden event to reset the form.
 */
const handleEditServiceModalHidden = () => {
  editServiceForm.reset();
};

document.addEventListener("click", handleDocumentClick);
createServiceForm.addEventListener("submit", handleCreateServiceFormSubmit);
editServiceForm.addEventListener("submit", handleEditServiceFormSubmit);
createServiceModal.addEventListener("hidden.bs.modal", handleCreateServiceModalHidden);
editServiceModal.addEventListener("hidden.bs.modal", handleEditServiceModalHidden);
