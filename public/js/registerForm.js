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

    document.querySelectorAll(".error").forEach((el) => (el.textContent = ""));

    let isValid = true;

    const { error } = Schema.validate(data, { abortEarly: false });

    if (error) {
      isValid = false;
      error.details.forEach((detail) => {
        const key = detail.path[0].replace("_", "-");
        const errorElement = document.getElementById(`${key}-error`);
        if (errorElement) {
          errorElement.textContent = detail.message;
        }
      });
    }

    if (isValid) {
      form.submit();
    }
  });
};

document.addEventListener("DOMContentLoaded", validateRegisterForm);
