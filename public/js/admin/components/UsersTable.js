(function () {
  const h = window.preact.h;

  window.UsersTable = function ({ users, onEdit, onDelete }) {
    if (users.length === 0) {
      return h("div", { className: "alert alert-info" }, "No hay usuarios registrados");
    }

    return h(
      "div",
      { className: "table-responsive" },
      h(
        "table",
        { className: "table table-hover" },
        h(
          "thead",
          null,
          h(
            "tr",
            null,
            h("th", null, "ID"),
            h("th", null, "Nombre"),
            h("th", null, "Email"),
            h("th", null, "Teléfono"),
            h("th", null, "Rol"),
            h("th", null, "Estado"),
            h("th", null, "Fecha Registro"),
            h("th", null, "Acciones")
          )
        ),
        h(
          "tbody",
          null,
          users.map((user) =>
            h(window.UserRow, {
              key: user.id,
              user,
              onEdit,
              onDelete,
            })
          )
        )
      )
    );
  };
})();
