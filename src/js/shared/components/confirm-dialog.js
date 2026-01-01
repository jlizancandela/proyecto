/**
 * Web Component for confirmation dialogs using the native <dialog> element.
 */
class ConfirmDialog extends HTMLElement {
  constructor() {
    super();
    this.dialog = null;
    this.resolvePromise = null;
  }

  connectedCallback() {
    this.render();
    this.dialog = this.querySelector("dialog");

    // Close on backdrop click (optional, but standard dialog behavior usually usually modal)
    this.dialog.addEventListener("click", (event) => {
      if (event.target === this.dialog) {
        this.close(false);
      }
    });

    this.querySelector(".btn-confirm").addEventListener("click", () => this.close(true));
    this.querySelector(".btn-cancel").addEventListener("click", () => this.close(false));
    this.querySelector(".btn-close-header").addEventListener("click", () => this.close(false));
  }

  render() {
    this.innerHTML = `
      <dialog class="border-0 rounded p-0 shadow-lg" style="min-width: 300px; max-width: 500px;">
        <div class="card border-0">
          <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-info-circle-fill me-2"></i>Confirmar Acción
            </h5>
            <button type="button" class="btn-close btn-close-header" aria-label="Close"></button>
          </div>
          <div class="card-body">
            <p class="confirm-message mb-0 text-secondary fs-6"></p>
          </div>
          <div class="card-footer bg-white border-top-0 d-flex justify-content-end gap-2 pb-3 pe-3">
            <button type="button" class="btn btn-secondary btn-cancel">Cancelar</button>
            <button type="button" class="btn btn-primary btn-confirm">Confirmar</button>
          </div>
        </div>
      </dialog>
    `;
  }

  open(message) {
    this.querySelector(".confirm-message").textContent = message;
    this.dialog.showModal();
    return new Promise((resolve) => {
      this.resolvePromise = resolve;
    });
  }

  close(result) {
    this.dialog.close();
    if (this.resolvePromise) {
      this.resolvePromise(result);
      this.resolvePromise = null;
    }
  }
}

if (!customElements.get("confirm-dialog")) {
  customElements.define("confirm-dialog", ConfirmDialog);
}

/**
 * Helper to show the confirmation dialog.
 * @param {string} message - The confirmation message.
 * @returns {Promise<boolean>} - Resolves to true if confirmed, false otherwise.
 */
export const confirmAction = (message) => {
  let dialog = document.querySelector("confirm-dialog");
  if (!dialog) {
    dialog = document.createElement("confirm-dialog");
    document.body.appendChild(dialog);
  }
  return dialog.open(message);
};
