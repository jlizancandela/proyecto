const editUserIdInput = document.getElementById("editUserId");
const editNombreInput = document.getElementById("editNombre");
const editApellidosInput = document.getElementById("editApellidos");
const editEmailInput = document.getElementById("editEmail");
const editTelefonoInput = document.getElementById("editTelefono");
const editRolInput = document.getElementById("editRol");
const editUserModal = document.getElementById("editUserModal");

/**
 * Fetches and displays user data in the edit modal.
 * @param {string} userId - The ID of the user to edit.
 */
const editUser = async (userId) => {
  try {
    const response = await fetch(`/admin/api/users/${userId}`);
    const result = await response.json();

    if (result.success) {
      const user = result.data;
      editUserIdInput.value = user.id;
      editNombreInput.value = user.nombre;
      editApellidosInput.value = user.apellidos;
      editEmailInput.value = user.email;
      editTelefonoInput.value = user.telefono || "";
      editRolInput.value = user.rol;

      const modal = new bootstrap.Modal(editUserModal);
      modal.show();
    } else {
      alert("Error al cargar usuario: " + result.error);
    }
  } catch (error) {
    alert("Error al cargar usuario: " + error.message);
  }
};

/**
 * Deletes a user after confirmation.
 * @param {string} userId - The ID of the user to delete.
 * @param {string} userName - The name of the user to delete.
 */
const deleteUser = async (userId, userName) => {
  if (!confirm(`¿Estás seguro de eliminar a ${userName}?`)) {
    return;
  }

  try {
    const response = await fetch(`/admin/api/users/${userId}`, {
      method: "DELETE",
    });
    const result = await response.json();

    if (result.success) {
      window.location.reload();
    } else {
      alert("Error al eliminar: " + result.error);
    }
  } catch (error) {
    alert("Error al eliminar: " + error.message);
  }
};
