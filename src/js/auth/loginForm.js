
/**
 * @file Handles login form interactions, specifically password visibility.
 * @project app-reservas
 */

import { initPasswordToggle } from "../shared/password-toggle.js";

const loginForm = document.getElementById("login-form");

if (loginForm) {
  initPasswordToggle("password", "toggle-password");
}
