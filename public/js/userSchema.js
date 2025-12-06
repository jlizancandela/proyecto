import * as yup from "https://cdn.jsdelivr.net/npm/yup@1.7.1/+esm";

export const userSchema = yup.object({
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
    .optional()
    .nullable()
    .transform((value) => (value === "" ? null : value)),
  "password-confirm": yup
    .string()
    .min(8, "La confirmación debe tener al menos 8 caracteres")
    .oneOf([yup.ref("password")], "Las contraseñas no coinciden")
    .optional()
    .nullable()
    .transform((value) => (value === "" ? null : value)),
  telefono: yup
    .string()
    .min(9, "El teléfono debe tener al menos 9 caracteres")
    .optional()
    .nullable()
    .transform((value) => (value === "" ? null : value)),
  rol: yup
    .string()
    .oneOf(["Cliente", "Especialista", "Admin"], "Rol inválido")
    .required("El rol es obligatorio"),
});
