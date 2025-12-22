const editUserForm = document.getElementById("editUserForm");
const editUserModal = document.getElementById("editUserModal");
const editUserId = document.getElementById("editUserId");
const editPassword = document.getElementById("editPassword");
const editPasswordConfirm = document.getElementById("editPasswordConfirm");
const editNombre = document.getElementById("editNombre");
const editApellidos = document.getElementById("editApellidos");
const editEmail = document.getElementById("editEmail");
const editTelefono = document.getElementById("editTelefono");
const editRol = document.getElementById("editRol");

/**
 * Handles the submission of the user edit form.
 * Validates data, makes a PUT request to the API, and reloads the page on success.
 * Password is optional and only included if specified.
 *
 * @async
 * @param {SubmitEvent} e - Form submission event
 * @returns {Promise<void>}
 * @throws {Error} If there is a connection error with the API
 */
const handleEditSubmit = async (e) => {
  e.preventDefault();

  e.target.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));

  const userId = editUserId.value;
  const password = editPassword.value;
  const passwordConfirm = editPasswordConfirm.value;

  if (password || passwordConfirm) {
    if (password !== passwordConfirm) {
      editPasswordConfirm.classList.add("is-invalid");
      editPasswordConfirm.nextElementSibling.textContent = "Las contraseñas no coinciden";
      return;
    }
  }

  const formData = {
    nombre: editNombre.value,
    apellidos: editApellidos.value,
    email: editEmail.value,
    telefono: editTelefono.value,
    rol: editRol.value,
  };

  if (password) {
    formData.password = password;
  }

  try {
    const response = await fetch(`/admin/api/users/${userId}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });

    const result = await response.json();

    if (result.success) {
      bootstrap.Modal.getInstance(editUserModal).hide();
      editUserForm.reset();
      window.location.reload();
    } else {
      alert("Error: " + (result.error || "No se pudo actualizar el usuario"));
    }
  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
};

/**
 * Cleans the form and removes error messages when the modal closes.
 * Restores the form to its initial state.
 *
 * @returns {void}
 */
const handleEditModalClose = () => {
  editUserForm.reset();
  editUserForm.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
};

editUserForm.addEventListener("submit", handleEditSubmit);
editUserModal.addEventListener("hidden.bs.modal", handleEditModalClose);
