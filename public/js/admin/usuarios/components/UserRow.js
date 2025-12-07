import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

const rolBadges = {
  Admin: "danger",
  Especialista: "warning",
  Cliente: "info",
};

export function UserRow({ user, onEdit, onDelete }) {
  return html`
    <tr>
      <td className="d-none d-md-table-cell text-muted">${user.id}</td>
      <td>
        <div className="fw-semibold">${user.nombreCompleto}</div>
        <small className="text-muted d-lg-none">
          <i className="bi bi-envelope me-1"></i>
          ${user.email}
        </small>
      </td>
      <td className="d-none d-lg-table-cell text-muted">${user.email}</td>
      <td className="d-none d-xl-table-cell text-muted">${user.telefono || "-"}</td>
      <td className="d-none d-sm-table-cell">
        <span className="badge rounded-pill bg-${rolBadges[user.rol] || "secondary"}">
          ${user.rol}
        </span>
      </td>
      <td className="d-none d-md-table-cell">
        <span className="badge rounded-pill bg-${user.activo ? "success" : "secondary"}">
          <i className="bi bi-${user.activo ? "check-circle" : "x-circle"} me-1"></i>
          ${user.activo ? "Activo" : "Inactivo"}
        </span>
      </td>
      <td className="d-none d-xl-table-cell text-muted small">${user.fechaRegistro}</td>
      <td className="text-end">
        <div className="btn-group btn-group-sm" role="group">
          <button
            className="btn btn-outline-warning"
            title="Editar usuario"
            onClick=${() => onEdit(user)}
          >
            <i className="bi bi-pencil-square"></i>
            <span className="d-none d-lg-inline ms-1">Editar</span>
          </button>
          <button
            className="btn btn-outline-danger"
            title="Eliminar usuario"
            onClick=${() => onDelete(user)}
          >
            <i className="bi bi-trash3"></i>
            <span className="d-none d-xl-inline ms-1">Eliminar</span>
          </button>
        </div>
      </td>
    </tr>
  `;
}
