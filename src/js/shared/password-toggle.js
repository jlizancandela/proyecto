
/**
 * Shared utility for password visibility toggle.
 */

/**
 * Initializes password toggles for a given input-group container or by ID.
 * @param {string} inputId - ID of the password input.
 * @param {string} toggleId - ID of the button to toggle visibility.
 */
export const initPasswordToggle = (inputId, toggleId) => {
  const input = document.getElementById(inputId);
  const toggleButton = document.getElementById(toggleId);

  if (!input || !toggleButton) return;

  toggleButton.addEventListener("click", () => {
    const icon = toggleButton.querySelector("i");
    const isPassword = input.type === "password";

    input.type = isPassword ? "text" : "password";
    icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
    toggleButton.setAttribute(
      "aria-label",
      isPassword ? "Hide password" : "Show password"
    );
  });
};
