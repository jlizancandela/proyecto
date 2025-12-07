import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export function SearchBar({ value, onChange }) {
  return html`
    <div className="position-relative">
      <i
        className="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"
      ></i>
      <input
        type="text"
        className="form-control ps-5"
        placeholder="Buscar por nombre, email o teléfono..."
        value=${value}
        onInput=${(e) => onChange(e.target.value)}
      />
    </div>
  `;
}
