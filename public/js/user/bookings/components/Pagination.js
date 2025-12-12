import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export const Pagination = ({ currentPage, totalPages, onPageChange }) => {
  if (totalPages <= 1) {
    return null;
  }

  const handlePrevious = () => {
    if (currentPage > 1) {
      onPageChange(currentPage - 1);
    }
  };

  const handleNext = () => {
    if (currentPage < totalPages) {
      onPageChange(currentPage + 1);
    }
  };

  const renderPageNumbers = () => {
    const pages = [];

    for (let i = 1; i <= totalPages; i++) {
      pages.push(
        html`
          <li class="page-item ${currentPage === i ? "active" : ""}">
            <button
              class="page-link"
              onClick=${() => onPageChange(i)}
              disabled=${currentPage === i}
            >
              ${i}
            </button>
          </li>
        `
      );
    }

    return pages;
  };

  return html`
    <nav aria-label="Pagination">
      <ul class="pagination justify-content-center">
        <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
          <button
            class="page-link"
            onClick=${handlePrevious}
            disabled=${currentPage === 1}
            aria-label="Previous"
          >
            <span aria-hidden="true">«</span>
            <span class="visually-hidden">Anterior</span>
          </button>
        </li>

        ${renderPageNumbers()}

        <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
          <button
            class="page-link"
            onClick=${handleNext}
            disabled=${currentPage === totalPages}
            aria-label="Next"
          >
            <span aria-hidden="true">»</span>
            <span class="visually-hidden">Siguiente</span>
          </button>
        </li>
      </ul>
    </nav>
  `;
};
