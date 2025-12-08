document.getElementById("createUserForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  // Limpiar errores previos
  this.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));

  const formData = {
    nombre: document.getElementById("createNombre").value,
    apellidos: document.getElementById("createApellidos").value,
    email: document.getElementById("createEmail").value,
    telefono: document.getElementById("createTelefono").value,
    password: document.getElementById("createPassword").value,
    "password-confirm": document.getElementById("createPasswordConfirm").value,
    rol: document.getElementById("createRol").value,
  };

  // Validación de contraseñas
  if (formData.password !== formData["password-confirm"]) {
    const confirmInput = document.getElementById("createPasswordConfirm");
    confirmInput.classList.add("is-invalid");
    confirmInput.nextElementSibling.textContent = "Las contraseñas no coinciden";
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
      // Cerrar modal
      bootstrap.Modal.getInstance(document.getElementById("createUserModal")).hide();
      // Limpiar formulario
      this.reset();
      // Recargar página
      window.location.reload();
    } else {
      alert("Error: " + (result.error || "No se pudo crear el usuario"));
    }
  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
});

// Limpiar formulario al cerrar modal
document.getElementById("createUserModal").addEventListener("hidden.bs.modal", function () {
  const form = document.getElementById("createUserForm");
  form.reset();
  form.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
});
