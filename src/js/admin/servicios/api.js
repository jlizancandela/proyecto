/**
 * @file API utilities for service operations in the admin panel.
 * @project app-reservas
 */

/**
 * Fetches a single service by ID.
 *
 * @param {number} serviceId - The service ID.
 * @return {Promise<object>} The service data.
 */
/**
 * Handles the API response, checking for JSON content type.
 *
 * @param {Response} response - The fetch API response.
 * @returns {Promise<object>} The parsed JSON data.
 * @throws {Error} If the response is not ok or not JSON.
 */
const handleResponse = async (response) => {
  const contentType = response.headers.get("content-type");
  if (contentType && contentType.includes("application/json")) {
    try {
      const data = await response.json();
      if (!response.ok) {
        if (data.error || data.message) {
          return data;
        }
        throw new Error(`Server returned ${response.status}: ${JSON.stringify(data)}`);
      }
      return data;
    } catch (e) {
      // Content-Type was JSON, but parsing failed (e.g., PHP warnings in output)
      const text = await response.text();
      throw new Error(`JSON Parse Error: ${e.message}. Body: ${text.substring(0, 50)}...`);
    }
  } else {
    // Non-JSON response (likely HTML error page)
    const text = await response.text();
    // Try to extract a title or body if it's HTML to be helpful
    let errorMessage = `Server returned non-JSON response (${response.status})`;
    if (text.includes("<title>")) {
      const titleMatch = text.match(/<title>(.*?)<\/title>/);
      if (titleMatch) {
        errorMessage += `: ${titleMatch[1]}`;
      }
    } else {
      // Limit text length to avoid flooding the toast
      errorMessage += `: ${text.substring(0, 50)}...`;
    }
    throw new Error(errorMessage);
  }
};

/**
 * Fetches a single service by ID.
 *
 * @param {number} serviceId - The service ID.
 * @return {Promise<object>} The service data.
 */
export const fetchService = async (serviceId) => {
  const response = await fetch(`/admin/api/services/${serviceId}`);
  return handleResponse(response);
};

/**
 * Creates a new service.
 *
 * @param {object} serviceData - The service data to create.
 * @return {Promise<object>} The API response.
 */
export const createService = async (serviceData) => {
  const response = await fetch("/admin/api/services", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(serviceData),
  });
  return handleResponse(response);
};

/**
 * Updates an existing service.
 *
 * @param {number} serviceId - The service ID.
 * @param {object} serviceData - The service data to update.
 * @return {Promise<object>} The API response.
 */
export const updateService = async (serviceId, serviceData) => {
  const response = await fetch(`/admin/api/services/${serviceId}`, {
    method: "PUT",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(serviceData),
  });
  return handleResponse(response);
};

/**
 * Activates a service.
 *
 * @param {number} serviceId - The service ID.
 * @return {Promise<object>} The API response.
 */
export const activateService = async (serviceId) => {
  const response = await fetch(`/admin/api/services/${serviceId}/activate`, {
    method: "POST",
  });
  return handleResponse(response);
};

/**
 * Deactivates a service.
 *
 * @param {number} serviceId - The service ID.
 * @return {Promise<object>} The API response.
 */
export const deactivateService = async (serviceId) => {
  const response = await fetch(`/admin/api/services/${serviceId}/deactivate`, {
    method: "POST",
  });
  return handleResponse(response);
};
