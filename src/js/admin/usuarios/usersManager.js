/**
 * @file Manages user creation, editing, and status toggling in the admin panel.
 * @project app-reservas
 */

import { fetchUser, createUser, updateUser, toggleUserStatus as toggleStatus, fetchMe } from "./api.js";
import { notification } from "../../shared/components/toast.js";
import { confirmAction } from "../../shared/components/confirm-dialog.js";

const editUserModal = document.getElementById("editUserModal");
const editUserForm = document.getElementById("editUserForm");
const createUserForm = document.getElementById("createUserForm");
const createUserModal = document.getElementById("createUserModal");

const editServiciosContainer = document.getElementById("editServicesContainer");
const editAvatarContainer = document.getElementById("editAvatarContainer");
const editDescriptionContainer = document.getElementById("editDescriptionContainer");
const createServiciosContainer = document.getElementById("createServicesContainer");
const createAvatarContainer = document.getElementById("createAvatarContainer");
const createDescriptionContainer = document.getElementById("createDescriptionContainer");

let currentEditUserServices = [];
let currentUserCache = null;

/**
 * Toggles services, avatar and description containers visibility based on role.
 *
 * @param {string} role - Selected role.
 * @param {HTMLElement} servicesContainer - Services container element.
 * @param {HTMLElement} avatarContainer - Avatar container element.
 * @param {HTMLElement} descriptionContainer - Description container element.
 */
const toggleSpecialistFields = (role, servicesContainer, avatarContainer, descriptionContainer) => {
  const isSpecialist = role === "Especialista";
  servicesContainer.style.display = isSpecialist ? "block" : "none";
  avatarContainer.style.display = isSpecialist ? "block" : "none";
  descriptionContainer.style.display = isSpecialist ? "block" : "none";
};

/**
 * Fetches and displays user data in the edit modal.
 *
 * @param {string} userId - The ID of the user to edit.
 */
const editUser = async (userId) => {
  currentEditUserServices = [];

  try {
    const result = await fetchUser(userId);

    if (result.success) {
      const user = result.data;
      const form = editUserForm.elements;

      form.editUserId.value = user.id;
      form.editNombre.value = user.nombre;
      form.editApellidos.value = user.apellidos;
      form.editEmail.value = user.email;
      form.editTelefono.value = user.telefono || "";
      form.editRol.value = user.rol;
      // Set value of select instead of checkbox
      form.editActivo.value = user.activo;

      // Get current user to check if we are editing ourselves
      if (!currentUserCache) {
        const meResult = await fetchMe();
        if (meResult.success) {
          currentUserCache = meResult.data;
        }
      }

      const isSelf = currentUserCache && currentUserCache.id === user.id;
      const isAdmin = user.rol === "Admin";

      // Disable role and status changes only if editing oneself
      form.editRol.disabled = isAdmin && isSelf;
      form.editActivo.disabled = isAdmin && isSelf;

      toggleSpecialistFields(
        user.rol,
        editServiciosContainer,
        editAvatarContainer,
        editDescriptionContainer
      );

      if (user.rol === "Especialista") {
        currentEditUserServices = user.servicios || [];
        form.editDescripcion.value = user.descripcion || "";

        const checkboxes = document
          .getElementById("editServiciosCheckboxes")
          .querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((cb) => (cb.checked = false));

        currentEditUserServices.forEach((serviceId) => {
          const cb = document.getElementById(`editService${serviceId}`);
          if (cb) cb.checked = true;
        });
      } else {
        form.editDescripcion.value = "";
      }

      const modal = new bootstrap.Modal(editUserModal);
      modal.show();
    } else {
      notification("Error al cargar usuario: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
  }
};

/**
 * Handles document click events for edit user buttons.
 */
const handleDocumentClick = async (e) => {
  if (e.target.closest(".btn-edit-user")) {
    const userId = e.target.closest(".btn-edit-user").dataset.userId;
    editUser(userId);
  }

  // Delete/Toggle actions removed for safety, now handled via edit modal
};

/**
 * Handles create user form submission.
 */
const handleCreateUserFormSubmit = async (e) => {
  e.preventDefault();

  const form = e.target.elements;
  const password = form.createPassword.value;
  const passwordConfirm = form.createPasswordConfirm.value;

  if (password !== passwordConfirm) {
    notification("Las contraseñas no coinciden", "error");
    return;
  }

  const formData = new FormData();
  formData.append("nombre", form.createNombre.value);
  formData.append("apellidos", form.createApellidos.value);
  formData.append("email", form.createEmail.value);
  formData.append("telefono", form.createTelefono.value);
  formData.append("password", password);
  formData.append("rol", form.createRol.value);

  if (form.createRol.value === "Especialista") {
    const checkboxes = document
      .getElementById("createServiciosCheckboxes")
      .querySelectorAll("input[type=checkbox]:checked");
    const selectedIds = Array.from(checkboxes).map((cb) => cb.value);

    if (selectedIds.length === 0) {
      notification("Debes seleccionar al menos un servicio para el especialista", "warning");
      return;
    }

    selectedIds.forEach((id) => formData.append("servicios[]", id));
    formData.append("descripcion", form.createDescripcion.value);

    if (form.createAvatar.files.length > 0) {
      formData.append("avatar", form.createAvatar.files[0]);
    }
  }

  try {
    const result = await createUser(formData);

    if (result.success) {
      notification("Usuario creado correctamente", "success");
      bootstrap.Modal.getInstance(createUserModal).hide();
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification("Error: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
  }
};

/**
 * Handles edit user form submission.
 */
const handleEditUserFormSubmit = async (e) => {
  e.preventDefault();

  const form = e.target.elements;
  const userId = form.editUserId.value;
  const password = form.editPassword.value;
  const passwordConfirm = form.editPasswordConfirm.value;

  if (password && password !== passwordConfirm) {
    notification("Las contraseñas no coinciden", "error");
    return;
  }

  const formData = new FormData();
  formData.append("nombre", form.editNombre.value);
  formData.append("apellidos", form.editApellidos.value);
  formData.append("email", form.editEmail.value);
  formData.append("telefono", form.editTelefono.value);
  formData.append("rol", form.editRol.value);
  formData.append("activo", form.editActivo.value);

  if (password) {
    formData.append("password", password);
  }

  if (form.editRol.value === "Especialista") {
    const checkboxes = document
      .getElementById("editServiciosCheckboxes")
      .querySelectorAll("input[type=checkbox]:checked");
    const selectedIds = Array.from(checkboxes).map((cb) => cb.value);

    if (selectedIds.length === 0) {
      notification("Debes seleccionar al menos un servicio para el especialista", "warning");
      return;
    }

    selectedIds.forEach((id) => formData.append("servicios[]", id));
    formData.append("descripcion", form.editDescripcion.value);

    if (form.editAvatar.files.length > 0) {
      formData.append("avatar", form.editAvatar.files[0]);
    }
  }

  try {
    const result = await updateUser(userId, formData);

    if (result.success) {
      notification("Usuario actualizado correctamente", "success");
      bootstrap.Modal.getInstance(editUserModal).hide();
      setTimeout(() => globalThis.location.reload(), 1000);
    } else {
      notification("Error: " + result.error, "error");
    }
  } catch (error) {
    notification("Error: " + error.message, "error");
  }
};

/**
 * Handles create rol change to show/hide specialist fields.
 */
const handleCreateRolChange = () => {
  const form = createUserForm.elements;
  toggleSpecialistFields(
    form.createRol.value,
    createServiciosContainer,
    createAvatarContainer,
    createDescriptionContainer
  );
};

/**
 * Handles edit rol change to show/hide specialist fields.
 */
const handleEditRolChange = () => {
  const form = editUserForm.elements;
  toggleSpecialistFields(
    form.editRol.value,
    editServiciosContainer,
    editAvatarContainer,
    editDescriptionContainer
  );
};

document.addEventListener("click", handleDocumentClick);

if (createUserForm) {
  createUserForm.addEventListener("submit", handleCreateUserFormSubmit);
  if (createUserForm.elements.createRol) {
    createUserForm.elements.createRol.addEventListener("change", handleCreateRolChange);
  }
}

if (editUserForm) {
  editUserForm.addEventListener("submit", handleEditUserFormSubmit);
  if (editUserForm.elements.editRol) {
    editUserForm.elements.editRol.addEventListener("change", handleEditRolChange);
  }
}

if (createUserModal) {
  createUserModal.addEventListener("hidden.bs.modal", () => createUserForm.reset());
}

if (editUserModal) {
  editUserModal.addEventListener("hidden.bs.modal", () => {
    editUserForm.reset();
    editServiciosContainer.style.display = "none";
    editAvatarContainer.style.display = "none";
    editDescriptionContainer.style.display = "none";
  });
}
