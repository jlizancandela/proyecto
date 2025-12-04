/**
 * Objeto que contiene el esquema de validación para el formulario de registro.
 * @type {Object}
 */
const Schema = Joi.object({
  nombre: Joi.string().min(3).required(),
  apellidos: Joi.string().min(3).required(),
  email: Joi.string().email().required(),
  password: Joi.string()
    .min(8)
    .required()
    .regex(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/
    ),
  password_confirm: Joi.string().min(8).required().valid(Joi.ref("password")),
  telefono: Joi.string().min(9).allow("").optional(),
});

/**
 * Objeto que contiene los mensajes de error personalizados para cada campo.
 * @type {Object}
 */
const errorMessages = {
  nombre: {
    "any.required": "El nombre es obligatorio",
    "string.empty": "El nombre es obligatorio",
    "string.min": "El nombre debe tener al menos 3 caracteres",
  },
  apellidos: {
    "any.required": "Los apellidos son obligatorios",
    "string.empty": "Los apellidos son obligatorios",
    "string.min": "Los apellidos deben tener al menos 3 caracteres",
  },
  email: {
    "any.required": "El email es obligatorio",
    "string.empty": "El email es obligatorio",
    "string.email": "El email no tiene un formato válido",
  },
  password: {
    "any.required": "La contraseña es obligatoria",
    "string.empty": "La contraseña es obligatoria",
    "string.min": "La contraseña debe tener al menos 8 caracteres",
    "string.pattern.base":
      "La contraseña debe contener mayúsculas, minúsculas, números y caracteres especiales",
    "string.regex.base":
      "La contraseña debe contener mayúsculas, minúsculas, números y caracteres especiales",
  },
  password_confirm: {
    "any.required": "Debes confirmar tu contraseña",
    "string.empty": "Debes confirmar tu contraseña",
    "string.min": "La confirmación debe tener al menos 8 caracteres",
    "any.only": "Las contraseñas no coinciden",
  },
  telefono: {
    "string.min": "El teléfono debe tener al menos 9 caracteres",
  },
};

/**
 * Obtiene el mensaje de error personalizado para un campo específico.
 * @param {string} field - El nombre del campo.
 * @param {string} errorType - El tipo de error.
 * @returns {string} El mensaje de error personalizado.
 */
function getCustomMessage(field, errorType) {
  if (errorMessages[field] && errorMessages[field][errorType]) {
    return errorMessages[field][errorType];
  }
  return "Campo inválido";
}

/**
 * Valida el formulario de registro.
 * @returns {void}
 */
const validateRegisterForm = () => {
  const form = document.getElementById("register-form");

  if (!form) {
    return;
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    console.log("Datos del formulario:", data);

    // Limpiar errores previos
    document
      .querySelectorAll(".text-danger")
      .forEach((el) => (el.textContent = ""));

    let isValid = true;

    const { error } = Schema.validate(data, { abortEarly: false });

    if (error) {
      isValid = false;
      error.details.forEach((detail) => {
        const field = detail.path[0];
        const errorType = detail.type;

        const key = field.replace("_", "-");
        const errorElement = document.getElementById(`${key}-error`);

        if (errorElement) {
          errorElement.textContent = getCustomMessage(field, errorType);
        }
      });
    }

    if (isValid) {
      form.submit();
    }
  });
};

document.addEventListener("DOMContentLoaded", validateRegisterForm);
