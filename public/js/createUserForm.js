import { userSchema } from "./userSchema.js";
import { getFormData, validateData, showErrors, clearErrors } from "./validation-utils.js";

const validateCreateUserForm = () => {
  const form = document.getElementById("create-user-form");

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
      const response = await fetch("/admin/api/users", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.success) {
        const modal = bootstrap.Modal.getInstance(document.getElementById("createUserModal"));
        modal.hide();
        form.reset();
        clearErrors();
        if (window.reloadUsersTable) window.reloadUsersTable();
      } else {
        alert("Error: " + (result.error || "No se pudo crear el usuario"));
      }
    } catch (error) {
      alert("Error de conexión");
    }
  });
};

document.addEventListener("DOMContentLoaded", validateCreateUserForm);
