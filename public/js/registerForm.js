const validateRegisterForm = () => {
  const form = document.getElementById("register-form");

  if (!form) {
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();

    const formData = {
      nombre: form.querySelector('[name="nombre"]').value,
      apellidos: form.querySelector('[name="apellidos"]').value,
      email: form.querySelector('[name="email"]').value,
      telefono: form.querySelector('[name="telefono"]').value,
      password: form.querySelector('[name="password"]').value,
      "password-confirm": form.querySelector('[name="password-confirm"]').value,
    };

    const errors = validateForm(formData);

    if (Object.keys(errors).length > 0) {
      displayErrors(errors);
      return;
    }

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

document.addEventListener("DOMContentLoaded", validateRegisterForm);
