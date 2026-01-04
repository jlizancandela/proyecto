import {
  __commonJS,
  __toESM,
  bootstrap_esm_exports,
  init_bootstrap_esm
} from "./chunk-JPOHOG3X.js";

// src/js/auth/loginForm.js
var require_loginForm = __commonJS({
  "src/js/auth/loginForm.js"() {
    var loginForm = document.getElementById("login-form");
    var handlePasswordToggle = () => {
      const form = loginForm.elements;
      const toggleButton2 = document.getElementById("toggle-password");
      if (!toggleButton2 || !form.password) return;
      const icon = toggleButton2.querySelector("i");
      const isPassword = form.password.type === "password";
      form.password.type = isPassword ? "text" : "password";
      icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
      toggleButton2.setAttribute("aria-label", isPassword ? "Ocultar contrase\xF1a" : "Mostrar contrase\xF1a");
    };
    var toggleButton = document.getElementById("toggle-password");
    if (loginForm && toggleButton) {
      toggleButton.addEventListener("click", handlePasswordToggle);
    }
  }
});

// src/js/auth/registerForm.js
var require_registerForm = __commonJS({
  "src/js/auth/registerForm.js"() {
    var registerForm = document.getElementById("register-form");
    var EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var MIN_NAME_LENGTH = 2;
    var MIN_PASSWORD_LENGTH = 6;
    var validateForm = (formData) => {
      const errors = {};
      if (!formData.nombre || formData.nombre.length < MIN_NAME_LENGTH) {
        errors.nombre = "El nombre debe tener al menos 2 caracteres";
      }
      if (!formData.apellidos || formData.apellidos.length < MIN_NAME_LENGTH) {
        errors.apellidos = "Los apellidos deben tener al menos 2 caracteres";
      }
      if (!formData.email || !EMAIL_REGEX.test(formData.email)) {
        errors.email = "El email no es v\xE1lido";
      }
      if (!formData.password || formData.password.length < MIN_PASSWORD_LENGTH) {
        errors.password = "La contrase\xF1a debe tener al menos 6 caracteres";
      }
      if (formData.password !== formData["password-confirm"]) {
        errors["password-confirm"] = "Las contrase\xF1as no coinciden";
      }
      return errors;
    };
    var displayErrors = (errors) => {
      const form = registerForm.elements;
      Object.keys(errors).forEach((field) => {
        const input = form[field];
        if (input) {
          const errorDiv = document.createElement("div");
          errorDiv.className = "form-text text-danger";
          errorDiv.textContent = errors[field];
          const targetInput = input instanceof NodeList ? input[0] : input;
          targetInput.classList.add("is-invalid");
          targetInput.parentElement.appendChild(errorDiv);
        }
      });
    };
    var clearErrors = () => {
      const errorMessages = document.querySelectorAll(".form-text.text-danger");
      errorMessages.forEach((error) => error.remove());
      const invalidInputs = document.querySelectorAll(".is-invalid");
      invalidInputs.forEach((input) => input.classList.remove("is-invalid"));
    };
    var handleRegisterFormSubmit = (e) => {
      e.preventDefault();
      clearErrors();
      const form = e.target.elements;
      const formData = {
        nombre: form.nombre.value,
        apellidos: form.apellidos.value,
        email: form.email.value,
        telefono: form.telefono.value,
        password: form.password.value,
        "password-confirm": form["password-confirm"].value
      };
      const errors = validateForm(formData);
      if (Object.keys(errors).length > 0) {
        displayErrors(errors);
        return;
      }
      registerForm.submit();
    };
    var handlePasswordToggle = () => {
      const form = registerForm.elements;
      const toggleButton2 = document.getElementById("toggle-password");
      if (!toggleButton2 || !form.password || !form["password-confirm"]) return;
      const icon = toggleButton2.querySelector("i");
      const isPassword = form.password.type === "password";
      form.password.type = isPassword ? "text" : "password";
      form["password-confirm"].type = isPassword ? "text" : "password";
      icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
      toggleButton2.setAttribute("aria-label", isPassword ? "Ocultar contrase\xF1a" : "Mostrar contrase\xF1a");
    };
    if (registerForm) {
      registerForm.addEventListener("submit", handleRegisterFormSubmit);
    }
    var toggleButton = document.getElementById("toggle-password");
    if (registerForm && toggleButton) {
      toggleButton.addEventListener("click", handlePasswordToggle);
    }
  }
});

// src/js/auth/authApp.js
var require_authApp = __commonJS({
  "src/js/auth/authApp.js"() {
    var import_loginForm = __toESM(require_loginForm());
    var import_registerForm = __toESM(require_registerForm());
    init_bootstrap_esm();
    window.bootstrap = bootstrap_esm_exports;
  }
});
export default require_authApp();
//# sourceMappingURL=auth.js.map
