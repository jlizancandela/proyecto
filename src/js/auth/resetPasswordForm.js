
/**
 * @file Handles reset password form interactions.
 * @project app-reservas
 */

import { initPasswordToggle } from "../shared/password-toggle.js";

const resetForm = document.querySelector('form[action="/reset-password"]');

if (resetForm) {
  initPasswordToggle("password", "toggle-password");
  initPasswordToggle("password-confirm", "toggle-password-confirm");
}
