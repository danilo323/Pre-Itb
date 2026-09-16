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
       2. PANTALLA DE LA BIBLIOTECA
       ============================================================
       Subida con progreso por archivo, filtros, orden, vistas,
       selección múltiple y visor. Todo sobre las tarjetas que PHP ya
       pintó: la única ida al servidor es subir y eliminar. */

    const grid = document.getElementById('biblioteca-grid');
    const zona = document.getElementById('biblioteca-dropzone');
    if (!zona && !grid) return;

    const $ = (id) => document.getElementById(id);
    const token = () => {
        const i = document.querySelector('#biblioteca-subida [name="csrf_token"]');
        return i ? i.value : '';
    };

    // Mensaje flotante breve, para no recargar la página por cada acción.
    function avisar(texto, tipo) {
        let caja = $('biblioteca-avisos');
        if (!caja) {
            caja = document.createElement('div');
            caja.id = 'biblioteca-avisos';
            caja.className = 'biblioteca-avisos';
            document.body.appendChild(caja);
        }
        const a = document.createElement('div');
        a.className = 'biblioteca-aviso is-' + (tipo || 'success');
        a.setAttribute('role', 'status');
        a.textContent = texto;
        caja.appendChild(a);
        setTimeout(function () {
            a.classList.add('is-saliendo');
            setTimeout(function () { a.remove(); }, 300);
        }, 3200);
    }

    /* ---------- SUBIDA ---------- */

    const input     = $('biblioteca-input');
    const modal     = $('biblioteca-modal');
    const btnAbrir  = $('biblioteca-abrir-subida');
    const btnCerrar = $('biblioteca-modal-cerrar');
    const btnCancel = $('biblioteca-modal-cancelar');
    const btnEnviar = $('biblioteca-enviar');
    const previews  = $('biblioteca-previews');
    const previewsCaja = $('biblioteca-previews-caja');
    const texto     = $('biblioteca-seleccion-texto');
    const btnVaciar = $('biblioteca-vaciar');

    let elegidos = [];
    let subiendo = false;
    let ultimoFoco = null;

    const pesoLegible = (b) => {
        if (b >= 1048576) return (b / 1048576).toFixed(1).replace('.', ',') + ' MB';
        if (b >= 1024) return Math.round(b / 1024) + ' KB';
        return b + ' B';
    };

    function abrirModal() {
        ultimoFoco = document.activeElement;
        modal.classList.remove('is-hidden');
        requestAnimationFrame(() => modal.classList.add('is-visible'));
        (btnCerrar || modal).focus();
    }

    function cerrarModal() {
        if (subiendo) return; // no cerrar a media subida
        modal.classList.remove('is-visible');
        setTimeout(() => modal.classList.add('is-hidden'), 200);
        if (ultimoFoco) ultimoFoco.focus();
    }

    btnVaciar?.addEventListener('click', () => { if (!subiendo) limpiarSeleccion(); });
    btnAbrir?.addEventListener('click', abrirModal);
    btnCerrar?.addEventListener('click', cerrarModal);
    btnCancel?.addEventListener('click', () => { limpiarSeleccion(); cerrarModal(); });
    modal?.addEventListener('click', (e) => { if (e.target === modal) cerrarModal(); });

    function sincronizarInput() {
        const dt = new DataTransfer();
        elegidos.forEach((f) => dt.items.add(f.archivo));
        if (input) input.files = dt.files;
    }

    function limpiarSeleccion() {
        elegidos.forEach((e) => URL.revokeObjectURL(e.url));
        elegidos = [];
        sincronizarInput();
        pintarPreviews();
    }

    function pintarPreviews() {
        if (!previews) return;
        previews.innerHTML = '';

        if (!elegidos.length) {
            previewsCaja?.classList.add('is-hidden');
            if (texto) texto.textContent = '';
            if (btnEnviar) btnEnviar.disabled = true;
            return;
        }

        previewsCaja?.classList.remove('is-hidden');
        if (btnEnviar) btnEnviar.disabled = subiendo;

        if (texto) {
            const pendientes = elegidos.filter((e) => e.estado === 'pendiente').length;
            texto.textContent = subiendo
                ? 'Subiendo…'
                : (pendientes === 1 ? '1 imagen lista para subir' : pendientes + ' imágenes listas para subir');
        }

        elegidos.forEach((item, i) => {
            const caja = document.createElement('div');
            caja.className = 'biblioteca-preview is-' + item.estado;

            const img = document.createElement('img');
            img.src = item.url;
            img.alt = item.archivo.name;

            const pie = document.createElement('span');
            pie.className = 'biblioteca-preview__nombre';
            pie.textContent = item.archivo.name;
            pie.title = item.archivo.name + ' · ' + pesoLegible(item.archivo.size);

            caja.append(img, pie);

            if (item.estado === 'pendiente') {
                const quitar = document.createElement('button');
                quitar.type = 'button';
                quitar.className = 'biblioteca-preview__quitar';
                quitar.innerHTML = '<i class="bi bi-x" aria-hidden="true"></i>';
                quitar.setAttribute('aria-label', 'Quitar ' + item.archivo.name + ' de la selección');
                quitar.addEventListener('click', () => {
                    URL.revokeObjectURL(item.url);
                    elegidos.splice(i, 1);
                    sincronizarInput();
                    pintarPreviews();
                });
                caja.appendChild(quitar);
            }

            // Barra de progreso real de ESTE archivo, no un giro indefinido.
            if (item.estado === 'subiendo') {
                const barra = document.createElement('div');
                barra.className = 'biblioteca-preview__barra';
                const relleno = document.createElement('span');
                relleno.style.width = item.progreso + '%';
                barra.appendChild(relleno);
                caja.appendChild(barra);
            }

            if (item.estado === 'hecho' || item.estado === 'error') {
                const marca = document.createElement('span');
                marca.className = 'biblioteca-preview__estado';
                marca.innerHTML = item.estado === 'hecho'
                    ? '<i class="bi bi-check-lg" aria-hidden="true"></i>'
                    : '<i class="bi bi-exclamation-lg" aria-hidden="true"></i>';
                marca.title = item.error || 'Subida';
                caja.appendChild(marca);
            }

            previews.appendChild(caja);
        });
    }

    function anadirArchivos(lista) {
        Array.from(lista || []).forEach((f) => {
            if (!/^image\//.test(f.type) && !/\.svg$/i.test(f.name)) return;
            const repetido = elegidos.some((e) => e.archivo.name === f.name && e.archivo.size === f.size);
            if (repetido) return;
            elegidos.push({ archivo: f, url: URL.createObjectURL(f), estado: 'pendiente', progreso: 0, error: '' });
        });
        sincronizarInput();
        pintarPreviews();
    }

    // Sube UN archivo y va contando su progreso. XMLHttpRequest y no fetch,
    // porque fetch todavía no informa del progreso de subida.
    function subirUno(item) {
        return new Promise((resolve) => {
            const datos = new FormData();
            datos.append('csrf_token', token());
            datos.append('action', 'subir_una');
            datos.append('imagen', item.archivo);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.pathname, true);

            xhr.upload.addEventListener('progress', (e) => {
                if (!e.lengthComputable) return;
                item.progreso = Math.round((e.loaded / e.total) * 100);
                pintarPreviews();
            });

            xhr.addEventListener('load', () => {
                let r = {};
                try { r = JSON.parse(xhr.responseText); } catch (err) { r = {}; }
                if (xhr.status >= 200 && xhr.status < 300 && r.ok) {
                    item.estado = 'hecho';
                    resolve({ ok: true, imagen: r.imagen });
                } else {
                    item.estado = 'error';
                    item.error = r.error || 'No se pudo subir.';
                    resolve({ ok: false, error: item.error });
                }
                pintarPreviews();
            });

            xhr.addEventListener('error', () => {
                item.estado = 'error';
                item.error = 'Se perdió la conexión.';
                pintarPreviews();
                resolve({ ok: false, error: item.error });
            });

            item.estado = 'subiendo';
            pintarPreviews();
            xhr.send(datos);
        });
    }

    $('biblioteca-subida')?.addEventListener('submit', async (e) => {
        // Con JavaScript se sube por aquí, de una en una y con progreso. Sin
        // JavaScript el formulario se envía solo y lo atiende el POST de PHP.
        e.preventDefault();
        if (subiendo) return;

        const pendientes = elegidos.filter((i) => i.estado === 'pendiente');
        if (!pendientes.length) return;

        subiendo = true;
        if (btnEnviar) btnEnviar.disabled = true;
        pintarPreviews();

        let ok = 0;
        const fallos = [];
        for (const item of pendientes) {
            const r = await subirUno(item);
            if (r.ok) { ok++; anadirTarjeta(r.imagen); }
            else fallos.push(item.archivo.name + ': ' + r.error);
        }

        subiendo = false;
        pintarPreviews();

        // Resumen honesto: cuántas entraron y cuáles no, con el motivo.
        if (ok && !fallos.length) {
            avisar(ok === 1 ? 'Imagen subida a la biblioteca.' : ok + ' imágenes subidas a la biblioteca.');
            setTimeout(() => { limpiarSeleccion(); cerrarModal(); }, 700);
        } else if (ok) {
            avisar(ok + ' subida(s). ' + fallos.length + ' con problemas.', 'warning');
        } else {
            avisar(fallos[0] || 'No se pudo subir ninguna imagen.', 'error');
        }
    });

    input?.addEventListener('change', () => anadirArchivos(input.files));

    if (zona) {
        ['dragenter', 'dragover'].forEach((ev) => zona.addEventListener(ev, (e) => {
            e.preventDefault(); zona.classList.add('is-encima');
        }));
        ['dragleave', 'drop'].forEach((ev) => zona.addEventListener(ev, (e) => {
            e.preventDefault(); zona.classList.remove('is-encima');
        }));
        zona.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files.length) anadirArchivos(e.dataTransfer.files);
        });
        zona.addEventListener('click', (e) => {
            if (e.target.closest('label')) return;
            input.click();
        });
    }

    // Soltar archivos en CUALQUIER parte de la pantalla abre la ventana de
    // subida: obligar a apuntar al recuadro es una punteria innecesaria.
    if (grid && modal) {
        let velo = null;
        window.addEventListener('dragover', (e) => {
            if (!e.dataTransfer || !Array.from(e.dataTransfer.types).includes('Files')) return;
            e.preventDefault();
            if (!velo) {
                velo = document.createElement('div');
                velo.className = 'biblioteca-soltar';
                velo.innerHTML = '<span><i class="bi bi-cloud-arrow-up-fill"></i> Suelta las imágenes para subirlas</span>';
                document.body.appendChild(velo);
            }
        });
        window.addEventListener('dragleave', (e) => {
            if (e.relatedTarget) return;
            if (velo) { velo.remove(); velo = null; }
        });
        window.addEventListener('drop', (e) => {
            if (!e.dataTransfer || !e.dataTransfer.files.length) return;
            e.preventDefault();
            if (velo) { velo.remove(); velo = null; }
            if (modal.classList.contains('is-hidden')) abrirModal();
            anadirArchivos(e.dataTransfer.files);
        });
    }

    /* ---------- TARJETA NUEVA SIN RECARGAR ---------- */

    function anadirTarjeta(img) {
        if (!grid || !img) return;
        const vacia = document.querySelector('.biblioteca-vacia');
        if (vacia) vacia.remove();

        const fig = document.createElement('figure');
        fig.className = 'biblioteca-card is-nueva';
        fig.dataset.nombre = (img.nombre || '').toLowerCase();
        fig.dataset.fecha = String(Math.floor(Date.now() / 1000));
        fig.dataset.peso = '0';
        fig.dataset.uso = 'sin-usar';
        fig.dataset.ruta = img.ruta;

        fig.innerHTML =
            '<label class="biblioteca-card__marca">' +
                '<input type="checkbox" class="js-marcar" aria-label="Seleccionar ' + img.nombre + '">' +
                '<span aria-hidden="true"></span>' +
            '</label>' +
            '<button type="button" class="biblioteca-card__img js-ver-imagen" ' +
                    'data-src="' + img.src + '" data-nombre="' + img.nombre + '" ' +
                    'data-meta="' + img.peso + ' · ' + img.fecha + ' · Recién subida" ' +
                    'aria-label="Ver ' + img.nombre + ' a tamaño completo">' +
                '<img src="' + img.src + '" alt="' + img.nombre + '">' +
                '<span class="biblioteca-card__lupa" aria-hidden="true"><i class="bi bi-arrows-fullscreen"></i></span>' +
            '</button>' +
            '<figcaption class="biblioteca-card__info">' +
                '<span class="biblioteca-card__nombre" title="' + img.nombre + '">' + img.nombre + '</span>' +
                '<span class="biblioteca-card__meta">' + img.peso + ' · ' + img.fecha + '</span>' +
                '<span class="biblioteca-card__uso"><i class="bi bi-dash-circle"></i> Sin usar</span>' +
            '</figcaption>' +
            '<div class="biblioteca-card__acciones">' +
                '<button type="button" class="btn btn-sm btn-outline js-copiar-ruta" data-ruta="' + img.ruta + '" ' +
                        'aria-label="Copiar la ruta de ' + img.nombre + '"><i class="bi bi-clipboard" aria-hidden="true"></i> Copiar ruta</button>' +
            '</div>';

        grid.prepend(fig);
        enlazarTarjeta(fig);
        actualizarConteo();
    }

    /* ---------- FILTRAR, ORDENAR Y VER ---------- */

    let filtroUso = 'todas';

    function tarjetas() {
        return grid ? Array.from(grid.querySelectorAll('.biblioteca-card')) : [];
    }

    function aplicarFiltro() {
        tarjetas().forEach((c) => {
            const visible = filtroUso === 'todas' || c.dataset.uso === filtroUso;
            c.hidden = !visible;
            if (!visible) {
                const ch = c.querySelector('.js-marcar');
                if (ch) ch.checked = false;
            }
        });
        actualizarConteo();
        actualizarLote();
    }

    function actualizarConteo() {
        const visibles = tarjetas().filter((c) => !c.hidden).length;
        const el = $('biblioteca-conteo');
        if (el) el.textContent = visibles + (visibles === 1 ? ' imagen' : ' imágenes');
    }

    document.querySelectorAll('[data-filtro-uso]').forEach((b) => {
        b.addEventListener('click', () => {
            document.querySelectorAll('[data-filtro-uso]').forEach((o) => {
                o.classList.toggle('is-activo', o === b);
                o.setAttribute('aria-pressed', String(o === b));
            });
            filtroUso = b.dataset.filtroUso;
            aplicarFiltro();
        });
    });

    $('biblioteca-orden')?.addEventListener('change', (e) => {
        const modo = e.target.value;
        const lista = tarjetas();
        lista.sort((a, b) => {
            if (modo === 'nombre') return a.dataset.nombre.localeCompare(b.dataset.nombre);
            if (modo === 'pesadas') return Number(b.dataset.peso) - Number(a.dataset.peso);
            if (modo === 'antiguas') return Number(a.dataset.fecha) - Number(b.dataset.fecha);
            return Number(b.dataset.fecha) - Number(a.dataset.fecha);
        });
        lista.forEach((c) => grid.appendChild(c));
    });

    // Solo los BOTONES del conmutador: la rejilla también lleva data-vista
    // para saber cómo pintarse, y sin acotar aquí acababa escuchando clics.
    document.querySelectorAll('.biblioteca-vistas [data-vista]').forEach((b) => {
        b.addEventListener('click', () => {
            document.querySelectorAll('.biblioteca-vistas [data-vista]').forEach((o) => {
                o.classList.toggle('is-activo', o === b);
                o.setAttribute('aria-pressed', String(o === b));
            });
            // Cambiar de vista no toca los filtros ni lo que esté marcado.
            if (grid) grid.dataset.vista = b.dataset.vista;
            try { localStorage.setItem('itb-biblioteca-vista', b.dataset.vista); } catch (err) {}
        });
    });

    // Se recuerda la vista elegida entre visitas.
    try {
        const guardada = localStorage.getItem('itb-biblioteca-vista');
        if (guardada === 'lista') document.querySelector('.biblioteca-vistas [data-vista="lista"]')?.click();
    } catch (err) {}

    /* ---------- SELECCIÓN MÚLTIPLE ---------- */

    const lote        = $('biblioteca-lote');
    const loteTexto   = $('biblioteca-lote-texto');
    const loteCancel  = $('biblioteca-lote-cancelar');
    const loteBorrar  = $('biblioteca-lote-eliminar');

    function marcadas() {
        return tarjetas().filter((c) => {
            const ch = c.querySelector('.js-marcar');
            return ch && ch.checked && !c.hidden;
        });
    }

    function actualizarLote() {
        if (!lote) return;
        const n = marcadas().length;
        lote.classList.toggle('is-hidden', n === 0);
        if (loteTexto) {
            loteTexto.textContent = n === 1
                ? '1 imagen seleccionada'
                : n + ' imágenes seleccionadas';
        }
    }

    loteCancel?.addEventListener('click', () => {
        tarjetas().forEach((c) => {
            const ch = c.querySelector('.js-marcar');
            if (ch) ch.checked = false;
            c.classList.remove('is-marcada');
        });
        actualizarLote();
    });

    loteBorrar?.addEventListener('click', () => {
        const sel = marcadas();
        if (!sel.length) return;

        const hacerlo = async () => {
            const datos = new FormData();
            datos.append('csrf_token', token());
            datos.append('action', 'eliminar_varias');
            sel.forEach((c) => datos.append('rutas[]', c.dataset.ruta));

            try {
                const r = await fetch(window.location.pathname, { method: 'POST', body: datos });
                const j = await r.json();
                (j.borradas || []).forEach((ruta) => {
                    grid.querySelector('[data-ruta="' + CSS.escape(ruta) + '"]')?.remove();
                });
                if (j.ok) {
                    avisar(j.borradas.length === 1 ? 'Imagen eliminada.' : j.borradas.length + ' imágenes eliminadas.');
                } else {
                    avisar((j.fallos || []).join(' · ') || 'No se pudo eliminar.', 'warning');
                }
            } catch (err) {
                avisar('No se pudo conectar con el servidor.', 'error');
            }
            actualizarLote();
            actualizarConteo();
        };

        const mensaje = sel.length === 1
            ? 'Vas a eliminar 1 imagen. Esta acción no se puede deshacer.'
            : 'Vas a eliminar ' + sel.length + ' imágenes. Esta acción no se puede deshacer.';

        if (typeof window.customConfirm === 'function') window.customConfirm(mensaje, hacerlo);
        else if (window.confirm(mensaje)) hacerlo();
    });

    /* ---------- COPIAR RUTA ---------- */

    async function copiarRuta(boton) {
        const ruta = boton.dataset.ruta;
        try {
            await navigator.clipboard.writeText(ruta);
        } catch (err) {
            // Navegadores sin permiso de portapapeles: se usa el camino viejo.
            const t = document.createElement('textarea');
            t.value = ruta;
            document.body.appendChild(t);
            t.select();
            try { document.execCommand('copy'); } catch (e2) {}
            t.remove();
        }
        const antes = boton.innerHTML;
        boton.innerHTML = '<i class="bi bi-check-lg" aria-hidden="true"></i> Copiada';
        boton.classList.add('is-copiado');
        setTimeout(() => {
            boton.innerHTML = antes;
            boton.classList.remove('is-copiado');
        }, 1600);
    }

    /* ---------- ENLAZAR UNA TARJETA ---------- */

    function enlazarTarjeta(card) {
        card.querySelector('.js-ver-imagen')?.addEventListener('click', function () { abrirVisor(this); });
        card.querySelector('.js-copiar-ruta')?.addEventListener('click', function () { copiarRuta(this); });
        const ch = card.querySelector('.js-marcar');
        if (ch) {
            ch.addEventListener('change', () => {
                card.classList.toggle('is-marcada', ch.checked);
                actualizarLote();
            });
        }
    }

    tarjetas().forEach(enlazarTarjeta);
    actualizarConteo();

    /* ---------- VISOR ---------- */

    const visor       = $('biblioteca-visor');
    const visorImg    = $('biblioteca-visor-img');
    const visorNombre = $('biblioteca-visor-nombre');
    const visorCerrar = $('biblioteca-visor-cerrar');
    let focoAntesVisor = null;

    const visorDim    = $('biblioteca-visor-dim');
    const visorPeso   = $('biblioteca-visor-peso');
    const visorTipo   = $('biblioteca-visor-tipo');
    const visorFecha  = $('biblioteca-visor-fecha');
    const visorRuta   = $('biblioteca-visor-ruta');
    const visorCopiar = $('biblioteca-visor-copiar');
    const visorUsos   = $('biblioteca-visor-usos');
    const visorAbrir  = $('biblioteca-visor-abrir');
    const visorPrev   = $('biblioteca-visor-prev');
    const visorNext   = $('biblioteca-visor-next');

    let botonActual = null;

    // Solo las que se ven ahora: si hay un filtro puesto, las flechas
    // recorren ese subconjunto y no la biblioteca entera.
    function botonesVisibles() {
        return tarjetas()
            .filter((c) => !c.hidden)
            .map((c) => c.querySelector('.js-ver-imagen'))
            .filter(Boolean);
    }

    function rellenarFicha(boton) {
        const d = boton.dataset;

        visorNombre.textContent = d.nombre || '';
        if (visorPeso)  visorPeso.textContent  = d.peso || '—';
        if (visorTipo)  visorTipo.textContent  = d.tipo || '—';
        if (visorFecha) visorFecha.textContent = d.fecha || '—';

        if (visorRuta)   visorRuta.textContent = d.ruta || '';
        if (visorCopiar) visorCopiar.dataset.ruta = d.ruta || '';
        if (visorAbrir)  visorAbrir.href = d.src || '#';

        // El tamaño real se lee de la propia imagen al cargarla: pedírselo al
        // servidor para las 107 de la rejilla ralentizaría la pantalla.
        if (visorDim) {
            visorDim.textContent = 'Midiendo…';
            const medir = () => {
                visorDim.textContent = visorImg.naturalWidth
                    ? visorImg.naturalWidth + ' × ' + visorImg.naturalHeight + ' px'
                    : 'No disponible';
            };
            if (visorImg.complete && visorImg.naturalWidth) medir();
            else visorImg.addEventListener('load', medir, { once: true });
        }

        if (visorUsos) {
            const usos = (d.usos || '').split('|').filter(Boolean);
            visorUsos.innerHTML = '';
            if (!usos.length) {
                const li = document.createElement('li');
                li.className = 'is-libre';
                li.textContent = 'No se está usando en ninguna sección. Se puede eliminar.';
                visorUsos.appendChild(li);
            } else {
                usos.forEach((u) => {
                    const li = document.createElement('li');
                    li.textContent = u;
                    visorUsos.appendChild(li);
                });
            }
        }

        const lista = botonesVisibles();
        const i = lista.indexOf(boton);
        if (visorPrev) visorPrev.disabled = i <= 0;
        if (visorNext) visorNext.disabled = i < 0 || i >= lista.length - 1;
    }

    function abrirVisor(boton) {
        if (!visor) return;
        if (!botonActual) focoAntesVisor = boton; // solo al abrir, no al pasar
        botonActual = boton;

        visorImg.src = boton.dataset.src;
        visorImg.alt = boton.dataset.nombre || '';
        rellenarFicha(boton);

        visor.classList.remove('is-hidden');
        requestAnimationFrame(() => visor.classList.add('is-visible'));
        visorCerrar?.focus();
    }

    function mover(paso) {
        const lista = botonesVisibles();
        const i = lista.indexOf(botonActual);
        const siguiente = lista[i + paso];
        if (siguiente) abrirVisor(siguiente);
    }

    visorPrev?.addEventListener('click', () => mover(-1));
    visorNext?.addEventListener('click', () => mover(1));
    visorCopiar?.addEventListener('click', function () { copiarRuta(this); });

    function cerrarVisor() {
        if (!visor) return;
        visor.classList.remove('is-visible');
        setTimeout(() => {
            visor.classList.add('is-hidden');
            visorImg.src = '';
        }, 200);
        botonActual = null;
        if (focoAntesVisor) focoAntesVisor.focus();
    }

    visorCerrar?.addEventListener('click', cerrarVisor);
    visor?.addEventListener('click', (e) => { if (e.target === visor) cerrarVisor(); });

    // Escape cierra la ventana que esté abierta, la de subir o la del visor.
    document.addEventListener('keydown', (e) => {
        const visorAbierto = visor && !visor.classList.contains('is-hidden');

        if (e.key === 'Escape') {
            if (visorAbierto) cerrarVisor();
            else if (modal && !modal.classList.contains('is-hidden')) cerrarModal();
            return;
        }

        // Recorrer la biblioteca con las flechas, como en cualquier visor.
        if (!visorAbierto) return;
        if (e.key === 'ArrowLeft')  { e.preventDefault(); mover(-1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); mover(1); }
    });
})();
