const validateRegisterForm = () => {
  const form = document.getElementById("register-form");

  if (!form) {
    console.log("Formulario de registro no encontrado");
    return;
  }

  console.log("Formulario de registro encontrado, agregando listener");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    console.log("Submit interceptado");
    clearErrors();

    const formData = {
      nombre: form.querySelector('[name="nombre"]').value,
      apellidos: form.querySelector('[name="apellidos"]').value,
      email: form.querySelector('[name="email"]').value,
      telefono: form.querySelector('[name="telefono"]').value,
      password: form.querySelector('[name="password"]').value,
      "password-confirm": form.querySelector('[name="password-confirm"]').value,
    };

    console.log("Datos del formulario:", formData);

    const errors = validateForm(formData);

    if (Object.keys(errors).length > 0) {
      console.log("Errores de validación encontrados:", errors);
      displayErrors(errors);
      return;
    }

    console.log("Validación pasada, enviando formulario...");
    form.submit();
  });
};

const validateForm = (formData) => {
  const errors = {};

  if (!formData.nombre || formData.nombre.length < 2) {
    errors.nombre = "El nombre debe tener al menos 2 caracteres";
  }

  if (!formData.apellidos || formData.apellidos.length < 2) {
    errors.apellidos = "Los apellidos deben tener al menos 2 caracteres";
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!formData.email || !emailRegex.test(formData.email)) {
    errors.email = "El email no es válido";
  }

  if (!formData.password || formData.password.length < 6) {
    errors.password = "La contraseña debe tener al menos 6 caracteres";
  }

  if (formData.password !== formData["password-confirm"]) {
    errors["password-confirm"] = "Las contraseñas no coinciden";
  }

  return errors;
};

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

const clearErrors = () => {
  const errorMessages = document.querySelectorAll(".form-text.text-danger");
  errorMessages.forEach((error) => error.remove());

  const invalidInputs = document.querySelectorAll(".is-invalid");
  invalidInputs.forEach((input) => input.classList.remove("is-invalid"));
};

const togglePasswordVisibility = () => {
  const button = document.getElementById("toggle-password");
  const passwordInput = document.getElementById("password");
  const confirmInput = document.getElementById("password-confirm");

  if (!button || !passwordInput || !confirmInput) return;

  button.addEventListener("click", () => {
    const icon = button.querySelector("i");
    const isPassword = passwordInput.type === "password";

    // Cambiar tipo de ambos inputs
    passwordInput.type = isPassword ? "text" : "password";
    confirmInput.type = isPassword ? "text" : "password";

    // Cambiar icono y aria-label
    icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
    button.setAttribute("aria-label", isPassword ? "Ocultar contraseña" : "Mostrar contraseña");
  });
};

document.addEventListener("DOMContentLoaded", () => {
  validateRegisterForm();
  togglePasswordVisibility();
});
