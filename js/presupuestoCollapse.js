/**
 * Presupuesto Collapse Module
 * Clean, modular, vanilla JS
 * Handles chapter collapse/expand functionality
 */

class PresupuestoCollapse {
    constructor() {
        this.capitulos = [];
        this.allCollapsed = false;
        this.init();
    }

    /**
     * Initialize module
     */
    init() {
        this.setupCapitulos();
        this.setupToggleAllButton();
        this.setupStickyHeader();
        this.restoreState();
    }

    /**
     * Setup individual chapter collapse handlers
     */
    setupCapitulos() {
        const headers = document.querySelectorAll('.capitulo-header');

        headers.forEach((header, index) => {
            const content = header.nextElementSibling;
            const capituloId = header.getAttribute('data-capitulo-id');

            this.capitulos.push({
                header,
                content,
                id: capituloId,
                collapsed: false
            });

            // Click handler
            header.addEventListener('click', (e) => {
                // Evitar toggle si se hace clic en el botón de eliminar
                if (e.target.closest('.btn-outline-danger')) return;
                this.toggleCapitulo(index);
            });

            // Keyboard accessibility
            header.setAttribute('tabindex', '0');
            header.setAttribute('role', 'button');
            header.setAttribute('aria-expanded', 'true');

            header.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleCapitulo(index);
                }
            });
        });
    }

    /**
     * Toggle individual chapter
     */
    toggleCapitulo(index) {
        const capitulo = this.capitulos[index];
        const isCollapsed = capitulo.collapsed;

        if (isCollapsed) {
            this.expandCapitulo(index);
        } else {
            this.collapseCapitulo(index);
        }

        this.saveState();
    }

    /**
     * Collapse chapter
     */
    collapseCapitulo(index) {
        const capitulo = this.capitulos[index];

        capitulo.header.classList.add('collapsed');
        capitulo.content.classList.add('collapsed');
        capitulo.header.setAttribute('aria-expanded', 'false');
        capitulo.collapsed = true;
    }

    /**
     * Expand chapter
     */
    expandCapitulo(index) {
        const capitulo = this.capitulos[index];

        capitulo.header.classList.remove('collapsed');
        capitulo.content.classList.remove('collapsed');
        capitulo.header.setAttribute('aria-expanded', 'true');
        capitulo.collapsed = false;
    }

    /**
     * Setup toggle all button
     */
    setupToggleAllButton() {
        const toggleBtn = document.getElementById('toggleAllCapitulos');
        if (!toggleBtn) return;

        toggleBtn.addEventListener('click', () => {
            this.toggleAll();
        });
    }

    /**
     * Toggle all chapters
     */
    toggleAll() {
        const allExpanded = this.capitulos.every(c => !c.collapsed);

        this.capitulos.forEach((capitulo, index) => {
            if (allExpanded) {
                this.collapseCapitulo(index);
            } else {
                this.expandCapitulo(index);
            }
        });

        this.updateToggleAllButton();
        this.saveState();
    }

    /**
     * Update toggle all button text
     */
    updateToggleAllButton() {
        const toggleBtn = document.getElementById('toggleAllCapitulos');
        if (!toggleBtn) return;

        const allExpanded = this.capitulos.every(c => !c.collapsed);
        const icon = toggleBtn.querySelector('i');
        const text = toggleBtn.querySelector('.btn-text');

        if (allExpanded) {
            icon.className = 'bi bi-arrows-collapse';
            text.textContent = 'Colapsar todo';
        } else {
            icon.className = 'bi bi-arrows-expand';
            text.textContent = 'Expandir todo';
        }
    }

    /**
     * Setup sticky header behavior
     */
    setupStickyHeader() {
        const stickyHeader = document.querySelector('.presupuesto-sticky-summary');
        if (!stickyHeader) return;

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 100) {
                stickyHeader.classList.add('scrolled');
            } else {
                stickyHeader.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    /**
     * Save collapse state to localStorage
     */
    saveState() {
        const state = this.capitulos.map(c => ({
            id: c.id,
            collapsed: c.collapsed
        }));

        localStorage.setItem('presupuesto_collapse_state', JSON.stringify(state));
    }

    /**
     * Restore collapse state from localStorage
     */
    restoreState() {
        const savedState = localStorage.getItem('presupuesto_collapse_state');
        if (!savedState) return;

        try {
            const state = JSON.parse(savedState);

            state.forEach(saved => {
                const capituloIndex = this.capitulos.findIndex(c => c.id === saved.id);
                if (capituloIndex !== -1 && saved.collapsed) {
                    this.collapseCapitulo(capituloIndex);
                }
            });

            this.updateToggleAllButton();
        } catch (e) {
            console.warn('Error restoring collapse state:', e);
        }
    }

    /**
     * Clear saved state
     */
    clearState() {
        localStorage.removeItem('presupuesto_collapse_state');
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.presupuestoCollapse = new PresupuestoCollapse();
});
