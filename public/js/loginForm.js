const togglePasswordVisibility = () => {
  const button = document.getElementById("toggle-password");
  const input = document.getElementById("password");

  if (!button || !input) return;

  button.addEventListener("click", () => {
    const icon = button.querySelector("i");
    const isPassword = input.type === "password";

    input.type = isPassword ? "text" : "password";
    icon.className = isPassword ? "bi bi-eye-slash" : "bi bi-eye";
    button.setAttribute("aria-label", isPassword ? "Ocultar contraseña" : "Mostrar contraseña");
  });
};

document.addEventListener("DOMContentLoaded", togglePasswordVisibility);
