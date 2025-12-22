const editServiceIdInput = document.getElementById("editServiceId");
const editNombreServicioInput = document.getElementById("editNombreServicio");
const editDescripcionInput = document.getElementById("editDescripcion");
const editDuracionInput = document.getElementById("editDuracion");
const editPrecioInput = document.getElementById("editPrecio");
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
 * Deletes a service after confirmation.
 * @param {string} serviceId - The ID of the service to delete.
 * @param {string} serviceName - The name of the service to delete.
 */
const deleteService = (serviceId, serviceName) => {
  if (!confirm("¿Estás seguro de eliminar el servicio '" + serviceName + "'?")) {
    return;
  }

  fetch("/admin/api/services/" + serviceId, {
    method: "DELETE",
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Servicio eliminado correctamente");
        window.location.reload();
      } else {
        alert("Error al eliminar: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Handles document click events for edit and delete service buttons.
 */
const handleDocumentClick = (e) => {
  if (e.target.closest(".btn-edit-service")) {
    const serviceId = e.target.closest(".btn-edit-service").dataset.serviceId;
    editService(serviceId);
  }

  if (e.target.closest(".btn-delete-service")) {
    const button = e.target.closest(".btn-delete-service");
    const serviceId = button.dataset.serviceId;
    const serviceName = button.dataset.serviceName;
    deleteService(serviceId, serviceName);
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
    duracion_minutos: parseInt(createDuracionInput.value),
    precio: parseFloat(createPrecioInput.value),
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
        window.location.reload();
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
    duracion_minutos: parseInt(editDuracionInput.value),
    precio: parseFloat(editPrecioInput.value),
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
        window.location.reload();
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
