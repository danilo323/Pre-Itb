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
        // Se anima la TARJETA de noticias secundarias entera, no cada noticia
        // suelta. Las noticias viven dentro de un carril que ya se mueve con su
        // propio transform, y esta animación usa transform también: las dos se
        // pisaban. Además el carrusel clona noticias al arrancar, y esos clones
        // nacían después de que el observador ya había pasado, así que se
        // quedaban con opacity 0 para siempre (huecos en blanco al rotar).
        '.noticias__main, .noticias__list'
    );

    animatedElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });

    // =========================================
    // 6. FORMULARIO DE ADMISIÓN — Validación
    // =========================================
    const admisionForm = document.getElementById('admision-form');

    if (admisionForm) {
        // Con 'novalidate' el navegador deja de mostrar sus propios globos y
        // pasamos a dar los mensajes nosotros: en español, uno por campo y con
        // el estilo del sitio. Los required/type del HTML se quedan puestos a
        // propósito: son la red de seguridad si este script no llega a cargar.
        admisionForm.setAttribute('novalidate', '');

        const submitBtn = admisionForm.querySelector('#admision-submit, .admision__submit-btn');
        const textoBotonOriginal = submitBtn ? submitBtn.innerHTML : '';

        const campo   = (n) => admisionForm.querySelector('[name="' + n + '"]');
        const valor   = (n) => { const el = campo(n); return el ? el.value.trim() : ''; };
        const marcado = (n) => { const el = admisionForm.querySelector('[name="' + n + '"]:checked'); return el ? el.value : ''; };
        const digitos = (t) => t.replace(/\D/g, '');
        const esExtranjero = () => marcado('nacionalidad') === 'extranjero';

        // Tope de nombres y apellidos. Va también en el maxlength del HTML y en
        // REGISTROS_MAX_NOMBRE de includes/registros.php: los tres tienen que
        // decir lo mismo.
        const MAX_NOMBRE = 25;

        // Letras (con tildes y ñ) unidas por un solo espacio, apóstrofo o guion.
        const RE_NOMBRE = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+(?:[ '\-][A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+)*$/;
        // Lo que se permite TECLEAR en esos dos campos. Es mas suelto que
        // RE_NOMBRE a proposito: mientras escribes pasas por estados que aun no
        // son un nombre valido ("Juan " con el espacio al final), y no tendria
        // sentido borrarte la tecla recien pulsada. Lo que se bloquea aqui son
        // los caracteres que NUNCA van a valer: numeros y simbolos.
        const RE_TECLA_NOMBRE = /[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ '\-]/g;
        // Comprobación de forma, no de existencia: algo@algo.tld y sin espacios.
        const RE_EMAIL = /^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/;

        // Cédula ecuatoriana: comprueba el dígito verificador con el algoritmo
        // módulo 10 del Registro Civil, no solo que sean 10 números. Así un
        // "0000000000" o un dígito cambiado por error de tipeo sí se detectan.
        function cedulaEcuatorianaValida(ced) {
            if (!/^\d{10}$/.test(ced)) return false;
            const provincia = parseInt(ced.slice(0, 2), 10);
            // 01-24 son las provincias; 30 es para nacidos en el exterior.
            if ((provincia < 1 || provincia > 24) && provincia !== 30) return false;
            // El tercer dígito indica el tipo: 0-5 = persona natural.
            if (parseInt(ced.charAt(2), 10) > 5) return false;
            let suma = 0;
            for (let i = 0; i < 9; i++) {
                let d = parseInt(ced.charAt(i), 10) * (i % 2 === 0 ? 2 : 1);
                if (d > 9) d -= 9;
                suma += d;
            }
            return (10 - (suma % 10)) % 10 === parseInt(ced.charAt(9), 10);
        }

        // Una regla por campo: devuelve el mensaje de error, o '' si está bien.
        // Teléfono y cédula cambian de regla según la nacionalidad elegida.
        const reglas = {
            nombre() {
                const v = valor('nombre');
                if (!v) return 'Escribe tus nombres.';
                if (v.length < 2) return 'Debe tener al menos 2 letras.';
                if (v.length > MAX_NOMBRE) return 'No puede superar los ' + MAX_NOMBRE + ' caracteres.';
                if (!RE_NOMBRE.test(v)) return 'Solo se permiten letras, espacios, apóstrofos y guiones.';
                return '';
            },
            apellido() {
                const v = valor('apellido');
                if (!v) return 'Escribe tus apellidos.';
                if (v.length < 2) return 'Debe tener al menos 2 letras.';
                if (v.length > MAX_NOMBRE) return 'No puede superar los ' + MAX_NOMBRE + ' caracteres.';
                if (!RE_NOMBRE.test(v)) return 'Solo se permiten letras, espacios, apóstrofos y guiones.';
                return '';
            },
            email() {
                const v = valor('email');
                if (!v) return 'Escribe tu correo electrónico.';
                if (v.length > 120) return 'El correo es demasiado largo.';
                if (!RE_EMAIL.test(v)) return 'El correo no tiene un formato válido (ejemplo@correo.com).';
                return '';
            },
            telefono() {
                const v = valor('telefono');
                if (!v) return 'Escribe tu celular o WhatsApp.';
                let d = digitos(v);
                if (!esExtranjero()) {
                    // Se acepta también con el prefijo del país: +593 99 123 4567.
                    if (d.indexOf('593') === 0) d = '0' + d.slice(3);
                    if (!/^09\d{8}$/.test(d)) {
                        return 'El celular debe tener 10 dígitos y empezar por 09 (ej. 0991234567).';
                    }
                    return '';
                }
                if (d.length < 7 || d.length > 15) {
                    return 'Escribe un número válido de 7 a 15 dígitos, con el código del país.';
                }
                return '';
            },
            cedula() {
                const v = valor('cedula');
                if (!v) {
                    return esExtranjero()
                        ? 'Escribe tu pasaporte o documento de identidad.'
                        : 'Escribe tu número de cédula.';
                }
                if (esExtranjero()) {
                    if (!/^[A-Za-z0-9\-]{5,20}$/.test(v)) {
                        return 'El documento debe tener de 5 a 20 caracteres (letras, números o guiones).';
                    }
                    return '';
                }
                const d = digitos(v);
                if (d.length !== 10) return 'La cédula debe tener exactamente 10 dígitos.';
                if (!cedulaEcuatorianaValida(d)) return 'La cédula no es válida. Revisa que no falte ni sobre un dígito.';
                return '';
            },
            nacionalidad() {
                return marcado('nacionalidad') ? '' : 'Selecciona tu nacionalidad.';
            },
            bachiller() {
                return marcado('bachiller') ? '' : 'Indica si eres bachiller.';
            },
            carrera() {
                return valor('carrera') ? '' : 'Selecciona un programa o área de interés.';
            },
            modalidad() {
                return valor('modalidad') ? '' : 'Selecciona la modalidad que prefieres.';
            },
            mensaje() {
                // Único campo opcional: solo se le controla el tamaño.
                return valor('mensaje').length > 500 ? 'El comentario no puede superar los 500 caracteres.' : '';
            }
        };

        // Los radios no tienen un input único al que apuntar, así que el aviso
        // se cuelga del .admision__form-group que los envuelve, igual que en
        // el resto de campos.
        function grupoDe(nombre) {
            const el = campo(nombre);
            return el ? el.closest('.admision__form-group') : null;
        }

        function pintarError(nombre, mensaje) {
            const grupo = grupoDe(nombre);
            if (!grupo) return;
            const control = campo(nombre);
            let aviso = grupo.querySelector('.admision__form-error');

            if (!mensaje) {
                grupo.classList.remove('is-invalid');
                if (aviso) aviso.remove();
                if (control) {
                    control.removeAttribute('aria-invalid');
                    control.removeAttribute('aria-describedby');
                }
                return;
            }

            if (!aviso) {
                aviso = document.createElement('p');
                aviso.className = 'admision__form-error';
                aviso.id = 'error-' + nombre;
                // role=alert hace que el lector de pantalla lo anuncie al aparecer.
                aviso.setAttribute('role', 'alert');
                grupo.appendChild(aviso);
            }
            aviso.textContent = mensaje;
            grupo.classList.add('is-invalid');
            if (control) {
                control.setAttribute('aria-invalid', 'true');
                control.setAttribute('aria-describedby', aviso.id);
            }
        }

        function validarCampo(nombre) {
            const mensaje = reglas[nombre] ? reglas[nombre]() : '';
            pintarError(nombre, mensaje);
            return !mensaje;
        }

        // En nombres y apellidos no se deja ni siquiera TECLEAR lo que no sea
        // una letra: si el usuario escribe un número o un símbolo, se descarta
        // en el momento en vez de dejarle terminar y luego marcarle el campo en
        // rojo. Se conserva la posición del cursor para poder seguir corrigiendo
        // en medio de la palabra sin que salte al final.
        ['nombre', 'apellido'].forEach((nombre) => {
            const control = campo(nombre);
            if (!control) return;
            control.addEventListener('input', () => {
                const original = control.value;
                const limpio = original.replace(RE_TECLA_NOMBRE, '').slice(0, MAX_NOMBRE);
                if (limpio === original) return;
                const cursor = control.selectionStart - (original.length - limpio.length);
                control.value = limpio;
                try {
                    control.setSelectionRange(cursor, cursor);
                } catch (e) {
                    // Algunos navegadores no dejan mover el cursor en ciertos
                    // tipos de input; no es crítico, el texto ya quedó limpio.
                }
            });
        });

        Object.keys(reglas).forEach((nombre) => {
            admisionForm.querySelectorAll('[name="' + nombre + '"]').forEach((control) => {
                const evento = (control.type === 'radio' || control.tagName === 'SELECT') ? 'change' : 'input';
                // Mientras se escribe NO se valida a la primera pulsación: sería
                // molesto ver "escribe tu correo" al teclear la primera letra.
                // Solo se revalida un campo YA marcado en rojo, para que el
                // error desaparezca en cuanto queda corregido.
                control.addEventListener(evento, () => {
                    const grupo = grupoDe(nombre);
                    if (grupo && grupo.classList.contains('is-invalid')) validarCampo(nombre);
                });
                // Al salir del campo sí se valida aunque no hubiera error previo:
                // ahí el usuario ya terminó de escribir.
                control.addEventListener('blur', () => {
                    if (control.value.trim() !== '') validarCampo(nombre);
                });
            });
        });

        // Cambiar de nacionalidad cambia las reglas de cédula y teléfono, así
        // que hay que volver a juzgarlos con la regla nueva; si no, un número
        // correcto podría quedarse marcado en rojo con la regla anterior.
        admisionForm.querySelectorAll('[name="nacionalidad"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                ['cedula', 'telefono'].forEach((n) => {
                    const grupo = grupoDe(n);
                    if (valor(n) !== '' || (grupo && grupo.classList.contains('is-invalid'))) validarCampo(n);
                });
            });
        });

        admisionForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // Se validan TODOS los campos, no se corta en el primer fallo: así
            // el usuario ve de una vez todo lo que tiene que corregir.
            const invalidos = Object.keys(reglas).filter((nombre) => !validarCampo(nombre));

            if (invalidos.length > 0) {
                const primero = campo(invalidos[0]);
                if (primero) {
                    // La cabecera es fija: sin restarla, el campo queda tapado
                    // justo detrás de ella al hacer scroll.
                    const alturaCabecera = parseInt(
                        getComputedStyle(document.documentElement).getPropertyValue('--header-height'), 10
                    ) || 120;
                    const y = primero.getBoundingClientRect().top + window.pageYOffset - alturaCabecera - 24;
                    window.scrollTo({ top: y, behavior: 'smooth' });
                    primero.focus({ preventScroll: true });
                }
                return;
            }

            // Todo correcto en el navegador: ahora se envía de verdad.
            // Antes esto solo pintaba "enviado" y tiraba los datos a la basura.
            if (!submitBtn) return;

            const restaurarBoton = () => {
                // Se restaura el texto ORIGINAL del botón, que es editable desde
                // el panel (Inicio → Admisión → Texto del botón).
                submitBtn.innerHTML = textoBotonOriginal;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
            };

            submitBtn.innerHTML = 'Enviando…';
            submitBtn.disabled = true;

            fetch(admisionForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(admisionForm),
                headers: { 'Accept': 'application/json' }
            })
                .then((respuesta) => respuesta.json().then((cuerpo) => ({ ok: respuesta.ok, cuerpo })))
                .then(({ ok, cuerpo }) => {
                    if (!ok) {
                        // El servidor valida otra vez por su cuenta. Si rechaza
                        // algo que aquí había pasado, se pintan sus mensajes en
                        // los campos correspondientes.
                        const errores = (cuerpo && cuerpo.errores) || {};
                        Object.keys(errores).forEach((n) => pintarError(n, errores[n]));
                        const primerCampo = campo(Object.keys(errores)[0]);
                        if (primerCampo) primerCampo.focus();
                        restaurarBoton();
                        return;
                    }

                    submitBtn.innerHTML = '<i class="fas fa-check"></i> ¡Enviado con éxito!';
                    submitBtn.style.background = '#27ae60';
                    setTimeout(() => {
                        restaurarBoton();
                        admisionForm.reset();
                        Object.keys(reglas).forEach((nombre) => pintarError(nombre, ''));
                    }, 3000);
                })
                .catch(() => {
                    // Sin conexión, o el servidor devolvió algo que no es JSON.
                    submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> No se pudo enviar';
                    submitBtn.style.background = '#d93025';
                    setTimeout(restaurarBoton, 3000);
                });
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

    // =========================================
    // 8. JARALLAX — Efecto de imagen que se desplaza al hacer scroll.
    //    Se activa solo con el atributo data-jarallax en el HTML (Trayectoria,
    //    Tu Experiencia ITB, Historias de Éxito, Nuestras Autoridades y el
    //    formulario de Admisión), así que agregarlo a una seccion nueva no
    //    necesita tocar este archivo.
    // =========================================
    if (typeof jarallax !== 'undefined') {
        jarallax(document.querySelectorAll('[data-jarallax]'), {
            speed: 0.5
        });
    }

    // =========================================
    // 9. CONTADOR ANIMADO — CountUp.js (Elementor-style)
    // =========================================
    const statNumbers = document.querySelectorAll('.trayectoria__stat-number[data-count]');

    if (statNumbers.length && typeof countUp !== 'undefined') {
        const CountUp = countUp.CountUp;

        const counterObserver = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-count'), 10);
                    const suffix = el.getAttribute('data-suffix') || '';
                    const prefix = el.getAttribute('data-prefix') || '';
                    const useGrouping = el.getAttribute('data-format') === 'thousands';

                    const counter = new CountUp(el, target, {
                        duration: 1.5,       // 1.5 segundos como Elementor
                        separator: ',',       // Separador de miles
                        prefix: prefix,
                        suffix: suffix,
                        useGrouping: useGrouping,
                        useEasing: true,      // Aceleración suave
                    });

                    if (!counter.error) {
                        counter.start();
                    }
                    obs.unobserve(el);
                }
            });
        }, { threshold: 0.2 });

        statNumbers.forEach(el => counterObserver.observe(el));
    }

    // =========================================
    // 10. NOTICIAS — Carruseles de Eventos y Secundarias
    // =========================================
    // Dos carruseles con el mismo espíritu pero distinto eje:
    //   - Eventos: horizontal, una tarjeta a la vez, con puntos para saltar.
    //   - Secundarias: vertical, se ven dos y bajan de a una (entra una nueva
    //     por arriba, sale la de abajo).
    //
    // El bucle infinito de la vertical se hace clonando al principio las
    // últimas noticias: cuando el carril llega arriba del todo, lo que se ve son
    // esos clones, idénticos a las últimas, así que se puede saltar al otro
    // extremo de golpe (sin transición) sin que se note nada.

    // OJO: aquí había una comprobación de 'prefers-reduced-motion' que dejaba
    // los dos carruseles quietos cuando el sistema pide reducir el movimiento.
    // Se quitó a propósito: Windows trae las animaciones desactivadas en muchos
    // equipos (Configuración → Accesibilidad → Efectos visuales), y en esos
    // navegadores la sección se veía completamente congelada, que es justo lo
    // contrario de lo que se pide de ella. El movimiento automático es el
    // objetivo de estos carruseles, no un adorno, así que ahora giran siempre.
    // Para quien no quiera que se muevan queda la salida de siempre: pasar el
    // puntero por encima los detiene.

    // ---- 10a. Eventos: carrusel horizontal ----
    const carruselEventos = document.querySelector('.js-carrusel-eventos');

    if (carruselEventos) {
        const pista   = carruselEventos.querySelector('.noticias__main-track');
        const eventos = pista ? Array.from(pista.children) : [];
        const puntos  = Array.from(carruselEventos.querySelectorAll('.noticias__dot'));
        const ESPERA_EVENTOS = 6000;

        if (pista && eventos.length > 1) {
            let actual = 0;
            let reloj = null;

            const mostrar = (n) => {
                actual = (n + eventos.length) % eventos.length;
                pista.style.transform = 'translateX(-' + (actual * 100) + '%)';
                eventos.forEach((ev, i) => ev.setAttribute('aria-hidden', i === actual ? 'false' : 'true'));
                puntos.forEach((p, i) => {
                    p.classList.toggle('is-active', i === actual);
                    p.setAttribute('aria-selected', i === actual ? 'true' : 'false');
                });
            };

            const arrancar = () => {
                if (reloj) return;
                reloj = setInterval(() => mostrar(actual + 1), ESPERA_EVENTOS);
            };
            const parar = () => { clearInterval(reloj); reloj = null; };

            puntos.forEach((punto) => {
                punto.addEventListener('click', () => {
                    mostrar(parseInt(punto.dataset.indice, 10) || 0);
                    // Se reinicia la cuenta para que el evento recién elegido
                    // se pueda leer entero y no salte medio segundo después.
                    parar();
                    arrancar();
                });
            });

            // Mientras el puntero está encima el carrusel se detiene: nadie
            // quiere que la tarjeta cambie justo cuando iba a hacer clic.
            carruselEventos.addEventListener('mouseenter', parar);
            carruselEventos.addEventListener('mouseleave', arrancar);
            // Lo mismo con el teclado: si el foco está dentro, no se mueve.
            carruselEventos.addEventListener('focusin', parar);
            carruselEventos.addEventListener('focusout', arrancar);

            mostrar(0);
            arrancar();
        }
    }

    // ---- 10b. Noticias secundarias: carrusel vertical ----
    const carruselNoticias = document.querySelector('.js-carrusel-noticias');

    if (carruselNoticias) {
        const marco = carruselNoticias.querySelector('.noticias__list-viewport');
        const carril = carruselNoticias.querySelector('.noticias__list-track');
        const originales = carril ? Array.from(carril.children) : [];
        const visibles = parseInt(carruselNoticias.dataset.visibles, 10) || 2;
        const ESPERA_NOTICIAS = 4500;
        const DURACION = 600; // debe coincidir con la transición del CSS

        // Con tantas noticias como huecos (o menos) no hay nada que rotar:
        // se quedan quietas y el carrusel ni se monta.
        if (marco && carril && originales.length > visibles) {
            // El carril baja, así que las noticias nuevas tienen que entrar POR
            // ARRIBA. Por eso los clones son de las ÚLTIMAS noticias y se ponen
            // al PRINCIPIO: son las que asoman al bajar, y como se ven idénticas
            // a las de verdad permiten saltar al otro extremo sin que se note.
            //
            //   carril = [ clon(N-2), clon(N-1) , 0, 1, 2, ... , N-1 ]
            //              \___ los clones ___/   \__ las de verdad __/
            const clones = originales.slice(-visibles).map((item) => {
                const copia = item.cloneNode(true);
                copia.setAttribute('aria-hidden', 'true');
                // Por si alguien vuelve a meter .noticias__item en la lista de
                // elementos con animación de aparición: un clon creado ahora ya
                // no lo verá el observador, así que se le quita el estado de
                // "todavía sin aparecer" para que no nazca invisible.
                copia.classList.remove('animate-on-scroll');
                copia.classList.add('is-visible');
                return copia;
            });
            carril.prepend.apply(carril, clones);

            // 'paso' = cuántas noticias quedan por encima del marco. Arranca en
            // 'visibles' para que lo que se vea de entrada sean las primeras de
            // verdad y los clones queden escondidos justo encima.
            let paso = visibles;
            let alto = 0;
            let reloj = null;

            const colocar = (conTransicion) => {
                if (!conTransicion) carril.style.transition = 'none';
                carril.style.transform = 'translateY(-' + (paso * alto) + 'px)';
                if (!conTransicion) {
                    void carril.offsetHeight; // fuerza el reflujo antes de devolver la transición
                    carril.style.transition = '';
                }
            };

            const medir = () => {
                alto = originales[0].getBoundingClientRect().height;
                marco.style.height = (alto * visibles) + 'px';
                // Recolocar el carril con el alto nuevo, si no queda descuadrado
                // tras un cambio de tamaño de ventana.
                colocar(false);
            };

            const avanzar = () => {
                // Restar hace BAJAR el carril: la noticia de arriba entra y la
                // de abajo sale, que es justo el sentido que se busca.
                paso--;
                colocar(true);

                if (paso <= 0) {
                    // Arriba del todo: lo que se ve son los clones de las
                    // últimas. En cuanto termina la animación se salta a la
                    // posición equivalente pero con las noticias de verdad
                    // (las últimas de la lista), sin transición. Se ve lo mismo,
                    // así que el salto es invisible y queda sitio para seguir
                    // bajando otra vuelta entera.
                    setTimeout(() => {
                        paso = originales.length;
                        colocar(false);
                    }, DURACION);
                }
            };

            const arrancar = () => {
                if (reloj) return;
                reloj = setInterval(avanzar, ESPERA_NOTICIAS);
            };
            const parar = () => { clearInterval(reloj); reloj = null; };

            carruselNoticias.addEventListener('mouseenter', parar);
            carruselNoticias.addEventListener('mouseleave', arrancar);
            carruselNoticias.addEventListener('focusin', parar);
            carruselNoticias.addEventListener('focusout', arrancar);

            medir();
            // Al cambiar el ancho cambia el alto de las noticias (el título pasa
            // de una línea a dos, por ejemplo), así que hay que volver a medir.
            let esperaResize = null;
            window.addEventListener('resize', () => {
                clearTimeout(esperaResize);
                esperaResize = setTimeout(medir, 150);
            });

            arrancar();
        }
    }

    // =========================================
    // 11. PROGRAMAS — Carrusel de la cuarta tarjeta
    // =========================================
    // Las tres primeras tarjetas llevan un programa fijo cada una. La cuarta se
    // queda con el cuarto programa y con todos los que se agreguen después, y
    // los va pasando de arriba hacia abajo, igual que las noticias secundarias:
    // los clones van al PRINCIPIO y el carril baja.
    const carruselProgramas = document.querySelector('.js-carrusel-programas');

    if (carruselProgramas) {
        const marco = carruselProgramas.querySelector('.programas__card-viewport');
        const carril = carruselProgramas.querySelector('.programas__card-track');
        const originales = carril ? Array.from(carril.children) : [];
        const ESPERA_PROGRAMAS = 5000;
        const DURACION_PROG = 600; // debe coincidir con la transición del CSS

        if (marco && carril && originales.length) {
            // Se ve un programa a la vez: con uno solo no hay nada que rotar,
            // pero el resto del montaje SÍ tiene que correr igual. Medir el alto
            // de la tarjeta no es cosa del giro, es lo que evita que el
            // contenido quede cortado por el overflow del CSS.
            const rota = originales.length > 1;

            if (rota) {
                const copia = originales[originales.length - 1].cloneNode(true);
                copia.setAttribute('aria-hidden', 'true');
                copia.classList.remove('animate-on-scroll');
                copia.classList.add('is-visible');
                carril.prepend(copia);
            }

            // Con clon delante se arranca en 1 (el clon queda escondido encima);
            // sin clon, la única diapositiva ya está en su sitio.
            let paso = rota ? 1 : 0;
            let alto = 0;
            let reloj = null;

            const colocar = (conTransicion) => {
                if (!conTransicion) carril.style.transition = 'none';
                carril.style.transform = 'translateY(-' + (paso * alto) + 'px)';
                if (!conTransicion) {
                    void carril.offsetHeight;
                    carril.style.transition = '';
                }
            };

            const medir = () => {
                // El alto de la tarjeta lo marcan las otras tres de la fila (el
                // marco va en absoluto y no aporta alto propio). Pero si algún
                // programa necesita MÁS de lo que dan las otras, su contenido
                // quedaría cortado: se mide lo que de verdad ocupa y, si hace
                // falta, se empuja la fila entera con un min-height.
                carruselProgramas.style.minHeight = '';
                let disponible = marco.getBoundingClientRect().height;

                let necesario = 0;
                originales.forEach((s) => {
                    necesario = Math.max(necesario, s.scrollHeight);
                });
                if (necesario > disponible) {
                    carruselProgramas.style.minHeight = necesario + 'px';
                    disponible = marco.getBoundingClientRect().height;
                }

                // El alto de cada diapositiva lo resuelve el CSS con un 100%
                // sobre el marco; aquí solo hace falta el número para saber
                // cuánto desplazar el carril en cada paso.
                alto = disponible;
                colocar(false);
            };

            const avanzar = () => {
                // Restar hace BAJAR el carril: el programa de arriba entra y el
                // que se veía sale por abajo.
                paso--;
                colocar(true);

                if (paso <= 0) {
                    // Arriba del todo se ve el clon del último. Al terminar la
                    // animación se salta a la posición equivalente pero con el
                    // programa de verdad, sin transición: se ve lo mismo, así
                    // que el salto no se nota.
                    setTimeout(() => {
                        paso = originales.length;
                        colocar(false);
                    }, DURACION_PROG);
                }
            };

            const arrancarProg = () => {
                // Sin comprobar 'prefers-reduced-motion', por el mismo motivo
                // explicado arriba en la sección 10: en muchos equipos Windows
                // esa preferencia viene activada de fábrica y la tarjeta se
                // quedaría congelada, que es justo lo contrario de lo que se
                // le pide. Quien no quiera que se mueva, pasa el puntero por
                // encima y se detiene.
                if (reloj) return;
                reloj = setInterval(avanzar, ESPERA_PROGRAMAS);
            };
            const pararProg = () => { clearInterval(reloj); reloj = null; };

            // Medir SIEMPRE, haya uno o diez programas: es lo que ajusta el alto
            // de la fila cuando un programa necesita más espacio del que dan las
            // otras tres tarjetas.
            medir();
            let esperaResizeProg = null;
            window.addEventListener('resize', () => {
                clearTimeout(esperaResizeProg);
                esperaResizeProg = setTimeout(medir, 150);
            });

            // Lo que sí depende de que haya más de uno es el giro.
            if (rota) {
                carruselProgramas.addEventListener('mouseenter', pararProg);
                carruselProgramas.addEventListener('mouseleave', arrancarProg);
                carruselProgramas.addEventListener('focusin', pararProg);
                carruselProgramas.addEventListener('focusout', arrancarProg);
                arrancarProg();
            }
        }
    }

    // =========================================
    // 12. PIE DE PÁGINA — Mapa de campus
    // =========================================
    // Al pulsar un campus de la columna del pie, el mapa de al lado se mueve
    // hasta él y debajo aparece su dirección. Cada botón trae ya puesta su
    // dirección y su enlace de mapa desde el panel (includes/footer.php), así
    // que aquí no hay ninguna dirección escrita a mano.
    const botonesCampus = document.querySelectorAll('.js-campus');
    const mapaFooter = document.getElementById('footer-map');
    const direccionFooter = document.getElementById('footer-map-direccion');

    if (botonesCampus.length && mapaFooter) {
        botonesCampus.forEach((boton) => {
            boton.addEventListener('click', () => {
                const url = boton.dataset.mapa;
                if (!url) return;

                // Si ya se está viendo ese campus no se recarga el mapa: volver a
                // asignar el mismo src lo haría parpadear sin motivo.
                if (mapaFooter.getAttribute('src') !== url) {
                    mapaFooter.setAttribute('src', url);
                }
                if (direccionFooter) {
                    direccionFooter.textContent = boton.dataset.direccion || '';
                }

                botonesCampus.forEach((otro) => {
                    const activo = (otro === boton);
                    otro.classList.toggle('is-active', activo);
                    otro.setAttribute('aria-pressed', activo ? 'true' : 'false');
                });
            });
        });
    }

    // =========================================
    // 13. TESTIMONIOS — Carrusel de historias de éxito
    // =========================================
    // Se ve una historia a la vez y pasan de izquierda a derecha.
    //
    // Hay DOS carriles, no uno: la foto está en la columna izquierda de la
    // rejilla y la cita en la derecha, con el título de la sección en medio.
    // Se mueven juntos al mismo índice para que foto y texto siempre
    // correspondan, y así el título se queda quieto en su sitio.
    const carruselTestimonios = document.querySelector('.js-carrusel-testimonios');

    if (carruselTestimonios) {
        const carrilFoto  = carruselTestimonios.querySelector('.testimonios__photo-track');
        const carrilCita  = carruselTestimonios.querySelector('.testimonios__quote-track');
        const fotos       = carrilFoto ? Array.from(carrilFoto.children) : [];
        const citas       = carrilCita ? Array.from(carrilCita.children) : [];
        const puntosTest  = Array.from(carruselTestimonios.querySelectorAll('.testimonios__dot'));
        const ESPERA_TESTIMONIOS = 7000; // son textos largos: hay que dar tiempo a leerlos

        if (carrilFoto && carrilCita && fotos.length > 1 && fotos.length === citas.length) {
            let actualTest = 0;
            let relojTest = null;

            const mostrarTest = (n) => {
                actualTest = (n + fotos.length) % fotos.length;
                const desplazamiento = 'translateX(-' + (actualTest * 100) + '%)';
                carrilFoto.style.transform = desplazamiento;
                carrilCita.style.transform = desplazamiento;

                fotos.forEach((f, i) => f.setAttribute('aria-hidden', i === actualTest ? 'false' : 'true'));
                citas.forEach((c, i) => c.setAttribute('aria-hidden', i === actualTest ? 'false' : 'true'));
                puntosTest.forEach((p, i) => {
                    p.classList.toggle('is-active', i === actualTest);
                    p.setAttribute('aria-selected', i === actualTest ? 'true' : 'false');
                });
            };

            const arrancarTest = () => {
                // Sin comprobar 'prefers-reduced-motion', igual que los demás
                // carruseles de la página (ver el comentario de la sección 10).
                if (relojTest) return;
                relojTest = setInterval(() => mostrarTest(actualTest + 1), ESPERA_TESTIMONIOS);
            };
            const pararTest = () => { clearInterval(relojTest); relojTest = null; };

            puntosTest.forEach((punto) => {
                punto.addEventListener('click', () => {
                    mostrarTest(parseInt(punto.dataset.indice, 10) || 0);
                    // Se reinicia la cuenta para que la historia recién elegida
                    // se pueda leer entera y no salte al segundo siguiente.
                    pararTest();
                    arrancarTest();
                });
            });

            // Mientras se está leyendo (puntero encima o foco dentro), no cambia.
            carruselTestimonios.addEventListener('mouseenter', pararTest);
            carruselTestimonios.addEventListener('mouseleave', arrancarTest);
            carruselTestimonios.addEventListener('focusin', pararTest);
            carruselTestimonios.addEventListener('focusout', arrancarTest);

            mostrarTest(0);
            arrancarTest();
        }
    }

    // =========================================
    // 14. BOTÓN IR ARRIBA (Scroll to top)
    // =========================================
    const scrollToTopBtn = document.getElementById('top-to-bottom');

    if (scrollToTopBtn) {
        // Mostrar u ocultar el botón al hacer scroll
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.classList.add('scroll_visible');
            } else {
                scrollToTopBtn.classList.remove('scroll_visible');
            }
        });

        // Animación suave al hacer click
        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
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

/* =============================================
   BOTONES FLOTANTES: se apartan al llegar al pie
   Al final de la pagina el propio pie ya tiene los telefonos, las redes y los
   enlaces, asi que las burbujas solo tapaban contenido. Se usa
   IntersectionObserver y no el evento 'scroll' para no recalcular posiciones en
   cada pixel de desplazamiento.
   ============================================= */
document.addEventListener('DOMContentLoaded', () => {
    const pie = document.getElementById('footer');
    const flotantes = document.querySelectorAll('.js-floating');

    if (!pie || !flotantes.length || !('IntersectionObserver' in window)) return;

    const observador = new IntersectionObserver((entradas) => {
        entradas.forEach((entrada) => {
            flotantes.forEach((el) => {
                el.classList.toggle('floating--oculto', entrada.isIntersecting);
            });
        });
    }, { rootMargin: '0px 0px -40% 0px' });

    observador.observe(pie);
});
