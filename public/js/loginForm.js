// Handles password visibility toggle for login form.

const loginForm = document.getElementById("login-form");

/**
 * Handles password visibility toggle.
 */
const handlePasswordToggle = () => {
  const form = loginForm.elements;
  const toggleButton = document.getElementById("toggle-password");

  if (!toggleButton || !form.password) return;

  const icon = toggleButton.querySelector("i");
  const isPassword = form.password.type === "password";

  form.password.type = isPassword ? "text" : "password";
  icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
  toggleButton.setAttribute("aria-label", isPassword ? "Ocultar contraseña" : "Mostrar contraseña");
};

const toggleButton = document.getElementById("toggle-password");
if (toggleButton) {
  toggleButton.addEventListener("click", handlePasswordToggle);
}
