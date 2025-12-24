const editUserIdInput = document.getElementById("editUserId");
const editNombreInput = document.getElementById("editNombre");
const editApellidosInput = document.getElementById("editApellidos");
const editEmailInput = document.getElementById("editEmail");
const editTelefonoInput = document.getElementById("editTelefono");
const editRolInput = document.getElementById("editRol");
const editServiciosSelect = document.getElementById("editServicios");
const editServiciosContainer = document.getElementById("editServicesContainer");
const editAvatarContainer = document.getElementById("editAvatarContainer");
const editDescriptionContainer = document.getElementById("editDescriptionContainer");
const editAvatarInput = document.getElementById("editAvatar");
const editDescripcionInput = document.getElementById("editDescripcion");
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
const createDescriptionContainer = document.getElementById("createDescriptionContainer");
const createAvatarInput = document.getElementById("createAvatar");
const createDescripcionInput = document.getElementById("createDescripcion");
const createUserForm = document.getElementById("createUserForm");
const createUserModal = document.getElementById("createUserModal");

let availableServices = [];

// Variable para almacenar servicios del usuario actual en edición (para manejar condiciones de carrera)
let currentEditUserServices = [];

/**
 * Loads available services from API
 */
const loadServices = () => {
  fetch("/api/services")
    .then((response) => response.json())
    .then((data) => {
      availableServices = data.success ? data.servicios : [];
      populateServicesSelect(createServiciosSelect, []);
      // Usar los servicios guardados si ya se abrió el modal de edición
      populateServicesSelect(editServiciosSelect, currentEditUserServices);
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
    // Convertir IDs a string para comparación robusta
    const selectedIdsString = selectedIds.map(String);
    if (selectedIdsString.includes(String(service.id))) {
      option.selected = true;
    }
    selectElement.appendChild(option);
  });
};

/**
 * Toggles services, avatar and description containers visibility based on role
 * @param {string} role - Selected role
 * @param {HTMLElement} servicesContainer - Services container element
 * @param {HTMLElement} avatarContainer - Avatar container element
 * @param {HTMLElement} descriptionContainer - Description container element
 */
const toggleSpecialistFields = (role, servicesContainer, avatarContainer, descriptionContainer) => {
  if (role === "Especialista") {
    servicesContainer.style.display = "block";
    avatarContainer.style.display = "block";
    descriptionContainer.style.display = "block";
  } else {
    servicesContainer.style.display = "none";
    avatarContainer.style.display = "none";
    descriptionContainer.style.display = "none";
  }
};

/**
 * Fetches and displays user data in the edit modal.
 * @param {string} userId - The ID of the user to edit.
 */
const editUser = (userId) => {
  // Resetear servicios actuales antes de cargar usuario
  currentEditUserServices = [];

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

        // Proteger admin: deshabilitar cambio de rol y estado si es Admin
        if (user.rol === "Admin") {
          editRolInput.disabled = true;
          editActivoCheckbox.disabled = true;
        } else {
          editRolInput.disabled = false;
          editActivoCheckbox.disabled = false;
        }

        // Mostrar/ocultar servicios y campos de especialista según rol
        toggleSpecialistFields(
          user.rol,
          editServiciosContainer,
          editAvatarContainer,
          editDescriptionContainer
        );

        // Cargar datos de especialista
        if (user.rol === "Especialista") {
          currentEditUserServices = user.servicios || [];
          editDescripcionInput.value = user.descripcion || "";

          // Si los servicios disponibles no se han cargado aún, cargarlos primero
          if (availableServices.length === 0) {
            fetch("/api/services")
              .then((response) => response.json())
              .then((data) => {
                availableServices = data.success ? data.servicios : [];
                populateServicesSelect(editServiciosSelect, currentEditUserServices);
              })
              .catch((error) => {
                console.error("Error loading services:", error);
              });
          } else {
            populateServicesSelect(editServiciosSelect, currentEditUserServices);
          }
        } else {
          editDescripcionInput.value = "";
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

  const formData = new FormData();
  formData.append("nombre", createNombreInput.value);
  formData.append("apellidos", createApellidosInput.value);
  formData.append("email", createEmailInput.value);
  formData.append("telefono", createTelefonoInput.value);
  formData.append("password", password);
  formData.append("rol", createRolInput.value);

  // Añadir servicios y descripción si es especialista
  if (createRolInput.value === "Especialista") {
    const selectedOptions = Array.from(createServiciosSelect.selectedOptions);
    selectedOptions.forEach((option) => {
      formData.append("servicios[]", option.value);
    });

    if (selectedOptions.length === 0) {
      alert("Debes seleccionar al menos un servicio para el especialista");
      return;
    }

    formData.append("descripcion", createDescripcionInput.value);

    // Añadir avatar si existe
    if (createAvatarInput.files.length > 0) {
      formData.append("avatar", createAvatarInput.files[0]);
    }
  }

  fetch("/admin/api/users", {
    method: "POST",
    body: formData,
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

  const formData = new FormData();
  formData.append("nombre", editNombreInput.value);
  formData.append("apellidos", editApellidosInput.value);
  formData.append("email", editEmailInput.value);
  formData.append("telefono", editTelefonoInput.value);
  formData.append("rol", editRolInput.value);
  formData.append("activo", editActivoCheckbox.checked ? "1" : "0");

  if (password) {
    formData.append("password", password);
  }

  // Añadir servicios y descripción si es especialista
  if (editRolInput.value === "Especialista") {
    const selectedOptions = Array.from(editServiciosSelect.selectedOptions);
    selectedOptions.forEach((option) => {
      formData.append("servicios[]", option.value);
    });

    if (selectedOptions.length === 0) {
      alert("Debes seleccionar al menos un servicio para el especialista");
      return;
    }

    formData.append("descripcion", editDescripcionInput.value);

    // Añadir avatar si existe (si se seleccionó uno nuevo)
    if (editAvatarInput.files.length > 0) {
      formData.append("avatar", editAvatarInput.files[0]);
    }
  }

  fetch("/admin/api/users/" + userId, {
    method: "POST", // Change to POST for file upload support (method spoofing used if API requires PUT, but PHP native file upload generally works better with POST properly handled, or we keep PUT if we parse input stream manually, but FormData with PUT is tricky in PHP. Best practice: POST usually used for files or spoof method)
    // Actually, PHP doesn't populate $_FILES on PUT requests easily.
    // Usually one uses POST with _method=PUT.
    // Let's use POST but send _method field.
    body: formData,
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
  editDescriptionContainer.style.display = "none";
};

/**
 * Handles create rol change to show/hide specialist fields
 */
const handleCreateRolChange = () => {
  toggleSpecialistFields(
    createRolInput.value,
    createServiciosContainer,
    createAvatarContainer,
    createDescriptionContainer
  );
};

/**
 * Handles edit rol change to show/hide specialist fields
 */
const handleEditRolChange = () => {
  toggleSpecialistFields(
    editRolInput.value,
    editServiciosContainer,
    editAvatarContainer,
    editDescriptionContainer
  );
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
