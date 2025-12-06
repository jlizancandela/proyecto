(function () {
  const h = window.preact.h;
  const render = window.preact.render;

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
    } = window.useUsers();

    // Exponer reload globalmente
    window.reloadUsersTable = reload;

    const handleEdit = (user) => {
      if (window.openEditUserModal) {
        window.openEditUserModal(user.id, user);
      }
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
      return h(
        "div",
        { className: "text-center py-5" },
        h(
          "div",
          { className: "spinner-border text-primary", role: "status" },
          h("span", { className: "visually-hidden" }, "Cargando...")
        )
      );
    }

    if (error) {
      return h("div", { className: "alert alert-danger" }, error);
    }

    return h(
      "div",
      null,
      h(window.SearchBar, {
        value: search,
        onChange: setSearch,
      }),

      h(window.UsersTable, {
        users,
        onEdit: handleEdit,
        onDelete: handleDelete,
      }),

      totalPages > 1 &&
        h(window.Pagination, {
          currentPage: page,
          totalPages,
          onPageChange: changePage,
        })
    );
  }

  function initApp() {
    const container = document.getElementById("users-app");
    if (container) {
      render(h(UsersApp), container);
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initApp);
  } else {
    initApp();
  }
})();
