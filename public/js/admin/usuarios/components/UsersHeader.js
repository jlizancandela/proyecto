import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";
import { SearchBar } from "./SearchBar.js";

const html = htm.bind(h);

export function UsersHeader({ search, onSearchChange }) {
  return html`
    <div className="row g-3 mb-3 justify-content-between align-items-center">
      <div className="col-12 col-md-6 col-lg-5 col-xl-4">
        <${SearchBar} value=${search} onChange=${onSearchChange} />
      </div>
      <div className="col-12 col-md-auto">
        <button
          className="btn btn-primary shadow-sm w-100 w-md-auto px-4"
          onClick=${() => window.openCreateUserModal()}
        >
          <i className="bi bi-plus-circle me-2"></i>
          Nuevo Usuario
        </button>
      </div>
    </div>
  `;
}
