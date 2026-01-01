/**
 * Web Component for toast notifications using Bootstrap 5.
 */
class ToastNotification extends HTMLElement {
  constructor() {
    super();
    this.bsToast = null;
  }

  connectedCallback() {
    this.render();
    this.initBootstrap();
  }

  initBootstrap() {
    this.toastEl = this.querySelector(".toast");
    this.toastBody = this.querySelector(".toast-body");
    this.closeBtn = this.querySelector(".btn-close");

    if (window.bootstrap) {
      this.bsToast = new bootstrap.Toast(this.toastEl);
    } else {
      console.error("Bootstrap JS is not loaded.");
    }
  }

  render() {
    this.innerHTML = `
      <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>
    `;
  }

  /**
   * Shows the toast with a specific message and style.
   * @param {string} message
   * @param {string} type - 'success', 'danger', 'warning', 'info'
   */
  show(message, type = "info") {
    if (!this.bsToast) return;

    const bgClass = `bg-${type}`;
    const textColor = type === "warning" || type === "info" ? "text-dark" : "text-white";
    const btnWhite = textColor === "text-white" ? "btn-close-white" : "";

    this.toastEl.className = `toast align-items-center border-0 ${bgClass} ${textColor}`;
    this.closeBtn.className = `btn-close me-2 m-auto ${btnWhite}`;
    this.toastBody.textContent = message;

    this.bsToast.show();
  }
}

if (!customElements.get("toast-notification")) {
  customElements.define("toast-notification", ToastNotification);
}

export const notification = (message, type = "info") => {
  let toast = document.getElementById("toast-notification");
  if (!toast) {
    toast = document.createElement("toast-notification");
    toast.id = "toast-notification";
    document.body.appendChild(toast);
  }

  setTimeout(() => {
    toast.show(message, type);
  }, 0);
};
