const createUserForm = document.getElementById("createUserForm");
const createUserModal = document.getElementById("createUserModal");
const createNombre = document.getElementById("createNombre");
const createApellidos = document.getElementById("createApellidos");
const createEmail = document.getElementById("createEmail");
const createTelefono = document.getElementById("createTelefono");
const createPassword = document.getElementById("createPassword");
const createPasswordConfirm = document.getElementById("createPasswordConfirm");
const createRol = document.getElementById("createRol");

/**
 * Handles user creation form submission.
 * Validates the data, makes a POST request to the API and reloads the page on success.
 *
 * @async
 * @param {SubmitEvent} e - Form submit event
 * @returns {Promise<void>}
 * @throws {Error} If there is a connection error with the API
 */
const handleCreateSubmit = async (e) => {
  e.preventDefault();

  e.target.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));

  const formData = {
    nombre: createNombre.value,
    apellidos: createApellidos.value,
    email: createEmail.value,
    telefono: createTelefono.value,
    password: createPassword.value,
    "password-confirm": createPasswordConfirm.value,
    rol: createRol.value,
  };

  if (formData.password !== formData["password-confirm"]) {
    createPasswordConfirm.classList.add("is-invalid");
    createPasswordConfirm.nextElementSibling.textContent = "Las contraseñas no coinciden";
    return;
  }

  try {
    const response = await fetch("/admin/api/users", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(formData),
    });

    const result = await response.json();

    if (result.success) {
      bootstrap.Modal.getInstance(createUserModal).hide();
      createUserForm.reset();
      window.location.reload();
    } else {
      alert("Error: " + (result.error || "No se pudo crear el usuario"));
    }
  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
};

/**
 * Clears the form and removes error messages when the modal is closed.
 * Restores the form to its initial state.
 *
 * @returns {void}
 */
const handleCreateModalClose = () => {
  createUserForm.reset();
  createUserForm.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
};

createUserForm.addEventListener("submit", handleCreateSubmit);
createUserModal.addEventListener("hidden.bs.modal", handleCreateModalClose);
