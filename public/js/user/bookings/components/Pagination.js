/**
 * @file Pagination Component
 * @project app-reservas
 *
 * Displays pagination controls for navigating between pages of results.
 * Shows previous/next buttons and numbered page buttons with current page highlighted.
 */

import { h } from "https://esm.sh/preact@10.19.3";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

/**
 * Handles previous page navigation.
 * @param {number} currentPage - Current page number.
 * @param {Function} onPageChange - Callback for page change.
 */
const handlePreviousPage = (currentPage, onPageChange) => {
  if (currentPage > 1) {
    onPageChange(currentPage - 1);
  }
};

/**
 * Handles next page navigation.
 * @param {number} currentPage - Current page number.
 * @param {number} totalPages - Total number of pages.
 * @param {Function} onPageChange - Callback for page change.
 */
const handleNextPage = (currentPage, totalPages, onPageChange) => {
  if (currentPage < totalPages) {
    onPageChange(currentPage + 1);
  }
};

/**
 * Renders individual page number buttons.
 * @param {number} totalPages - Total number of pages.
 * @param {number} currentPage - Current page number.
 * @param {Function} onPageChange - Callback for page change.
 * @returns {Array} Array of page button components.
 */
const renderPageNumbers = (totalPages, currentPage, onPageChange) => {
  const pages = [];

  for (let i = 1; i <= totalPages; i++) {
    pages.push(
      html`
        <li class="page-item ${currentPage === i ? "active" : ""}">
          <button class="page-link" onClick=${() => onPageChange(i)} disabled=${currentPage === i}>
            ${i}
          </button>
        </li>
      `
    );
  }

  return pages;
};

/**
 * Renders pagination component with previous, next, and page number buttons.
 * @param {Object} props - Component props.
 * @param {number} props.currentPage - Current page number.
 * @param {number} props.totalPages - Total number of pages.
 * @param {Function} props.onPageChange - Callback for page change.
 * @returns {Object|null} Preact component or null if only one page.
 */
export const Pagination = ({ currentPage, totalPages, onPageChange }) => {
  if (totalPages <= 1) {
    return null;
  }

  return html`
    <nav aria-label="Pagination">
      <ul class="pagination justify-content-center">
        <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
          <button
            class="page-link"
            onClick=${() => handlePreviousPage(currentPage, onPageChange)}
            disabled=${currentPage === 1}
            aria-label="Previous"
          >
            <span aria-hidden="true">«</span>
            <span class="visually-hidden">Anterior</span>
          </button>
        </li>

        ${renderPageNumbers(totalPages, currentPage, onPageChange)}

        <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
          <button
            class="page-link"
            onClick=${() => handleNextPage(currentPage, totalPages, onPageChange)}
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
