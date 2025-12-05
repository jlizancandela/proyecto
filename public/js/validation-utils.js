/**
 * Extrae los datos de un formulario HTML.
 * @param {HTMLFormElement} form - El formulario del cual extraer los datos
 * @returns {Object} Objeto con los datos del formulario
 */
export const getFormData = (form) => {
  const formData = new FormData(form);
  return Object.fromEntries(formData);
};

/**
 * Valida datos contra un esquema de validación (Yup).
 * Función pura sin efectos secundarios.
 * @param {Object} schema - Esquema de validación de Yup
 * @param {Object} data - Datos a validar
 * @returns {Promise<{isValid: boolean, errors: Array}>} Resultado de la validación
 */
export const validateData = async (schema, data) => {
  try {
    await schema.validate(data, { abortEarly: false });
    return { isValid: true, errors: [] };
  } catch (error) {
    return {
      isValid: false,
      errors:
        error.inner?.map((err) => ({
          field: err.path,
          message: err.message,
        })) || [],
    };
  }
};

/**
 * Muestra errores en el DOM.
 * Busca elementos con ID en formato `${field}-error`.
 * @param {Array} errors - Array de errores con formato {field, message}
 */
export const showErrors = (errors) => {
  errors.forEach(({ field, message }) => {
    const errorElement = document.getElementById(`${field}-error`);
    if (errorElement) errorElement.textContent = message;
  });
};

/**
 * Limpia todos los mensajes de error del formulario.
 * @param {string} selector - Selector CSS para los elementos de error
 */
export const clearErrors = (selector = ".text-danger") => {
  document.querySelectorAll(selector).forEach((el) => (el.textContent = ""));
};

/**
 * Valida un formulario y lo envía si es válido.
 * @param {Object} schema - Esquema de validación de Yup
 * @param {HTMLFormElement} form - Formulario a validar
 */
export const validateForm = async (schema, form) => {
  const data = getFormData(form);
  console.log("Datos del formulario:", data);

  const { isValid, errors } = await validateData(schema, data);

  if (isValid) {
    form.submit();
  } else {
    showErrors(errors);
  }
};
