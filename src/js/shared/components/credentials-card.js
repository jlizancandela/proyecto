class CredentialsCard extends HTMLElement {
  constructor() {
    super();
    this.STORAGE_KEY = 'sr_credentials_card_collapsed';
  }

  connectedCallback() {
    const isCollapsed = localStorage.getItem(this.STORAGE_KEY) === 'true';
    this.render(isCollapsed);
    this.attachEvents();
  }

  render(isCollapsed = false) {
    this.innerHTML = `
      <div class="credentials-card ${isCollapsed ? 'collapsed' : ''}">
        <button class="credentials-toggle" aria-label="${isCollapsed ? 'Mostrar' : 'Ocultar'}">
          ${isCollapsed ? '🔑' : '×'}
        </button>
        <div class="credentials-content">
          <div class="credentials-title">
            <i class="bi bi-key-fill me-2"></i>Credenciales de Prueba
          </div>
          <div class="credentials-list">
            <div class="credential-item">
              <span class="credential-role">👑 Admin:</span>
              <div class="credential-email">test+alberto.garcia@jorgelizancandela.com</div>
              <div class="credential-pass">Pelu123!</div>
            </div>
            <div class="credential-item">
              <span class="credential-role">✂️ Especialista:</span>
              <div class="credential-email">test+maria.fernandez@jorgelizancandela.com</div>
              <div class="credential-pass">Pelu123!</div>
            </div>
            <div class="credential-item">
              <span class="credential-role">👤 Cliente:</span>
              <div class="credential-email">test+fernando.alvarez@jorgelizancandela.com</div>
              <div class="credential-pass">Pelu123!</div>
            </div>
          </div>
        </div>
      </div>
      <style>${this.getStyles()}</style>
    `;
  }

  getStyles() {
    return `
      .credentials-card {
        position: fixed;
        bottom: 20px;
        right: 20px;
        max-width: 320px;
        background: #fff9c4;
        border: 1px solid #f9a825;
        border-radius: 4px;
        box-shadow: 4px 4px 10px rgba(0,0,0,0.15);
        padding: 16px;
        font-size: 13px;
        line-height: 1.5;
        transform: rotate(-2deg);
        z-index: 9999;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      }
      
      /* Estado colapsado - solo visible la esquina */
      .credentials-card.collapsed {
        max-width: 50px;
        padding: 8px;
        overflow: hidden;
      }
      
      .credentials-card.collapsed .credentials-content {
        opacity: 0;
        visibility: hidden;
        width: 0;
        height: 0;
      }
      
      .credentials-card:not(.collapsed) {
        animation: slideInRight 0.4s ease-out;
      }
      
      @keyframes slideInRight {
        from {
          opacity: 0;
          transform: translateX(100px) rotate(-2deg);
        }
        to {
          opacity: 1;
          transform: translateX(0) rotate(-2deg);
        }
      }
      
      .credentials-toggle {
        position: absolute;
        top: 4px;
        right: 8px;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #f57f17;
        line-height: 1;
        z-index: 10;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.2s;
      }
      
      .credentials-toggle:hover {
        background: rgba(245, 127, 23, 0.2);
      }
      
      .credentials-card.collapsed .credentials-toggle {
        position: relative;
        top: 0;
        right: 0;
        margin: 0 auto;
        display: flex;
        font-size: 18px;
      }
      
      .credentials-content {
        transition: opacity 0.2s, visibility 0.2s;
      }
      
      .credentials-title {
        font-weight: 600;
        color: #f57f17;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #f9a825;
      }
      
      .credentials-card.collapsed .credentials-title {
        display: none;
      }
      
      .credential-item {
        margin-bottom: 10px;
      }
      
      .credential-role {
        font-weight: 600;
        color: #424242;
      }
      
      .credential-email {
        color: #1565c0;
        word-break: break-all;
      }
      
      .credential-pass {
        font-family: monospace;
        background: rgba(0,0,0,0.05);
        padding: 2px 6px;
        border-radius: 3px;
        display: inline-block;
        margin-top: 2px;
      }
    `;
  }

  attachEvents() {
    const card = this.querySelector('.credentials-card');
    const toggleBtn = this.querySelector('.credentials-toggle');
    
    toggleBtn.addEventListener('click', () => {
      const isCollapsed = card.classList.contains('collapsed');
      
      if (isCollapsed) {
        // Expandir
        card.classList.remove('collapsed');
        localStorage.setItem(this.STORAGE_KEY, 'false');
      } else {
        // Colapsar
        card.classList.add('collapsed');
        localStorage.setItem(this.STORAGE_KEY, 'true');
      }
    });
  }
}

if (!customElements.get('credentials-card')) {
  customElements.define('credentials-card', CredentialsCard);
}
