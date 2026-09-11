/* =============================================
   SOBRE-NOSOTROS.JS — Lógica Específica
   PROPÓSITO: Este script SOLO se carga en la página "Sobre Nosotros" (sobre-nosotros.php).
   Tenerlo separado de main.js garantiza que la memoria y red no se desperdicien en otras páginas (Buena práctica de Escalabilidad).
   Maneja: Modales de Co-Gobierno, Animaciones de Scroll Reveal y micro-interacciones.
   ============================================= */

document.addEventListener('DOMContentLoaded', () => {

    // =========================================
    // 1. MODAL / DETALLE DE CO-GOBIERNO
    // =========================================
    const initCogobModal = () => {
        const plusButtons = document.querySelectorAll('.sobre-cogob__plus');
        if (!plusButtons.length) return;

        // Crear contenedor modal si no existe en el DOM
        let modalOverlay = document.querySelector('.cogob-modal-overlay');
        if (!modalOverlay) {
            modalOverlay = document.createElement('div');
            modalOverlay.className = 'cogob-modal-overlay';
            modalOverlay.innerHTML = `
                <div class="cogob-modal" role="dialog" aria-modal="true" aria-labelledby="cogob-modal-title">
                    <button type="button" class="cogob-modal__close" aria-label="Cerrar">&times;</button>
                    <img src="" alt="" class="cogob-modal__photo">
                    <h3 id="cogob-modal-title" class="cogob-modal__name"></h3>
                    <span class="cogob-modal__role"></span>
                    <div class="cogob-modal__badge">Órgano Colegiado Superior &bull; ITB</div>
                </div>
            `;
            document.body.appendChild(modalOverlay);
        }

        const modalPhoto = modalOverlay.querySelector('.cogob-modal__photo');
        const modalName  = modalOverlay.querySelector('.cogob-modal__name');
        const modalRole  = modalOverlay.querySelector('.cogob-modal__role');
        const closeBtn   = modalOverlay.querySelector('.cogob-modal__close');

        const openModal = (nombre, cargo, foto) => {
            modalName.textContent = nombre;
            modalRole.textContent = cargo;
            modalPhoto.src = foto || 'img/placeholder_autoridad.svg';
            modalPhoto.alt = nombre;
            modalOverlay.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        };

        const closeModal = () => {
            modalOverlay.classList.remove('is-active');
            document.body.style.overflow = '';
        };

        plusButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const nombre = btn.dataset.nombre || btn.getAttribute('aria-label') || 'Representante';
                const cargo  = btn.dataset.cargo || '';
                const foto   = btn.dataset.foto || '';
                openModal(nombre, cargo, foto);
            });
        });

        // Click en botón de cerrar
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }

        // Click en fondo oscuro para cerrar
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        // Tecla escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modalOverlay.classList.contains('is-active')) {
                closeModal();
            }
        });
    };

    // =========================================
    // 2. SCROLL REVEAL SUAVE PARA SECCIONES
    // =========================================
    const initScrollAnimations = () => {
        if (!('IntersectionObserver' in window)) return;

        const animTargets = document.querySelectorAll(
            '.sobre-intro__content, .sobre-intro__image-wrapper, .sobre-mv__card, .sobre-valores__item, .sobre-cogob__card, .sobre-cogob__banner'
        );

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        animTargets.forEach(el => observer.observe(el));
    };

    // =========================================
    // 3. MICRO-INTERACCIÓN BADGE TRAYECTORIA
    // =========================================
    const initBadgeEffect = () => {
        const badge = document.querySelector('.sobre-intro__badge');
        if (!badge) return;

        badge.addEventListener('mouseenter', () => {
            badge.style.transform = 'translateY(-4px) scale(1.02)';
            badge.style.transition = 'transform 0.25s ease, box-shadow 0.25s ease';
            badge.style.boxShadow = '0 16px 36px rgba(0, 0, 0, 0.22)';
        });

        badge.addEventListener('mouseleave', () => {
            badge.style.transform = 'translateY(0) scale(1)';
            badge.style.boxShadow = '0 12px 32px rgba(0, 0, 0, 0.16)';
        });
    };

    // Ejecutar inicializaciones
    initCogobModal();
    initScrollAnimations();
    initBadgeEffect();

});
