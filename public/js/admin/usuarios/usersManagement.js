// Función para editar usuario (carga datos con fetch)
async function editUser(userId) {
  try {
    const response = await fetch(`/admin/api/users/${userId}`);
    const result = await response.json();

    if (result.success) {
      const user = result.data;
      // Rellenar el modal de edición
      document.getElementById("editUserId").value = user.id;
      document.getElementById("editNombre").value = user.nombre;
      document.getElementById("editApellidos").value = user.apellidos;
      document.getElementById("editEmail").value = user.email;
      document.getElementById("editTelefono").value = user.telefono || "";
      document.getElementById("editRol").value = user.rol;

      // Abrir modal
      const modal = new bootstrap.Modal(document.getElementById("editUserModal"));
      modal.show();
    } else {
      alert("Error al cargar usuario: " + result.error);
    }
  } catch (error) {
    alert("Error al cargar usuario: " + error.message);
  }
}

// Función para eliminar usuario (con fetch)
async function deleteUser(userId, userName) {
  if (!confirm(`¿Estás seguro de eliminar a ${userName}?`)) {
    return;
  }

  try {
    const response = await fetch(`/admin/api/users/${userId}`, {
      method: "DELETE",
    });
    const result = await response.json();

    if (result.success) {
      // Recargar la página para mostrar la tabla actualizada
      window.location.reload();
    } else {
      alert("Error al eliminar: " + result.error);
    }
  } catch (error) {
    alert("Error al eliminar: " + error.message);
  }
}
