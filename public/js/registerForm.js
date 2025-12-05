import * as yup from "https://cdn.jsdelivr.net/npm/yup@1.7.1/+esm";
import { validateForm, clearErrors } from "./validation-utils.js";

/**
 * Objeto que contiene el esquema de validación para el formulario de registro.
 * @type {Object}
 */
const Schema = yup.object({
  nombre: yup
    .string()
    .min(3, "El nombre debe tener al menos 3 caracteres")
    .required("El nombre es obligatorio"),
  apellidos: yup
    .string()
    .min(3, "Los apellidos deben tener al menos 3 caracteres")
    .required("Los apellidos son obligatorios"),
  email: yup
    .string()
    .email("El email no tiene un formato válido")
    .required("El email es obligatorio"),
  password: yup
    .string()
    .min(8, "La contraseña debe tener al menos 8 caracteres")
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#])[A-Za-z\d@$!%*?&.#]{8,}$/,
      "La contraseña debe contener mayúsculas, minúsculas, números y caracteres especiales"
    )
    .required("La contraseña es obligatoria"),
  "password-confirm": yup
    .string()
    .min(8, "La confirmación debe tener al menos 8 caracteres")
    .oneOf([yup.ref("password")], "Las contraseñas no coinciden")
    .required("Debes confirmar tu contraseña"),
  telefono: yup
    .string()
    .min(9, "El teléfono debe tener al menos 9 caracteres")
    .optional()
    .nullable()
    .transform((value) => (value === "" ? null : value)),
});

/**
 * Valida el formulario de registro.
 * @returns {void}
 */
const validateRegisterForm = () => {
  const form = document.getElementById("register-form");

  if (!form) {
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    clearErrors();
    validateForm(Schema, form);
  });
};

document.addEventListener("DOMContentLoaded", validateRegisterForm);
