import { h, render } from "https://esm.sh/preact@10.19.3";
import { useState } from "https://esm.sh/preact@10.19.3/hooks";
import htm from "https://esm.sh/htm";
import { useUsers } from "./hooks/useUsers.js";
import { UsersHeader } from "./components/UsersHeader.js";
import { UsersTable } from "./components/UsersTable.js";
import { Pagination } from "./components/Pagination.js";
import { CreateUserModal } from "./components/CreateUserModal.js";
import { EditUserModal } from "./components/EditUserModal.js";

const html = htm.bind(h);

function UsersApp() {
  const {
    users,
    loading,
    error,
    page,
    totalPages,
    search,
    setSearch,
    deleteUser,
    changePage,
    reload,
  } = useUsers();

  const [editingUserId, setEditingUserId] = useState(null);

  const handleEdit = (user) => {
    setEditingUserId(user.id);
  };

  const handleCloseEdit = () => {
    setEditingUserId(null);
  };

  const handleDelete = async (user) => {
    if (!confirm(`¿Eliminar usuario ${user.nombreCompleto}?`)) return;

    try {
      await deleteUser(user.id);
    } catch (err) {
      alert("Error: " + err.message);
    }
  };

  if (loading && users.length === 0) {
    return html`
      <div className="text-center py-5">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Cargando...</span>
        </div>
      </div>
    `;
  }

  if (error) {
    return html`
      <div className="alert alert-danger">${error}</div>
    `;
  }

  return html`
    <div>
      <${UsersHeader} search=${search} onSearchChange=${setSearch} />

      <${UsersTable} users=${users} onEdit=${handleEdit} onDelete=${handleDelete} />

      ${totalPages > 1 &&
      html`
        <div className="mt-4 d-flex justify-content-center">
          <${Pagination} currentPage=${page} totalPages=${totalPages} onPageChange=${changePage} />
        </div>
      `}

      <${CreateUserModal} onSuccess=${reload} />
      <${EditUserModal} userId=${editingUserId} onSuccess=${reload} onClose=${handleCloseEdit} />
    </div>
  `;
}

render(h(UsersApp), document.getElementById("users-app"));
