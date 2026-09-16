// admin/assets/biblioteca.js
//
// Dos cosas relacionadas con la Biblioteca de imágenes:
//
//   1. window.abrirBiblioteca(callback) — el selector que sale al pulsar
//      "Elegir de la biblioteca" en cualquier campo de imagen del panel.
//      Se carga en TODAS las pantallas (lo mete views/layout.php), porque
//      cualquier sección puede tener un campo de imagen.
//
//   2. La pantalla admin/biblioteca.php: arrastrar y soltar archivos.
//
// El selector nunca sube nada: solo devuelve la ruta de una imagen que YA
// está en la biblioteca. Subir es cosa de la pantalla de Biblioteca.

(function () {
    'use strict';

    const baseAdmin = document.body.dataset.adminBase || '/admin';

    /* ============================================================
       1. SELECTOR DE IMÁGENES
       ============================================================ */

    let overlay = null;      // el modal, se construye una sola vez
    let alElegir = null;     // callback del campo que lo abrió
    let imagenes = [];       // última lista traída del servidor

    function construirModal() {
        overlay = document.createElement('div');
        overlay.className = 'bib-picker-overlay';
        overlay.innerHTML = `
            <div class="bib-picker" role="dialog" aria-modal="true" aria-label="Elegir imagen de la biblioteca">
                <div class="bib-picker__head">
                    <h3><i class="bi bi-images"></i> Elegir de la biblioteca</h3>
                    <button type="button" class="bib-picker__cerrar" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="bib-picker__barra">
                    <input type="text" class="bib-picker__buscar form-input" placeholder="Buscar por nombre de archivo...">
                    <a href="${baseAdmin}/biblioteca.php" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                        <i class="bi bi-upload"></i> Subir imágenes
                    </a>
                </div>
                <div class="bib-picker__grid"></div>
                <div class="bib-picker__pie">
                    <span class="bib-picker__conteo"></span>
                    <button type="button" class="btn btn-outline bib-picker__cancelar">Cancelar</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        overlay.querySelector('.bib-picker__cerrar').addEventListener('click', cerrar);
        overlay.querySelector('.bib-picker__cancelar').addEventListener('click', cerrar);

        // Clic FUERA de la caja cierra; dentro, no. Sin esta comprobación,
        // cualquier clic en la rejilla cerraría el selector.
        overlay.addEventListener('click', e => { if (e.target === overlay) cerrar(); });

        overlay.querySelector('.bib-picker__buscar').addEventListener('input', function () {
            pintar(this.value.trim().toLowerCase());
        });

        // Delegación: las tarjetas se repintan al buscar, así que el listener
        // va en el contenedor y no en cada una.
        overlay.querySelector('.bib-picker__grid').addEventListener('click', function (e) {
            const card = e.target.closest('.bib-picker__item');
            if (!card) return;
            elegir(card.dataset.ruta, card.dataset.src);
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && overlay.classList.contains('is-visible')) cerrar();
        });
    }

    function pintar(filtro) {
        const grid = overlay.querySelector('.bib-picker__grid');
        const conteo = overlay.querySelector('.bib-picker__conteo');
        const lista = filtro
            ? imagenes.filter(i => i.nombre.toLowerCase().includes(filtro))
            : imagenes;

        if (!lista.length) {
            grid.innerHTML = `
                <p class="bib-picker__vacio">
                    ${imagenes.length
                        ? 'Ninguna imagen coincide con esa búsqueda.'
                        : 'La biblioteca está vacía. Sube imágenes desde Globales → Biblioteca.'}
                </p>`;
            conteo.textContent = '';
            return;
        }

        grid.innerHTML = lista.map(i => `
            <button type="button" class="bib-picker__item" data-ruta="${escapar(i.ruta)}" data-src="${escapar(i.src)}">
                <span class="bib-picker__thumb"><img src="${escapar(i.src)}" alt="" loading="lazy"></span>
                <span class="bib-picker__nombre" title="${escapar(i.nombre)}">${escapar(i.nombre)}</span>
                <span class="bib-picker__peso">${escapar(i.peso)}</span>
            </button>
        `).join('');

        conteo.textContent = lista.length + (lista.length === 1 ? ' imagen' : ' imágenes');
    }

    // Los nombres de archivo van dentro de atributos HTML: hay que escaparlos
    // o una comilla en el nombre rompe la tarjeta entera.
    function escapar(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[c]);
    }

    function cerrar() {
        if (!overlay) return;
        overlay.classList.remove('is-visible');
        setTimeout(() => overlay.classList.add('is-hidden'), 180);
        alElegir = null;
    }

    function elegir(ruta, src) {
        if (typeof alElegir === 'function') alElegir(ruta, src);
        cerrar();
    }

    window.abrirBiblioteca = function (callback) {
        if (!overlay) construirModal();
        alElegir = callback;

        overlay.classList.remove('is-hidden');
        requestAnimationFrame(() => overlay.classList.add('is-visible'));

        const buscar = overlay.querySelector('.bib-picker__buscar');
        buscar.value = '';
        overlay.querySelector('.bib-picker__grid').innerHTML =
            '<p class="bib-picker__vacio">Cargando imágenes…</p>';

        // Se pide la lista CADA vez que se abre, no una sola al cargar la
        // página: así una imagen recién subida en la otra pestaña ya aparece.
        fetch(baseAdmin + '/biblioteca.php?ajax=lista', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                imagenes = (data && data.items) ? data.items : [];
                pintar('');
                buscar.focus();
            })
            .catch(() => {
                overlay.querySelector('.bib-picker__grid').innerHTML =
                    '<p class="bib-picker__vacio">No se pudo cargar la biblioteca. Recarga la página e inténtalo otra vez.</p>';
            });
    };

    /* ============================================================
       2. PANTALLA DE LA BIBLIOTECA: arrastrar y soltar
       ============================================================ */

    const zona  = document.getElementById('biblioteca-dropzone');
    const input = document.getElementById('biblioteca-input');
    if (!zona || !input) return;

    const modal      = document.getElementById('biblioteca-modal');
    const btnAbrir   = document.getElementById('biblioteca-abrir-subida');
    const btnCerrar  = document.getElementById('biblioteca-modal-cerrar');
    const btnCancel  = document.getElementById('biblioteca-modal-cancelar');
    const btnEnviar  = document.getElementById('biblioteca-enviar');
    const previews   = document.getElementById('biblioteca-previews');
    const texto      = document.getElementById('biblioteca-seleccion-texto');

    // Lista propia de archivos elegidos. Hace falta para poder quitar uno:
    // input.files es de solo lectura, así que se rehace con un DataTransfer.
    let elegidos = [];

    const pesoLegible = (b) => {
        if (b >= 1048576) return (b / 1048576).toFixed(1).replace('.', ',') + ' MB';
        if (b >= 1024) return Math.round(b / 1024) + ' KB';
        return b + ' B';
    };

    /* ---- Ventana de subida ---- */

    let ultimoFoco = null;

    function abrirModal() {
        ultimoFoco = document.activeElement;
        modal.classList.remove('is-hidden');
        requestAnimationFrame(() => modal.classList.add('is-visible'));
        // El foco entra en la ventana; si no, seguiría detrás, en la página.
        (btnCerrar || modal).focus();
    }

    function cerrarModal() {
        modal.classList.remove('is-visible');
        setTimeout(() => modal.classList.add('is-hidden'), 200);
        // Devolver el foco a donde estaba: quien navega con teclado no se pierde.
        if (ultimoFoco) ultimoFoco.focus();
    }

    btnAbrir?.addEventListener('click', abrirModal);
    btnCerrar?.addEventListener('click', cerrarModal);
    btnCancel?.addEventListener('click', () => { limpiarSeleccion(); cerrarModal(); });
    modal?.addEventListener('click', (e) => { if (e.target === modal) cerrarModal(); });

    /* ---- Miniaturas de lo que se va a subir ---- */

    function sincronizarInput() {
        const dt = new DataTransfer();
        elegidos.forEach((f) => dt.items.add(f));
        input.files = dt.files;
    }

    function limpiarSeleccion() {
        elegidos = [];
        sincronizarInput();
        pintarPreviews();
    }

    function pintarPreviews() {
        previews.innerHTML = '';

        if (!elegidos.length) {
            previews.classList.add('is-hidden');
            texto.textContent = '';
            btnEnviar.disabled = true;
            return;
        }

        previews.classList.remove('is-hidden');
        btnEnviar.disabled = false;
        texto.textContent = elegidos.length === 1
            ? '1 imagen lista para subir'
            : `${elegidos.length} imágenes listas para subir`;

        elegidos.forEach((archivo, i) => {
            const item = document.createElement('div');
            item.className = 'biblioteca-preview';

            const img = document.createElement('img');
            // Se lee del archivo local, sin subir nada todavía.
            img.src = URL.createObjectURL(archivo);
            img.alt = archivo.name;
            // Liberar la memoria del objeto en cuanto el navegador lo pintó.
            img.addEventListener('load', () => URL.revokeObjectURL(img.src), { once: true });

            const pie = document.createElement('span');
            pie.className = 'biblioteca-preview__nombre';
            pie.textContent = archivo.name;
            pie.title = `${archivo.name} · ${pesoLegible(archivo.size)}`;

            const quitar = document.createElement('button');
            quitar.type = 'button';
            quitar.className = 'biblioteca-preview__quitar';
            quitar.innerHTML = '<i class="bi bi-x" aria-hidden="true"></i>';
            quitar.setAttribute('aria-label', `Quitar ${archivo.name} de la selección`);
            quitar.addEventListener('click', () => {
                elegidos.splice(i, 1);
                sincronizarInput();
                pintarPreviews();
            });

            item.append(img, pie, quitar);
            previews.appendChild(item);
        });
    }

    function añadirArchivos(lista) {
        // Solo imágenes, y sin repetir si se sueltan dos veces las mismas.
        Array.from(lista || []).forEach((f) => {
            const repetido = elegidos.some((e) => e.name === f.name && e.size === f.size);
            if (!repetido) elegidos.push(f);
        });
        sincronizarInput();
        pintarPreviews();
    }

    input.addEventListener('change', () => añadirArchivos(input.files));

    ['dragenter', 'dragover'].forEach(ev => {
        zona.addEventListener(ev, e => {
            e.preventDefault();
            zona.classList.add('is-encima');
        });
    });

    ['dragleave', 'drop'].forEach(ev => {
        zona.addEventListener(ev, e => {
            e.preventDefault();
            zona.classList.remove('is-encima');
        });
    });

    zona.addEventListener('drop', e => {
        if (!e.dataTransfer || !e.dataTransfer.files.length) return;
        añadirArchivos(e.dataTransfer.files);
    });

    // El recuadro entero abre el buscador de archivos, no solo el enlace.
    zona.addEventListener('click', e => {
        if (e.target.closest('label')) return; // el <label for> ya lo hace
        input.click();
    });

    /* ---- Visor: la imagen a tamaño completo ---- */

    const visor       = document.getElementById('biblioteca-visor');
    const visorImg    = document.getElementById('biblioteca-visor-img');
    const visorNombre = document.getElementById('biblioteca-visor-nombre');
    const visorMeta   = document.getElementById('biblioteca-visor-meta');
    const visorCerrar = document.getElementById('biblioteca-visor-cerrar');
    let focoAntesVisor = null;

    function abrirVisor(boton) {
        if (!visor) return;
        focoAntesVisor = boton;
        visorImg.src = boton.dataset.src;
        visorImg.alt = boton.dataset.nombre || '';
        visorNombre.textContent = boton.dataset.nombre || '';
        visorMeta.textContent = boton.dataset.meta || '';
        visor.classList.remove('is-hidden');
        requestAnimationFrame(() => visor.classList.add('is-visible'));
        visorCerrar?.focus();
    }

    function cerrarVisor() {
        if (!visor) return;
        visor.classList.remove('is-visible');
        setTimeout(() => {
            visor.classList.add('is-hidden');
            visorImg.src = '';
        }, 200);
        if (focoAntesVisor) focoAntesVisor.focus();
    }

    document.querySelectorAll('.js-ver-imagen').forEach((b) => {
        b.addEventListener('click', () => abrirVisor(b));
    });
    visorCerrar?.addEventListener('click', cerrarVisor);
    visor?.addEventListener('click', (e) => { if (e.target === visor) cerrarVisor(); });

    // Escape cierra la ventana que esté abierta, la de subir o la del visor.
    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        if (visor && !visor.classList.contains('is-hidden')) cerrarVisor();
        else if (modal && !modal.classList.contains('is-hidden')) cerrarModal();
    });
})();
