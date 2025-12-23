const togglePasswordButton = document.getElementById("toggle-password");
const passwordInput = document.getElementById("password");

/**
 * Handles password visibility toggle.
 */
const handlePasswordToggle = () => {
  if (!togglePasswordButton || !passwordInput) return;

  const icon = togglePasswordButton.querySelector("i");
  const isPassword = passwordInput.type === "password";

  passwordInput.type = isPassword ? "text" : "password";
  icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
  togglePasswordButton.setAttribute(
    "aria-label",
    isPassword ? "Ocultar contraseña" : "Mostrar contraseña"
  );
};

/**
 * Initializes password visibility toggle functionality.
 */
const initializePasswordToggle = () => {
  if (togglePasswordButton) {
    togglePasswordButton.addEventListener("click", handlePasswordToggle);
  }
};

document.addEventListener("DOMContentLoaded", initializePasswordToggle);
