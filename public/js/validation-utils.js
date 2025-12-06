export const getFormData = (form) => {
  const formData = new FormData(form);
  return Object.fromEntries(formData);
};

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

export const showErrors = (errors) => {
  errors.forEach(({ field, message }) => {
    const errorElement = document.getElementById(`${field}-error`);
    if (errorElement) errorElement.textContent = message;
  });
};

export const clearErrors = (selector = ".text-danger") => {
  document.querySelectorAll(selector).forEach((el) => (el.textContent = ""));
};

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
