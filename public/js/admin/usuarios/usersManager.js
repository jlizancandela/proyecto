const editUserIdInput = document.getElementById("editUserId");
const editNombreInput = document.getElementById("editNombre");
const editApellidosInput = document.getElementById("editApellidos");
const editEmailInput = document.getElementById("editEmail");
const editTelefonoInput = document.getElementById("editTelefono");
const editRolInput = document.getElementById("editRol");
const editServiciosSelect = document.getElementById("editServicios");
const editServiciosContainer = document.getElementById("editServicesContainer");
const editAvatarContainer = document.getElementById("editAvatarContainer");
const editActivoCheckbox = document.getElementById("editActivo");
const editPasswordInput = document.getElementById("editPassword");
const editPasswordConfirmInput = document.getElementById("editPasswordConfirm");
const editUserModal = document.getElementById("editUserModal");
const editUserForm = document.getElementById("editUserForm");

const createNombreInput = document.getElementById("createNombre");
const createApellidosInput = document.getElementById("createApellidos");
const createEmailInput = document.getElementById("createEmail");
const createTelefonoInput = document.getElementById("createTelefono");
const createPasswordInput = document.getElementById("createPassword");
const createPasswordConfirmInput = document.getElementById("createPasswordConfirm");
const createRolInput = document.getElementById("createRol");
const createServiciosSelect = document.getElementById("createServicios");
const createServiciosContainer = document.getElementById("createServicesContainer");
const createAvatarContainer = document.getElementById("createAvatarContainer");
const createUserForm = document.getElementById("createUserForm");
const createUserModal = document.getElementById("createUserModal");

let availableServices = [];

/**
 * Loads available services from API
 */
const loadServices = () => {
  fetch("/api/services")
    .then((response) => response.json())
    .then((services) => {
      availableServices = services;
      populateServicesSelect(createServiciosSelect, []);
      populateServicesSelect(editServiciosSelect, []);
    })
    .catch((error) => {
      console.error("Error loading services:", error);
    });
};

/**
 * Populates a services select element
 * @param {HTMLSelectElement} selectElement - The select element to populate
 * @param {array} selectedIds - Array of selected service IDs
 */
const populateServicesSelect = (selectElement, selectedIds = []) => {
  selectElement.innerHTML = "";
  availableServices.forEach((service) => {
    const option = document.createElement("option");
    option.value = service.id;
    option.textContent = service.nombre;
    if (selectedIds.includes(service.id)) {
      option.selected = true;
    }
    selectElement.appendChild(option);
  });
};

/**
 * Toggles services and avatar containers visibility based on role
 * @param {string} role - Selected role
 * @param {HTMLElement} servicesContainer - Services container element
 * @param {HTMLElement} avatarContainer - Avatar container element
 */
const toggleSpecialistFields = (role, servicesContainer, avatarContainer) => {
  if (role === "Especialista") {
    servicesContainer.style.display = "block";
    avatarContainer.style.display = "block";
  } else {
    servicesContainer.style.display = "none";
    avatarContainer.style.display = "none";
  }
};

/**
 * Fetches and displays user data in the edit modal.
 * @param {string} userId - The ID of the user to edit.
 */
const editUser = (userId) => {
  fetch("/admin/api/users/" + userId)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        const user = result.data;
        editUserIdInput.value = user.id;
        editNombreInput.value = user.nombre;
        editApellidosInput.value = user.apellidos;
        editEmailInput.value = user.email;
        editTelefonoInput.value = user.telefono || "";
        editRolInput.value = user.rol;
        editActivoCheckbox.checked = user.activo;

        // Mostrar/ocultar servicios y avatar según rol
        toggleSpecialistFields(user.rol, editServiciosContainer, editAvatarContainer);

        // Cargar servicios si es especialista
        if (user.rol === "Especialista") {
          const selectedServices = user.servicios || [];
          populateServicesSelect(editServiciosSelect, selectedServices);
        }

        const modal = new bootstrap.Modal(editUserModal);
        modal.show();
      } else {
        alert("Error al cargar usuario: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
};

/**
 * Deletes a user after confirmation.
 * @param {string} userId - The ID of the user to delete.
 * @param {string} userName - The name of the user to delete.
 */
const deleteUser = (userId, userName) => {
  if (!confirm("¿Estás seguro de eliminar a " + userName + "?")) {
    return;
  }

  fetch("/admin/api/users/" + userId, {
    method: "DELETE",
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Usuario eliminado correctamente");
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
 * Handles document click events for edit and delete user buttons.
 */
const handleDocumentClick = (e) => {
  if (e.target.closest(".btn-edit-user")) {
    const userId = e.target.closest(".btn-edit-user").dataset.userId;
    editUser(userId);
  }

  if (e.target.closest(".btn-delete-user")) {
    const button = e.target.closest(".btn-delete-user");
    const userId = button.dataset.userId;
    const userName = button.dataset.userName;
    deleteUser(userId, userName);
  }
};

/**
 * Handles create user form submission.
 */
const handleCreateUserFormSubmit = (e) => {
  e.preventDefault();

  const password = createPasswordInput.value;
  const passwordConfirm = createPasswordConfirmInput.value;

  if (password !== passwordConfirm) {
    alert("Las contraseñas no coinciden");
    return;
  }

  const formData = {
    nombre: createNombreInput.value,
    apellidos: createApellidosInput.value,
    email: createEmailInput.value,
    telefono: createTelefonoInput.value,
    password: password,
    rol: createRolInput.value,
  };

  // Añadir servicios si es especialista
  if (createRolInput.value === "Especialista") {
    const selectedOptions = Array.from(createServiciosSelect.selectedOptions);
    formData.servicios = selectedOptions.map((option) => parseInt(option.value));

    if (formData.servicios.length === 0) {
      alert("Debes seleccionar al menos un servicio para el especialista");
      return;
    }
  }

  fetch("/admin/api/users", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Usuario creado correctamente");
        bootstrap.Modal.getInstance(createUserModal).hide();
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
 * Handles edit user form submission.
 */
const handleEditUserFormSubmit = (e) => {
  e.preventDefault();

  const userId = editUserIdInput.value;
  const password = editPasswordInput.value;
  const passwordConfirm = editPasswordConfirmInput.value;

  if (password && password !== passwordConfirm) {
    alert("Las contraseñas no coinciden");
    return;
  }

  const formData = {
    nombre: editNombreInput.value,
    apellidos: editApellidosInput.value,
    email: editEmailInput.value,
    telefono: editTelefonoInput.value,
    rol: editRolInput.value,
    activo: editActivoCheckbox.checked,
  };

  // Añadir servicios si es especialista
  if (editRolInput.value === "Especialista") {
    const selectedOptions = Array.from(editServiciosSelect.selectedOptions);
    formData.servicios = selectedOptions.map((option) => parseInt(option.value));

    if (formData.servicios.length === 0) {
      alert("Debes seleccionar al menos un servicio para el especialista");
      return;
    }
  }

  if (password) {
    formData.password = password;
  }

  fetch("/admin/api/users/" + userId, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Usuario actualizado correctamente");
        bootstrap.Modal.getInstance(editUserModal).hide();
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
 * Handles create user modal hidden event to reset the form.
 */
const handleCreateUserModalHidden = () => {
  createUserForm.reset();
};

/**
 * Handles edit user modal hidden event to reset the form.
 */
const handleEditUserModalHidden = () => {
  editUserForm.reset();
  editServiciosContainer.style.display = "none";
  editAvatarContainer.style.display = "none";
};

/**
 * Handles create rol change to show/hide specialist fields
 */
const handleCreateRolChange = () => {
  toggleSpecialistFields(createRolInput.value, createServiciosContainer, createAvatarContainer);
};

/**
 * Handles edit rol change to show/hide specialist fields
 */
const handleEditRolChange = () => {
  toggleSpecialistFields(editRolInput.value, editServiciosContainer, editAvatarContainer);
};

document.addEventListener("click", handleDocumentClick);
createUserForm.addEventListener("submit", handleCreateUserFormSubmit);
editUserForm.addEventListener("submit", handleEditUserFormSubmit);
createUserModal.addEventListener("hidden.bs.modal", handleCreateUserModalHidden);
editUserModal.addEventListener("hidden.bs.modal", handleEditUserModalHidden);
createRolInput.addEventListener("change", handleCreateRolChange);
editRolInput.addEventListener("change", handleEditRolChange);

// Cargar servicios al iniciar
loadServices();
