(function () {
  const h = window.preact.h;

  const rolBadges = {
    Admin: "danger",
    Especialista: "warning",
    Cliente: "info",
  };

  window.UserRow = function ({ user, onEdit, onDelete }) {
    return h(
      "tr",
      null,
      h("td", null, user.id),
      h("td", null, user.nombreCompleto),
      h("td", null, user.email),
      h("td", null, user.telefono || "-"),
      h(
        "td",
        null,
        h(
          "span",
          {
            className: `badge bg-${rolBadges[user.rol] || "secondary"}`,
          },
          user.rol
        )
      ),
      h(
        "td",
        null,
        h(
          "span",
          {
            className: `badge bg-${user.activo ? "success" : "secondary"}`,
          },
          user.activo ? "Activo" : "Inactivo"
        )
      ),
      h("td", null, user.fechaRegistro),
      h(
        "td",
        null,
        h(
          "div",
          { className: "btn-group btn-group-sm" },
          h(
            "button",
            {
              className: "btn btn-outline-warning",
              title: "Editar",
              onClick: () => onEdit(user),
            },
            h("i", { className: "bi bi-pencil" })
          ),
          h(
            "button",
            {
              className: "btn btn-outline-danger",
              title: "Eliminar",
              onClick: () => onDelete(user),
            },
            h("i", { className: "bi bi-trash" })
          )
        )
      )
    );
  };
})();
