import { h } from "https://esm.sh/preact@10.19.3";
import { useMemo } from "https://esm.sh/preact@10.19.3/hooks";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export function Pagination({ currentPage, totalPages, onPageChange }) {
  const pages = useMemo(() => {
    const result = [];
    const maxVisible = 5;

    let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let end = Math.min(totalPages, start + maxVisible - 1);

    if (end - start < maxVisible - 1) {
      start = Math.max(1, end - maxVisible + 1);
    }

    for (let i = start; i <= end; i++) {
      result.push(i);
    }

    return result;
  }, [currentPage, totalPages]);

  return html`
    <nav aria-label="Paginación de usuarios">
      <ul className="pagination justify-content-center mb-0">
        <li className="page-item ${currentPage === 1 ? "disabled" : ""}">
          <a
            className="page-link"
            href="#"
            onClick=${(e) => {
              e.preventDefault();
              if (currentPage > 1) onPageChange(currentPage - 1);
            }}
          >
            Anterior
          </a>
        </li>

        ${pages.map(
          (pageNum) => html`
            <li key=${pageNum} className="page-item ${pageNum === currentPage ? "active" : ""}">
              <a
                className="page-link"
                href="#"
                onClick=${(e) => {
                  e.preventDefault();
                  onPageChange(pageNum);
                }}
              >
                ${pageNum}
              </a>
            </li>
          `
        )}

        <li className="page-item ${currentPage === totalPages ? "disabled" : ""}">
          <a
            className="page-link"
            href="#"
            onClick=${(e) => {
              e.preventDefault();
              if (currentPage < totalPages) onPageChange(currentPage + 1);
            }}
          >
            Siguiente
          </a>
        </li>
      </ul>
    </nav>
  `;
}
