// Handles rendering and interaction of pagination controls.

/**
 * Renders the pagination controls.
 *
 * @param {HTMLElement} container - Container element for pagination.
 * @param {number} currentPage - Current active page.
 * @param {number} totalPages - Total number of pages.
 * @param {Function} onPageChange - Callback function when page changes.
 */
export const renderPagination = (container, currentPage, totalPages, onPageChange) => {
  if (totalPages <= 1) {
    container.innerHTML = "";
    return;
  }

  let paginationHTML = '<ul class="pagination justify-content-center">';

  paginationHTML += `
    <li class="page-item ${currentPage <= 1 ? "disabled" : ""}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
  `;

  for (let i = 1; i <= totalPages; i++) {
    if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
      paginationHTML += `
        <li class="page-item ${i === currentPage ? "active" : ""}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>
      `;
    } else if (i === currentPage - 3 || i === currentPage + 3) {
      paginationHTML += `
        <li class="page-item disabled">
          <span class="page-link">...</span>
        </li>
      `;
    }
  }

  paginationHTML += `
    <li class="page-item ${currentPage >= totalPages ? "disabled" : ""}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  `;

  paginationHTML += "</ul>";
  container.innerHTML = paginationHTML;

  container.querySelectorAll("a.page-link").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      const page = parseInt(e.currentTarget.dataset.page);
      if (page && page !== currentPage) {
        onPageChange(page);
      }
    });
  });
};
