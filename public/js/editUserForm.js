import { userSchema } from "./userSchema.js";
import { getFormData, validateData, showErrors, clearErrors } from "./validation-utils.js";

let currentUserId = null;

window.openEditUserModal = function (userId, userData = null) {
  currentUserId = userId;
  clearErrors();

  if (userData) {
    document.getElementById("edit-user-id").value = userData.id;
    document.getElementById("edit-nombre").value = userData.nombre;
    document.getElementById("edit-apellidos").value = userData.apellidos;
    document.getElementById("edit-email").value = userData.email;
    document.getElementById("edit-telefono").value = userData.telefono || "";
    document.getElementById("edit-rol").value = userData.rol;

    document.getElementById("edit-password").value = "";
    document.getElementById("edit-password-confirm").value = "";

    const modal = new bootstrap.Modal(document.getElementById("editUserModal"));
    modal.show();
    return;
  }

  fetch(`/admin/api/users/${userId}`)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        const user = result.data;

        document.getElementById("edit-user-id").value = user.id;
        document.getElementById("edit-nombre").value = user.nombre;
        document.getElementById("edit-apellidos").value = user.apellidos;
        document.getElementById("edit-email").value = user.email;
        document.getElementById("edit-telefono").value = user.telefono || "";
        document.getElementById("edit-rol").value = user.rol;

        document.getElementById("edit-password").value = "";
        document.getElementById("edit-password-confirm").value = "";

        const modal = new bootstrap.Modal(document.getElementById("editUserModal"));
        modal.show();
      } else {
        alert("Error al cargar usuario");
      }
    })
    .catch((error) => {
      alert("Error de conexión");
    });
};

const validateEditUserForm = () => {
  const form = document.getElementById("edit-user-form");

  if (!form) {
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const data = getFormData(form);
    const { isValid, errors } = await validateData(userSchema, data);

    if (!isValid) {
      showErrors(errors);
      return;
    }

    try {
      const userId = document.getElementById("edit-user-id").value;
      const response = await fetch(`/admin/api/users/${userId}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.success) {
        const modal = bootstrap.Modal.getInstance(document.getElementById("editUserModal"));
        modal.hide();
        form.reset();
        clearErrors();
        if (window.reloadUsersTable) window.reloadUsersTable();
      } else {
        alert("Error: " + (result.error || "No se pudo actualizar el usuario"));
      }
    } catch (error) {
      alert("Error de conexión");
    }
  });
};

document.addEventListener("DOMContentLoaded", validateEditUserForm);
