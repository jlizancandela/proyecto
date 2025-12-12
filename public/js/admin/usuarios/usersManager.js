function editUser(userId) {
  fetch("/admin/api/users/" + userId)
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        const user = result.data;
        document.getElementById("editUserId").value = user.id;
        document.getElementById("editNombre").value = user.nombre;
        document.getElementById("editApellidos").value = user.apellidos;
        document.getElementById("editEmail").value = user.email;
        document.getElementById("editTelefono").value = user.telefono || "";
        document.getElementById("editRol").value = user.rol;

        const modal = new bootstrap.Modal(document.getElementById("editUserModal"));
        modal.show();
      } else {
        alert("Error al cargar usuario: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
}

function deleteUser(userId, userName) {
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
}

document.addEventListener("click", function (e) {
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
});

document.getElementById("createUserForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const password = document.getElementById("createPassword").value;
  const passwordConfirm = document.getElementById("createPasswordConfirm").value;

  if (password !== passwordConfirm) {
    alert("Las contraseñas no coinciden");
    return;
  }

  const formData = {
    nombre: document.getElementById("createNombre").value,
    apellidos: document.getElementById("createApellidos").value,
    email: document.getElementById("createEmail").value,
    telefono: document.getElementById("createTelefono").value,
    password: password,
    rol: document.getElementById("createRol").value,
  };

  fetch("/admin/api/users", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Usuario creado correctamente");
        bootstrap.Modal.getInstance(document.getElementById("createUserModal")).hide();
        window.location.reload();
      } else {
        alert("Error: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
});

document.getElementById("editUserForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const userId = document.getElementById("editUserId").value;
  const password = document.getElementById("editPassword").value;
  const passwordConfirm = document.getElementById("editPasswordConfirm").value;

  if (password && password !== passwordConfirm) {
    alert("Las contraseñas no coinciden");
    return;
  }

  const formData = {
    nombre: document.getElementById("editNombre").value,
    apellidos: document.getElementById("editApellidos").value,
    email: document.getElementById("editEmail").value,
    telefono: document.getElementById("editTelefono").value,
    rol: document.getElementById("editRol").value,
  };

  if (password) {
    formData.password = password;
  }

  fetch("/admin/api/users/" + userId, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        alert("Usuario actualizado correctamente");
        bootstrap.Modal.getInstance(document.getElementById("editUserModal")).hide();
        window.location.reload();
      } else {
        alert("Error: " + result.error);
      }
    })
    .catch((error) => {
      alert("Error: " + error.message);
    });
});

document.getElementById("createUserModal").addEventListener("hidden.bs.modal", function () {
  document.getElementById("createUserForm").reset();
});

document.getElementById("editUserModal").addEventListener("hidden.bs.modal", function () {
  document.getElementById("editUserForm").reset();
});
