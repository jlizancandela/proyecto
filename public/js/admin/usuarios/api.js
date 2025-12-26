// API utilities for user operations.

/**
 * Fetches a single user by ID.
 *
 * @param {number} userId - The user ID.
 * @return {Promise<object>} The user data.
 */
export const fetchUser = async (userId) => {
  const response = await fetch(`/admin/api/users/${userId}`);
  return response.json();
};

/**
 * Creates a new user.
 *
 * @param {FormData} formData - The user data to create (FormData for file upload support).
 * @return {Promise<object>} The API response.
 */
export const createUser = async (formData) => {
  const response = await fetch("/admin/api/users", {
    method: "POST",
    body: formData,
  });
  return response.json();
};

/**
 * Updates an existing user.
 *
 * @param {number} userId - The user ID.
 * @param {FormData} formData - The user data to update (FormData for file upload support).
 * @return {Promise<object>} The API response.
 */
export const updateUser = async (userId, formData) => {
  const response = await fetch(`/admin/api/users/${userId}`, {
    method: "POST",
    body: formData,
  });
  return response.json();
};

/**
 * Toggles user active status.
 *
 * @param {number} userId - The user ID.
 * @param {string} newStatus - New status ("0" or "1").
 * @return {Promise<object>} The API response.
 */
export const toggleUserStatus = async (userId, newStatus) => {
  const formData = new FormData();
  formData.append("activo", newStatus);

  const response = await fetch(`/admin/api/users/${userId}`, {
    method: "POST",
    body: formData,
  });
  return response.json();
};
