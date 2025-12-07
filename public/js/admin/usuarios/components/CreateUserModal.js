import { h } from "https://esm.sh/preact@10.19.3";
import { useState } from "https://esm.sh/preact@10.19.3/hooks";
import htm from "https://esm.sh/htm";

const html = htm.bind(h);

export function CreateUserModal({ onSuccess }) {
  const [formData, setFormData] = useState({
    nombre: "",
    apellidos: "",
    email: "",
    telefono: "",
    password: "",
    "password-confirm": "",
    rol: "Cliente",
  });
  const [errors, setErrors] = useState({});
  const [isOpen, setIsOpen] = useState(false);

  // Exponer función global para abrir el modal desde el botón en userstable.latte
  if (typeof window !== "undefined") {
    window.openCreateUserModal = () => setIsOpen(true);
  }

  const handleClose = () => {
    setIsOpen(false);
    clearForm();
  };

  const clearForm = () => {
    setFormData({
      nombre: "",
      apellidos: "",
      email: "",
      telefono: "",
      password: "",
      "password-confirm": "",
      rol: "Cliente",
    });
    setErrors({});
  };

  const validateForm = async () => {
    const newErrors = {};

    if (!formData.nombre || formData.nombre.length < 2) {
      newErrors.nombre = "El nombre debe tener al menos 2 caracteres";
    }

    if (!formData.apellidos || formData.apellidos.length < 2) {
      newErrors.apellidos = "Los apellidos deben tener al menos 2 caracteres";
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email || !emailRegex.test(formData.email)) {
      newErrors.email = "El email no es válido";
    }

    if (!formData.password || formData.password.length < 6) {
      newErrors.password = "La contraseña debe tener al menos 6 caracteres";
    }

    if (formData.password !== formData["password-confirm"]) {
      newErrors["password-confirm"] = "Las contraseñas no coinciden";
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors({});

    const isValid = await validateForm();
    if (!isValid) return;

    try {
      const response = await fetch("/admin/api/users", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const result = await response.json();

      if (result.success) {
        handleClose();
        onSuccess();
      } else {
        alert("Error: " + (result.error || "No se pudo crear el usuario"));
      }
    } catch (error) {
      alert("Error de conexión");
    }
  };

  const handleChange = (field, value) => {
    setFormData((prev) => ({ ...prev, [field]: value }));
    if (errors[field]) {
      setErrors((prev) => ({ ...prev, [field]: "" }));
    }
  };

  return html`
    <div
      className="modal fade ${isOpen ? "show" : ""}"
      id="createUserModal"
      tabindex="-1"
      style="display: ${isOpen ? "block" : "none"}"
      aria-labelledby="createUserModalLabel"
    >
      <div className="modal-dialog">
        <div className="modal-content">
          <div className="modal-header">
            <h5 className="modal-title" id="createUserModalLabel">
              <i className="bi bi-person-plus me-2"></i>
              Crear Nuevo Usuario
            </h5>
            <button
              type="button"
              className="btn-close"
              onClick=${handleClose}
              aria-label="Close"
            ></button>
          </div>

          <form onSubmit=${handleSubmit}>
            <div className="modal-body">
              <!-- Nombre -->
              <div className="mb-3">
                <label className="form-label">Nombre *</label>
                <input
                  type="text"
                  className="form-control"
                  value=${formData.nombre}
                  onInput=${(e) => handleChange("nombre", e.target.value)}
                  required
                />
                ${errors.nombre &&
                html`
                  <div className="form-text text-danger">${errors.nombre}</div>
                `}
              </div>

              <!-- Apellidos -->
              <div className="mb-3">
                <label className="form-label">Apellidos *</label>
                <input
                  type="text"
                  className="form-control"
                  value=${formData.apellidos}
                  onInput=${(e) => handleChange("apellidos", e.target.value)}
                  required
                />
                ${errors.apellidos &&
                html`
                  <div className="form-text text-danger">${errors.apellidos}</div>
                `}
              </div>

              <!-- Email -->
              <div className="mb-3">
                <label className="form-label">Email *</label>
                <input
                  type="email"
                  className="form-control"
                  value=${formData.email}
                  onInput=${(e) => handleChange("email", e.target.value)}
                  required
                />
                ${errors.email &&
                html`
                  <div className="form-text text-danger">${errors.email}</div>
                `}
              </div>

              <!-- Teléfono -->
              <div className="mb-3">
                <label className="form-label">Teléfono</label>
                <input
                  type="tel"
                  className="form-control"
                  value=${formData.telefono}
                  onInput=${(e) => handleChange("telefono", e.target.value)}
                />
                ${errors.telefono &&
                html`
                  <div className="form-text text-danger">${errors.telefono}</div>
                `}
              </div>

              <!-- Password -->
              <div className="mb-3">
                <label className="form-label">Contraseña *</label>
                <input
                  type="password"
                  className="form-control"
                  value=${formData.password}
                  onInput=${(e) => handleChange("password", e.target.value)}
                  required
                />
                ${errors.password &&
                html`
                  <div className="form-text text-danger">${errors.password}</div>
                `}
              </div>

              <!-- Confirmar Password -->
              <div className="mb-3">
                <label className="form-label">Confirmar Contraseña *</label>
                <input
                  type="password"
                  className="form-control"
                  value=${formData["password-confirm"]}
                  onInput=${(e) => handleChange("password-confirm", e.target.value)}
                  required
                />
                ${errors["password-confirm"] &&
                html`
                  <div className="form-text text-danger">${errors["password-confirm"]}</div>
                `}
              </div>

              <!-- Rol -->
              <div className="mb-3">
                <label className="form-label">Rol *</label>
                <select
                  className="form-select"
                  value=${formData.rol}
                  onChange=${(e) => handleChange("rol", e.target.value)}
                  required
                >
                  <option value="Cliente">Cliente</option>
                  <option value="Especialista">Especialista</option>
                  <option value="Admin">Administrador</option>
                </select>
                ${errors.rol &&
                html`
                  <div className="form-text text-danger">${errors.rol}</div>
                `}
              </div>
            </div>

            <div className="modal-footer">
              <button type="button" className="btn btn-secondary" onClick=${handleClose}>
                Cancelar
              </button>
              <button type="submit" className="btn btn-primary">
                <i className="bi bi-save me-1"></i>
                Crear Usuario
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    ${isOpen &&
    html`
      <div className="modal-backdrop fade show" onClick=${handleClose}></div>
    `}
  `;
}
