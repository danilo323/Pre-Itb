/* =============================================
   MAIN.JS — Landing Page ITB
   ============================================= */

document.addEventListener('DOMContentLoaded', () => {

    // =========================================
    // 0. HERO SLIDESHOW — Ken Burns Effect
    // =========================================
    const slides = document.querySelectorAll('.hero__slide');
    if (slides.length > 1) {
        // Quitar clase init tras el primer frame (evita transición al cargar)
        requestAnimationFrame(() => slides[0].classList.remove('hero__slide--init'));

        let current = 0;
        let animating = false;
        const SLIDE_DURATION = 7000; // 7s por slide

        const goToSlide = (nextIndex) => {
            if (animating) return;
            animating = true;

            const outgoing = slides[current];
            current = nextIndex % slides.length;
            const incoming = slides[current];

            // Slide saliente: sube y sale por arriba
            outgoing.classList.add('hero__slide--leaving');

            // Slide entrante: entra desde abajo
            incoming.classList.add('hero__slide--active');

            // Reiniciar Ken Burns en el nuevo slide
            const img = incoming.querySelector('.hero__slide-img');
            img.style.animation = 'none';
            img.offsetHeight; // fuerza reflow
            img.style.animation = '';

            // Limpiar clases al terminar la transición
            outgoing.addEventListener('transitionend', () => {
                // Deshabilitar transición para que el reset sea instantáneo (sin animación visible)
                outgoing.style.transition = 'none';
                outgoing.classList.remove('hero__slide--active', 'hero__slide--leaving');
                outgoing.offsetHeight; // fuerza reflow
                outgoing.style.transition = ''; // restaurar transición
                animating = false;
            }, { once: true });
        };

        setInterval(() => {
            goToSlide(current + 1);
        }, SLIDE_DURATION);
    }

    // =========================================
    // 1. NAVBAR — Menú Hamburguesa (Mobile)
    // =========================================
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMenu = document.getElementById('navbar-menu');

    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', () => {
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');
            document.body.style.overflow = navbarMenu.classList.contains('active') ? 'hidden' : '';
        });

        document.addEventListener('click', (e) => {
            if (!navbarMenu.contains(e.target) && !navbarToggle.contains(e.target)) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // =========================================
    // 2. DROPDOWNS — Menú desplegable (Mobile)
    // =========================================
    const dropdownItems = document.querySelectorAll('.navbar__item--dropdown');

    dropdownItems.forEach(item => {
        const link = item.querySelector('.navbar__link');
        link.addEventListener('click', (e) => {
            if (window.innerWidth <= 1024) {
                e.preventDefault();
                item.classList.toggle('active');
                dropdownItems.forEach(other => {
                    if (other !== item) other.classList.remove('active');
                });
            }
        });
    });

    // =========================================
    // 3. SITE HEADER — Efecto scroll (sombra)
    // =========================================
    const siteHeader = document.getElementById('site-header');

    if (siteHeader) {
        window.addEventListener('scroll', () => {
            siteHeader.classList.toggle('site-header--scrolled', window.scrollY > 10);
        });
    }

    // =========================================
    // 4. CERRAR MENÚ al redimensionar ventana
    // =========================================
    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            if (navbarToggle) navbarToggle.classList.remove('active');
            if (navbarMenu) navbarMenu.classList.remove('active');
            document.body.style.overflow = '';
            dropdownItems.forEach(item => item.classList.remove('active'));
        }
    });

    // =========================================
    // 5. ANIMACIÓN — Scroll reveal (aparición)
    // =========================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Animar secciones al hacer scroll
    const animatedElements = document.querySelectorAll(
        '.trayectoria__content, .trayectoria__stats, ' +
        '.areas__card, ' +
        '.programas__card, ' +
        '.experiencia__content, .experiencia__image, ' +
        '.testimonios__image, .testimonios__content, ' +
        '.autoridades__card, ' +
        '.servicios__card, ' +
        '.admision__content, .admision__form-wrapper, ' +
        '.noticias__main, .noticias__item'
    );

    animatedElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });

    // =========================================
    // 6. FORMULARIO — Validación básica
    // =========================================
    const admisionForm = document.getElementById('admision-form');

    if (admisionForm) {
        admisionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            // Aquí puedes agregar tu lógica de envío (fetch, AJAX, etc.)
            const submitBtn = document.getElementById('admision-submit');
            submitBtn.innerHTML = '<i class="fas fa-check"></i> ¡Enviado con éxito!';
            submitBtn.style.background = '#27ae60';
            submitBtn.disabled = true;

            setTimeout(() => {
                submitBtn.innerHTML = 'Enviar Solicitud <i class="fas fa-paper-plane"></i>';
                submitBtn.style.background = '';
                submitBtn.disabled = false;
                admisionForm.reset();
            }, 3000);
        });
    }

    // =========================================
    // 7. VIDEO MODAL
    // =========================================
    const videoTriggers = document.querySelectorAll('.js-video-modal-trigger');
    const videoModal = document.getElementById('video-modal');
    const videoIframe = document.getElementById('video-modal-iframe');
    const videoClose = document.getElementById('video-modal-close');
    const videoOverlay = document.getElementById('video-modal-overlay');

    if (videoModal && videoIframe) {
        const closeModal = () => {
            videoModal.classList.remove('hero__video-modal--active');
            videoIframe.src = ''; // Detener el video al cerrar
            document.body.style.overflow = '';
        };

        videoTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const videoUrl = trigger.getAttribute('data-video-url');
                if (videoUrl) {
                    videoIframe.src = videoUrl;
                    videoModal.classList.add('hero__video-modal--active');
                    document.body.style.overflow = 'hidden'; // Evitar scroll de la pagina
                }
            });
        });

        if (videoClose) videoClose.addEventListener('click', closeModal);
        if (videoOverlay) videoOverlay.addEventListener('click', closeModal);
        
        // Cerrar con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && videoModal.classList.contains('hero__video-modal--active')) {
                closeModal();
            }
        });
    }

});

/* =============================================
   CSS para las animaciones de scroll
   (inyectado vía JS para mantenerlo junto)
   ============================================= */
const style = document.createElement('style');
style.textContent = `
    .animate-on-scroll {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1),
                    transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .animate-on-scroll.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Stagger delays para grids */
    .areas__card:nth-child(2).animate-on-scroll { transition-delay: 0.1s; }
    .areas__card:nth-child(3).animate-on-scroll { transition-delay: 0.2s; }

    .programas__card:nth-child(2).animate-on-scroll { transition-delay: 0.1s; }
    .programas__card:nth-child(3).animate-on-scroll { transition-delay: 0.2s; }
    .programas__card:nth-child(4).animate-on-scroll { transition-delay: 0.3s; }

    .autoridades__card:nth-child(2).animate-on-scroll { transition-delay: 0.1s; }
    .autoridades__card:nth-child(3).animate-on-scroll { transition-delay: 0.2s; }

    .servicios__card:nth-child(2).animate-on-scroll { transition-delay: 0.05s; }
    .servicios__card:nth-child(3).animate-on-scroll { transition-delay: 0.1s; }
    .servicios__card:nth-child(4).animate-on-scroll { transition-delay: 0.15s; }
    .servicios__card:nth-child(5).animate-on-scroll { transition-delay: 0.2s; }
    .servicios__card:nth-child(6).animate-on-scroll { transition-delay: 0.25s; }
`;
document.head.appendChild(style);
