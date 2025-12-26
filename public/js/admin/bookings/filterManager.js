// Manages URL parameters and filter state for bookings.

/**
 * Updates the URL parameters without reloading the page.
 *
 * @param {object} filters - Current filter parameters.
 */
export const updateUrlParams = (filters) => {
  const params = new URLSearchParams(filters);
  const newUrl = `${window.location.pathname}?${params.toString()}`;
  window.history.pushState({}, "", newUrl);
};

/**
 * Updates the PDF export link with current filter parameters.
 *
 * @param {URLSearchParams} params - Current URL parameters.
 */
export const updatePdfExportLink = (params) => {
  const pdfLink = document.querySelector('a[href^="/admin/bookings/pdf"]');
  if (pdfLink) {
    pdfLink.href = `/admin/bookings/pdf?${params.toString()}`;
  }
};

/**
 * Loads filter state from URL parameters.
 *
 * @param {Array<string>} fields - Array of field names to extract from URL.
 * @return {object} Filter object with values from URL.
 */
export const loadFiltersFromUrl = (fields) => {
  const urlParams = new URLSearchParams(window.location.search);
  const filters = {};

  fields.forEach((field) => {
    const value = urlParams.get(field);
    if (value) filters[field] = value;
  });

  return filters;
};

/**
 * Builds filter object from form inputs.
 *
 * @param {object} filterInputs - Object containing filter input elements.
 * @return {object} Filter object with non-empty values.
 */
export const buildFiltersFromInputs = (filterInputs) => {
  const filters = {};

  Object.entries(filterInputs).forEach(([key, input]) => {
    if (input.value) {
      filters[key] = input.value;
    }
  });

  return filters;
};

/**
 * Clears all filter inputs.
 *
 * @param {object} filterInputs - Object containing filter input elements.
 */
export const clearFilterInputs = (filterInputs) => {
  Object.values(filterInputs).forEach((input) => {
    input.value = "";
  });
};
