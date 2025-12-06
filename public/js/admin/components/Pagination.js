(function () {
  const h = window.preact.h;
  const { useMemo } = window.preactHooks;

  window.Pagination = function ({ currentPage, totalPages, onPageChange }) {
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

    return h(
      "nav",
      { "aria-label": "Paginación de usuarios" },
      h(
        "ul",
        { className: "pagination justify-content-center mb-0" },
        h(
          "li",
          {
            className: `page-item ${currentPage === 1 ? "disabled" : ""}`,
          },
          h(
            "a",
            {
              className: "page-link",
              href: "#",
              onClick: (e) => {
                e.preventDefault();
                if (currentPage > 1) onPageChange(currentPage - 1);
              },
            },
            "Anterior"
          )
        ),

        pages.map((pageNum) =>
          h(
            "li",
            {
              key: pageNum,
              className: `page-item ${pageNum === currentPage ? "active" : ""}`,
            },
            h(
              "a",
              {
                className: "page-link",
                href: "#",
                onClick: (e) => {
                  e.preventDefault();
                  onPageChange(pageNum);
                },
              },
              pageNum
            )
          )
        ),

        h(
          "li",
          {
            className: `page-item ${currentPage === totalPages ? "disabled" : ""}`,
          },
          h(
            "a",
            {
              className: "page-link",
              href: "#",
              onClick: (e) => {
                e.preventDefault();
                if (currentPage < totalPages) onPageChange(currentPage + 1);
              },
            },
            "Siguiente"
          )
        )
      )
    );
  };
})();
