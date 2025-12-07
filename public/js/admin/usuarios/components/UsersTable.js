import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { UserRow } from "./UserRow.js";

const html = htm.bind(h);

export function UsersTable({ users, onEdit, onDelete }) {
  if (users.length === 0) {
    return html`
      <div className="alert alert-info">
        <i className="bi bi-info-circle me-2"></i>
        No hay usuarios registrados
      </div>
    `;
  }

  return html`
    <div className="table-responsive shadow-sm rounded">
      <table className="table table-hover align-middle mb-0">
        <thead className="table-light">
          <tr>
            <th className="d-none d-md-table-cell border-0">ID</th>
            <th className="border-0">Nombre</th>
            <th className="d-none d-lg-table-cell border-0">Email</th>
            <th className="d-none d-xl-table-cell border-0">Teléfono</th>
            <th className="d-none d-sm-table-cell border-0">Rol</th>
            <th className="d-none d-md-table-cell border-0">Estado</th>
            <th className="d-none d-xl-table-cell border-0">Fecha</th>
            <th className="border-0 text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          ${users.map(
            (user) => html`
              <${UserRow} key=${user.id} user=${user} onEdit=${onEdit} onDelete=${onDelete} />
            `
          )}
        </tbody>
      </table>
    </div>
  `;
}
