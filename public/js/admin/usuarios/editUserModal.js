document.getElementById("editUserForm").addEventListener("submit", async function (e) {
  e.preventDefault();

  // Limpiar errores previos
  this.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));

  const userId = document.getElementById("editUserId").value;
  const password = document.getElementById("editPassword").value;
  const passwordConfirm = document.getElementById("editPasswordConfirm").value;

  // Validación de contraseñas si se está cambiando
  if (password || passwordConfirm) {
    if (password !== passwordConfirm) {
      const confirmInput = document.getElementById("editPasswordConfirm");
      confirmInput.classList.add("is-invalid");
      confirmInput.nextElementSibling.textContent = "Las contraseñas no coinciden";
      return;
    }
  }

  const formData = {
    nombre: document.getElementById("editNombre").value,
    apellidos: document.getElementById("editApellidos").value,
    email: document.getElementById("editEmail").value,
    telefono: document.getElementById("editTelefono").value,
    rol: document.getElementById("editRol").value,
  };

  // Solo incluir password si se está cambiando
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
      // Cerrar modal
      bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
      // Limpiar formulario
      this.reset();
      // Recargar página
      window.location.reload();
    } else {
      alert("Error: " + (result.error || "No se pudo actualizar el usuario"));
    }
  } catch (error) {
    alert("Error de conexión: " + error.message);
  }
});

// Limpiar formulario al cerrar modal
document.getElementById("editUserModal").addEventListener("hidden.bs.modal", function () {
  const form = document.getElementById("editUserForm");
  form.reset();
  form.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
});
