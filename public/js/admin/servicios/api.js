// API utilities for service operations.

/**
 * Fetches a single service by ID.
 *
 * @param {number} serviceId - The service ID.
 * @return {Promise<object>} The service data.
 */
export const fetchService = async (serviceId) => {
  const response = await fetch(`/admin/api/services/${serviceId}`);
  return response.json();
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
  return response.json();
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
  return response.json();
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
  return response.json();
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
  return response.json();
};
