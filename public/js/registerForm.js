const registerForm = document.getElementById("register-form");
const togglePasswordButton = document.getElementById("toggle-password");
const passwordInput = document.getElementById("password");
const passwordConfirmInput = document.getElementById("password-confirm");

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const MIN_NAME_LENGTH = 2;
const MIN_PASSWORD_LENGTH = 6;

/**
 * Validates form data and returns validation errors.
 * @param {Object} formData - The form data to validate.
 * @returns {Object} Object containing validation errors.
 */
const validateForm = (formData) => {
  const errors = {};

  if (!formData.nombre || formData.nombre.length < MIN_NAME_LENGTH) {
    errors.nombre = "El nombre debe tener al menos 2 caracteres";
  }

  if (!formData.apellidos || formData.apellidos.length < MIN_NAME_LENGTH) {
    errors.apellidos = "Los apellidos deben tener al menos 2 caracteres";
  }

  if (!formData.email || !EMAIL_REGEX.test(formData.email)) {
    errors.email = "El email no es válido";
  }

  if (!formData.password || formData.password.length < MIN_PASSWORD_LENGTH) {
    errors.password = "La contraseña debe tener al menos 6 caracteres";
  }

  if (formData.password !== formData["password-confirm"]) {
    errors["password-confirm"] = "Las contraseñas no coinciden";
  }

  return errors;
};

/**
 * Displays validation errors in the form.
 * @param {Object} errors - Object containing validation errors.
 */
const displayErrors = (errors) => {
  Object.keys(errors).forEach((field) => {
    const input = document.querySelector(`[name="${field}"]`);
    if (input) {
      const errorDiv = document.createElement("div");
      errorDiv.className = "form-text text-danger";
      errorDiv.textContent = errors[field];
      input.classList.add("is-invalid");
      input.parentElement.appendChild(errorDiv);
    }
  });
};

/**
 * Clears all validation errors from the form.
 */
const clearErrors = () => {
  const errorMessages = document.querySelectorAll(".form-text.text-danger");
  errorMessages.forEach((error) => error.remove());

  const invalidInputs = document.querySelectorAll(".is-invalid");
  invalidInputs.forEach((input) => input.classList.remove("is-invalid"));
};

/**
 * Handles register form submission with validation.
 */
const handleRegisterFormSubmit = async (e) => {
  e.preventDefault();
  clearErrors();

  const formData = {
    nombre: registerForm.querySelector('[name="nombre"]').value,
    apellidos: registerForm.querySelector('[name="apellidos"]').value,
    email: registerForm.querySelector('[name="email"]').value,
    telefono: registerForm.querySelector('[name="telefono"]').value,
    password: registerForm.querySelector('[name="password"]').value,
    "password-confirm": registerForm.querySelector('[name="password-confirm"]').value,
  };

  const errors = validateForm(formData);

  if (Object.keys(errors).length > 0) {
    displayErrors(errors);
    return;
  }

  registerForm.submit();
};

/**
 * Handles password visibility toggle for both password fields.
 */
const handlePasswordToggle = () => {
  const icon = togglePasswordButton.querySelector("i");
  const isPassword = passwordInput.type === "password";

  passwordInput.type = isPassword ? "text" : "password";
  passwordConfirmInput.type = isPassword ? "text" : "password";

  icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
  togglePasswordButton.setAttribute(
    "aria-label",
    isPassword ? "Ocultar contraseña" : "Mostrar contraseña"
  );
};

/**
 * Initializes register form with validation and password toggle.
 */
const initializeRegisterForm = () => {
  if (registerForm) {
    registerForm.addEventListener("submit", handleRegisterFormSubmit);
  }

  if (togglePasswordButton && passwordInput && passwordConfirmInput) {
    togglePasswordButton.addEventListener("click", handlePasswordToggle);
  }
};

document.addEventListener("DOMContentLoaded", initializeRegisterForm);
