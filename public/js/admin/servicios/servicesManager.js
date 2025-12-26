// Manages service creation, editing, and status toggling in the admin panel.

import {
  fetchService,
  createService,
  updateService,
  activateService,
  deactivateService,
} from "./api.js";

const editServiceModal = document.getElementById("editServiceModal");
const editServiceForm = document.getElementById("editServiceForm");
const createServiceForm = document.getElementById("createServiceForm");
const createServiceModal = document.getElementById("createServiceModal");

/**
 * Fetches and displays service data in the edit modal.
 *
 * @param {string} serviceId - The ID of the service to edit.
 */
const editService = async (serviceId) => {
  try {
    const result = await fetchService(serviceId);

    if (result.success) {
      const service = result.data;
      const form = editServiceForm.elements;

      form.editServiceId.value = service.id;
      form.editNombreServicio.value = service.nombre_servicio;
      form.editDescripcion.value = service.descripcion;
      form.editDuracion.value = service.duracion_minutos;
      form.editPrecio.value = service.precio;
      form.editActivo.checked = service.activo;

      const modal = new bootstrap.Modal(editServiceModal);
      modal.show();
    } else {
      alert("Error al cargar servicio: " + result.error);
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
};

/**
 * Toggles service active status.
 *
 * @param {string} serviceId - The ID of the service to toggle.
 * @param {string} currentStatus - Current status (0 or 1).
 */
const toggleServiceStatus = async (serviceId, currentStatus) => {
  const isActive = currentStatus === "1";

  try {
    const result = isActive ? await deactivateService(serviceId) : await activateService(serviceId);

    if (result.success) {
      globalThis.location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
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
const handleCreateServiceFormSubmit = async (e) => {
  e.preventDefault();

  const form = e.target.elements;

  const formData = {
    nombre_servicio: form.createNombreServicio.value,
    descripcion: form.createDescripcion.value,
    duracion_minutos: Number.parseInt(form.createDuracion.value),
    precio: Number.parseFloat(form.createPrecio.value),
  };

  try {
    const result = await createService(formData);

    if (result.success) {
      alert("Servicio creado correctamente");
      bootstrap.Modal.getInstance(createServiceModal).hide();
      globalThis.location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
};

/**
 * Handles edit service form submission.
 */
const handleEditServiceFormSubmit = async (e) => {
  e.preventDefault();

  const form = e.target.elements;

  const formData = {
    nombre_servicio: form.editNombreServicio.value,
    descripcion: form.editDescripcion.value,
    duracion_minutos: Number.parseInt(form.editDuracion.value),
    precio: Number.parseFloat(form.editPrecio.value),
    activo: form.editActivo.checked,
  };

  try {
    const result = await updateService(form.editServiceId.value, formData);

    if (result.success) {
      alert("Servicio actualizado correctamente");
      bootstrap.Modal.getInstance(editServiceModal).hide();
      globalThis.location.reload();
    } else {
      alert("Error: " + result.error);
    }
  } catch (error) {
    alert("Error: " + error.message);
  }
};

document.addEventListener("click", handleDocumentClick);
createServiceForm.addEventListener("submit", handleCreateServiceFormSubmit);
editServiceForm.addEventListener("submit", handleEditServiceFormSubmit);
createServiceModal.addEventListener("hidden.bs.modal", () => createServiceForm.reset());
editServiceModal.addEventListener("hidden.bs.modal", () => editServiceForm.reset());
