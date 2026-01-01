/**
 * @file Manages service creation, editing, and status toggling in the admin panel.
 * @project app-reservas
 */

import {
  fetchService,
  createService,
  updateService,
  activateService,
  deactivateService,
} from "./api.js";
import { notification } from "../../shared/components/toast.js";

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
      notification("Error al cargar servicio: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
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
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification("Error: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
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
      notification("Servicio creado correctamente", "success");
      bootstrap.Modal.getInstance(createServiceModal).hide();
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification("Error: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
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
      notification("Servicio actualizado correctamente", "success");
      bootstrap.Modal.getInstance(editServiceModal).hide();
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification("Error: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
  }
};

document.addEventListener("click", handleDocumentClick);

if (createServiceForm) {
  createServiceForm.addEventListener("submit", handleCreateServiceFormSubmit);
}

if (editServiceForm) {
  editServiceForm.addEventListener("submit", handleEditServiceFormSubmit);
}

if (createServiceModal) {
  createServiceModal.addEventListener("hidden.bs.modal", () => createServiceForm.reset());
}

if (editServiceModal) {
  editServiceModal.addEventListener("hidden.bs.modal", () => editServiceForm.reset());
}
