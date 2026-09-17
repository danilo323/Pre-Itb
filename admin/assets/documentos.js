// admin/assets/documentos.js
//
// Dos cosas relacionadas con la documentos de documentos:
//
//   1. window.abrirdocumentos(callback) â€” el selector que sale al pulsar
//      "Elegir de la documentos" en cualquier campo de documento del panel.
//      Se carga en TODAS las pantallas (lo mete views/layout.php), porque
//      cualquier secciÃ³n puede tener un campo de documento.
//
//   2. La pantalla admin/documentos.php: arrastrar y soltar archivos.
//
// Las dos suben por el mismo sitio: subirdocumento(), aquÃ­ abajo. Antes la
// subida vivÃ­a dentro del bloque de la pantalla de documentos, que sale
// antes en cualquier otra pantalla, asÃ­ que el selector no podÃ­a subir y
// se limitaba a enlazar a la documentos en una pestaÃ±a nueva.

(function () {
    'use strict';

    const baseAdmin = document.body.dataset.adminBase || '/admin';

    /* ============================================================
       0. COSAS QUE USAN LAS DOS PARTES
       ============================================================ */

    // El token va en el <body> (views/layout.php). Se deja el formulario de
    // la pantalla de documentos como respaldo por si alguna pantalla del
    // panel no pasara por el layout.
    function tokenCsrf() {
        const enCuerpo = document.body.dataset.csrf;
        if (enCuerpo) return enCuerpo;
        const input = document.querySelector('input[name="csrf_token"]');
        return input ? input.value : '';
    }

    // Sube UN archivo y va contando su progreso. XMLHttpRequest y no fetch,
    // porque fetch todavÃ­a no informa del progreso de subida.
    // Resuelve siempre (nunca rechaza): { ok, documento } o { ok:false, error }.
    function subirdocumento(archivo, alProgresar) {
        return new Promise((resolve) => {
            const datos = new FormData();
            datos.append('csrf_token', tokenCsrf());
            datos.append('action', 'subir_una');
            datos.append('documento', archivo);

            const xhr = new XMLHttpRequest();
            // Siempre a documentos.php, no a la pantalla actual: el endpoint
            // responde antes de pintar nada, asÃ­ que sirve desde cualquiera.
            xhr.open('POST', baseAdmin + '/documentos.php', true);

            xhr.upload.addEventListener('progress', (e) => {
                if (!e.lengthComputable || typeof alProgresar !== 'function') return;
                alProgresar(Math.round((e.loaded / e.total) * 100));
            });

            xhr.addEventListener('load', () => {
                let r = {};
                try { r = JSON.parse(xhr.responseText); } catch (err) { r = {}; }
                if (xhr.status >= 200 && xhr.status < 300 && r.ok) {
                    resolve({ ok: true, documento: r.documento });
                } else if (xhr.status === 403) {
                    // csrf_check() corta con texto plano, no con JSON.
                    resolve({ ok: false, error: 'La sesiÃ³n caducÃ³. Recarga la pÃ¡gina e intÃ©ntalo otra vez.' });
                } else {
                    resolve({ ok: false, error: r.error || 'No se pudo subir.' });
                }
            });

            xhr.addEventListener('error', () => {
                resolve({ ok: false, error: 'Se perdiÃ³ la conexiÃ³n.' });
            });

            xhr.send(datos);
        });
    }

    // Mismo criterio que la pantalla de documentos. El
    // servidor vuelve a comprobarlo; esto solo evita el viaje en balde.
    function esdocumento(f) {
        return /\.(pdf|xml|doc|docx|xls|xlsx)$/i.test(f.name);
    }

    /* ============================================================
       1. SELECTOR DE IMÃGENES
       ============================================================ */

    let overlay = null;      // el modal, se construye una sola vez
    let alElegir = null;     // callback del campo que lo abriÃ³
    let documentoes = [];       // Ãºltima lista traÃ­da del servidor
    let recienSubidas = [];  // rutas de esta sesiÃ³n del modal, para destacarlas
    let subiendoEnPicker = false;

    function construirModal() {
        overlay = document.createElement('div');
        overlay.className = 'bib-picker-overlay';
        overlay.innerHTML = `
            <div class="bib-picker" role="dialog" aria-modal="true" aria-label="Elegir documento de la galería">
                <div class="bib-picker__head">
                    <h3><i class="bi bi-folder-fill"></i> Elegir de Documentos</h3>
                    <button type="button" class="bib-picker__cerrar" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="bib-picker__barra">
                    <div class="bib-picker__campo">
                        <i class="bi bi-search" aria-hidden="true"></i>
                        <input type="search" class="bib-picker__buscar" placeholder="Buscar por nombre de archivo…"
                               aria-label="Buscar documentos por nombre" autocomplete="off">
                        <button type="button" class="bib-picker__limpiar" aria-label="Limpiar la búsqueda" hidden>
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <button type="button" class="bib-picker__subir">
                        <i class="bi bi-upload"></i> Subir documentos
                    </button>
                    <input type="file" class="bib-picker__file" accept=".pdf,.xml,.doc,.docx,.xls,.xlsx" multiple hidden>
                </div>
                <div class="bib-picker__cola" hidden aria-live="polite"></div>
                <div class="bib-picker__grid"></div>
                <div class="bib-picker__pie">
                    <span class="bib-picker__conteo"></span>
                    <div class="bib-picker__acciones">
                        <a href="${baseAdmin}/documentos.php" target="_blank" rel="noopener" class="bib-picker__enlace">
                            <i class="bi bi-box-arrow-up-right"></i> Administrar documentos
                        </a>
                        <button type="button" class="btn btn-outline bib-picker__cancelar">Cancelar</button>
                    </div>
                </div>
                <div class="bib-picker__soltar" aria-hidden="true">
                    <span><i class="bi bi-cloud-arrow-up-fill"></i> Suelta los documentos para subirlos</span>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        const buscar = overlay.querySelector('.bib-picker__buscar');
        const limpiar = overlay.querySelector('.bib-picker__limpiar');
        const file = overlay.querySelector('.bib-picker__file');

        overlay.querySelector('.bib-picker__cerrar').addEventListener('click', cerrar);
        overlay.querySelector('.bib-picker__cancelar').addEventListener('click', cerrar);

        // Clic FUERA de la caja cierra; dentro, no. Sin esta comprobaciÃ³n,
        // cualquier clic en la rejilla cerrarÃ­a el selector.
        overlay.addEventListener('click', e => { if (e.target === overlay) cerrar(); });

        buscar.addEventListener('input', function () {
            limpiar.hidden = this.value === '';
            pintar(this.value.trim().toLowerCase());
        });

        limpiar.addEventListener('click', () => {
            buscar.value = '';
            limpiar.hidden = true;
            pintar('');
            buscar.focus();
        });

        // DelegaciÃ³n: las tarjetas se repintan al buscar, asÃ­ que el listener
        // va en el contenedor y no en cada una.
        overlay.querySelector('.bib-picker__grid').addEventListener('click', function (e) {
            const card = e.target.closest('.bib-picker__item');
            if (!card) return;
            elegir(card.dataset.ruta, card.dataset.src);
        });

        /* ---- Subida desde el propio selector ---- */
        overlay.querySelector('.bib-picker__subir').addEventListener('click', () => file.click());
        file.addEventListener('change', () => {
            subirEnPicker(file.files);
            file.value = '';   // permite volver a elegir el mismo archivo
        });

        engancharArrastre();

        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape' || !overlay.classList.contains('is-visible')) return;
            // Con la bÃºsqueda escrita, Escape la limpia antes de cerrar: es lo
            // que espera quien estÃ¡ filtrando.
            if (buscar.value !== '') {
                buscar.value = '';
                limpiar.hidden = true;
                pintar('');
                return;
            }
            cerrar();
        });
    }

    /* ---- Arrastrar y soltar sobre el selector ----
       Mismo planteamiento que en la pantalla de documentos: dragenter y
       dragleave burbujean por cada hijo, asÃ­ que hace falta un contador; y
       el velo se quita SIEMPRE al soltar, incluso si lo soltado no eran
       archivos, porque si no se queda tapando el modal y parece colgado. */
    function engancharArrastre() {
        let profundidad = 0;
        let arrastreInterno = false;

        const traeArchivos = (e) =>
            !!e.dataTransfer && Array.from(e.dataTransfer.types || []).includes('Files');

        const quitar = () => {
            profundidad = 0;
            overlay.classList.remove('is-soltando');
        };

        // Arrastrar una miniatura del propio selector no es subir nada.
        overlay.addEventListener('dragstart', () => { arrastreInterno = true; });
        overlay.addEventListener('dragend', () => { arrastreInterno = false; quitar(); });

        overlay.addEventListener('dragenter', (e) => {
            if (arrastreInterno || !traeArchivos(e)) return;
            profundidad++;
            overlay.classList.add('is-soltando');
        });

        overlay.addEventListener('dragover', (e) => {
            if (arrastreInterno || !traeArchivos(e)) return;
            e.preventDefault();   // sin esto el navegador abre el archivo
        });

        overlay.addEventListener('dragleave', () => {
            profundidad = Math.max(0, profundidad - 1);
            if (profundidad === 0) quitar();
        });

        overlay.addEventListener('drop', (e) => {
            quitar();
            arrastreInterno = false;
            if (!e.dataTransfer || !e.dataTransfer.files.length) return;
            e.preventDefault();
            subirEnPicker(e.dataTransfer.files);
        });

        // Redes de seguridad: si el arrastre acaba fuera de la ventana no
        // llega ningÃºn evento y el velo se quedarÃ­a puesto.
        window.addEventListener('blur', quitar);
        document.addEventListener('visibilitychange', () => { if (document.hidden) quitar(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') quitar(); });
    }

    function pintar(filtro) {
        const grid = overlay.querySelector('.bib-picker__grid');
        const conteo = overlay.querySelector('.bib-picker__conteo');
        const lista = filtro
            ? documentoes.filter(i => i.nombre.toLowerCase().includes(filtro))
            : documentoes;

        if (!lista.length) {
            grid.innerHTML = `
                <p class="bib-picker__vacio">
                    ${documentoes.length
                        ? 'Ningún documento coincide con esa búsqueda.'
                        : 'La galería está vacía. Usa «Subir documentos» para añadir archivos.'}
                </p>`;
            conteo.textContent = '';
            return;
        }

        grid.innerHTML = lista.map(i => {
            const nueva = recienSubidas.includes(i.ruta);
            return `
            <button type="button" class="bib-picker__item${nueva ? ' is-nueva' : ''}" data-ruta="${escapar(i.ruta)}" data-src="${escapar(i.src)}">
                <span class="bib-picker__thumb"><i class="bi bi-file-earmark-text"></i></span>
                ${nueva ? '<span class="bib-picker__nueva">ReciÃ©n subida</span>' : ''}
                <span class="bib-picker__nombre" title="${escapar(i.nombre)}">${escapar(i.nombre)}</span>
                <span class="bib-picker__peso">${escapar(i.peso)}</span>
            </button>`;
        }).join('');

        conteo.textContent = lista.length + (lista.length === 1 ? ' documento' : ' documentos');
    }

    /* ---- Subir desde el selector ----
       Se sube de una en una para poder enseÃ±ar el progreso real de cada
       archivo y decir cuÃ¡l fallÃ³ y por quÃ©, igual que en la pantalla de
       documentos. Las que entran se colocan las PRIMERAS de la rejilla y se
       marcan, que es lo que se acaba de subir y lo que se va a elegir. */
    async function subirEnPicker(archivos) {
        if (subiendoEnPicker) return;

        const buenos = Array.from(archivos || []).filter(esdocumento);
        const cola = overlay.querySelector('.bib-picker__cola');

        if (!buenos.length) {
            pintarCola([{ nombre: '', estado: 'error', error: 'Eso no es una documento. Se admiten JPG, PNG, WEBP, GIF y SVG.' }]);
            return;
        }

        subiendoEnPicker = true;
        overlay.querySelector('.bib-picker__subir').disabled = true;

        const items = buenos.map(f => ({ nombre: f.name, archivo: f, estado: 'espera', progreso: 0, error: '' }));
        pintarCola(items);

        const entradas = [];
        const fallos = [];

        for (const item of items) {
            item.estado = 'subiendo';
            pintarCola(items);

            const r = await subirdocumento(item.archivo, (pct) => {
                item.progreso = pct;
                pintarCola(items);
            });

            if (r.ok) {
                item.estado = 'hecho';
                entradas.push(r.documento);
                recienSubidas.push(r.documento.ruta);
                // Al principio: documentos_listar() ordena por fecha
                // descendente, asÃ­ que ahÃ­ es donde le toca.
                documentoes.unshift(r.documento);
            } else {
                item.estado = 'error';
                item.error = r.error;
                fallos.push(item);
            }
            pintarCola(items);
        }

        subiendoEnPicker = false;
        overlay.querySelector('.bib-picker__subir').disabled = false;

        // La bÃºsqueda se limpia: si habÃ­a un filtro puesto, lo reciÃ©n subido
        // no aparecerÃ­a y darÃ­a la sensaciÃ³n de que no se subiÃ³.
        const buscar = overlay.querySelector('.bib-picker__buscar');
        buscar.value = '';
        overlay.querySelector('.bib-picker__limpiar').hidden = true;
        pintar('');

        if (entradas.length) {
            const grid = overlay.querySelector('.bib-picker__grid');
            grid.scrollTop = 0;
            // Se le da el foco a la primera: asÃ­ se elige con Enter, sin
            // tener que buscarla entre las demÃ¡s.
            const primera = grid.querySelector('.bib-picker__item.is-nueva');
            if (primera) primera.focus();
        }

        // Los errores se quedan a la vista; si todo fue bien, la cola se
        // retira sola y deja sitio a la rejilla.
        if (!fallos.length) {
            setTimeout(() => {
                if (subiendoEnPicker) return;
                cola.hidden = true;
                cola.innerHTML = '';
            }, 1400);
        } else {
            pintarCola(items.filter(i => i.estado === 'error'));
        }
    }

    function pintarCola(items) {
        const cola = overlay.querySelector('.bib-picker__cola');
        if (!items.length) { cola.hidden = true; cola.innerHTML = ''; return; }

        cola.hidden = false;
        cola.innerHTML = items.map(i => {
            const icono = i.estado === 'hecho' ? 'bi-check-circle-fill'
                : i.estado === 'error' ? 'bi-exclamation-triangle-fill'
                : 'bi-arrow-up-circle';
            return `
            <div class="bib-picker__subida is-${i.estado}">
                <i class="bi ${icono}" aria-hidden="true"></i>
                <span class="bib-picker__subida-nombre">${escapar(i.nombre)}</span>
                ${i.estado === 'subiendo'
                    ? `<span class="bib-picker__progreso"><span style="width:${i.progreso}%"></span></span>`
                    : `<span class="bib-picker__subida-msg">${escapar(i.error || (i.estado === 'hecho' ? 'Subida' : 'En cola'))}</span>`}
            </div>`;
        }).join('');
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

    window.abrirdocumentos = function (callback) {
        if (!overlay) construirModal();
        alElegir = callback;

        overlay.classList.remove('is-hidden');
        requestAnimationFrame(() => overlay.classList.add('is-visible'));

        // Cada apertura empieza limpia: la marca de "reciÃ©n subida" es de la
        // sesiÃ³n anterior y ya no dice nada.
        recienSubidas = [];
        const cola = overlay.querySelector('.bib-picker__cola');
        cola.hidden = true;
        cola.innerHTML = '';

        const buscar = overlay.querySelector('.bib-picker__buscar');
        buscar.value = '';
        overlay.querySelector('.bib-picker__limpiar').hidden = true;
        overlay.querySelector('.bib-picker__grid').innerHTML =
            '<p class="bib-picker__vacio">Cargando documentosâ€¦</p>';

        // Se pide la lista CADA vez que se abre, no una sola al cargar la
        // pÃ¡gina: asÃ­ una documento reciÃ©n subida en la otra pestaÃ±a ya aparece.
        fetch(baseAdmin + '/documentos.php?ajax=lista', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                documentoes = (data && data.items) ? data.items : [];
                pintar('');
                buscar.focus();
            })
            .catch(() => {
                overlay.querySelector('.bib-picker__grid').innerHTML =
                    '<p class="bib-picker__vacio">No se pudo cargar la documentos. Recarga la pÃ¡gina e intÃ©ntalo otra vez.</p>';
            });
    };


    /* ============================================================
       2. PANTALLA DE LA documentos
       ============================================================
       Subida con progreso por archivo, filtros, orden, vistas,
       selecciÃ³n mÃºltiple y visor. Todo sobre las tarjetas que PHP ya
       pintÃ³: la Ãºnica ida al servidor es subir y eliminar. */

    const grid = document.getElementById('documentos-grid');
    const zona = document.getElementById('documentos-dropzone');
    if (!zona && !grid) return;

    const $ = (id) => document.getElementById(id);
    // Mismo token que usa el selector: vive en el <body> (views/layout.php).
    const token = tokenCsrf;

    // Mensaje flotante breve, para no recargar la pÃ¡gina por cada acciÃ³n.
    function avisar(texto, tipo) {
        let caja = $('documentos-avisos');
        if (!caja) {
            caja = document.createElement('div');
            caja.id = 'documentos-avisos';
            caja.className = 'documentos-avisos';
            document.body.appendChild(caja);
        }
        const a = document.createElement('div');
        a.className = 'documentos-aviso is-' + (tipo || 'success');
        a.setAttribute('role', 'status');
        a.textContent = texto;
        caja.appendChild(a);
        setTimeout(function () {
            a.classList.add('is-saliendo');
            setTimeout(function () { a.remove(); }, 300);
        }, 3200);
    }

    /* ---------- SUBIDA ---------- */

    const input     = $('documentos-input');
    const modal     = $('documentos-modal');
    const btnAbrir  = $('documentos-abrir-subida');
    const btnCerrar = $('documentos-modal-cerrar');
    const btnCancel = $('documentos-modal-cancelar');
    const btnEnviar = $('documentos-enviar');
    const previews  = $('documentos-previews');
    const previewsCaja = $('documentos-previews-caja');
    const texto     = $('documentos-seleccion-texto');
    const btnVaciar = $('documentos-vaciar');

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
                : (pendientes === 1 ? '1 documento listo para subir' : pendientes + ' documentos listos para subir');
        }

        elegidos.forEach((item, i) => {
            const caja = document.createElement('div');
            caja.className = 'documentos-preview is-' + item.estado;

            const img = document.createElement('div');
            img.className = 'documentos-preview__icon';
            img.style.fontSize = '2rem';
            img.style.padding = '10px 0';
            img.style.color = '#1A3B70';
            img.style.textAlign = 'center';
            
            const ext = item.archivo.name.split('.').pop().toLowerCase();
            if (ext === 'pdf') img.innerHTML = '<i class="bi bi-file-earmark-pdf-fill" style="color:#dc3545;"></i>';
            else if (['doc', 'docx'].includes(ext)) img.innerHTML = '<i class="bi bi-file-earmark-word-fill" style="color:#0d6efd;"></i>';
            else if (['xls', 'xlsx'].includes(ext)) img.innerHTML = '<i class="bi bi-file-earmark-excel-fill" style="color:#198754;"></i>';
            else img.innerHTML = '<i class="bi bi-file-earmark-text-fill"></i>';

            const pie = document.createElement('span');
            pie.className = 'documentos-preview__nombre';
            pie.textContent = item.archivo.name;
            pie.title = item.archivo.name + ' Â· ' + pesoLegible(item.archivo.size);

            caja.append(img, pie);

            if (item.estado === 'pendiente') {
                const quitar = document.createElement('button');
                quitar.type = 'button';
                quitar.className = 'documentos-preview__quitar';
                quitar.innerHTML = '<i class="bi bi-x" aria-hidden="true"></i>';
                quitar.setAttribute('aria-label', 'Quitar ' + item.archivo.name + ' de la selecciÃ³n');
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
                barra.className = 'documentos-preview__barra';
                const relleno = document.createElement('span');
                relleno.style.width = item.progreso + '%';
                barra.appendChild(relleno);
                caja.appendChild(barra);
            }

            if (item.estado === 'hecho' || item.estado === 'error') {
                const marca = document.createElement('span');
                marca.className = 'documentos-preview__estado';
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
            const ext = f.name.split('.').pop().toLowerCase();
            const permitidas = ['pdf', 'xml', 'doc', 'docx', 'xls', 'xlsx'];
            if (!permitidas.includes(ext)) return;
            const repetido = elegidos.some((e) => e.archivo.name === f.name && e.archivo.size === f.size);
            if (repetido) return;
            elegidos.push({ archivo: f, url: URL.createObjectURL(f), estado: 'pendiente', progreso: 0, error: '' });
        });
        sincronizarInput();
        pintarPreviews();
    }

    // La subida en sÃ­ la hace subirdocumento(), compartida con el selector.
    // AquÃ­ solo se traduce su progreso y su resultado a las miniaturas de
    // esta pantalla.
    async function subirUno(item) {
        item.estado = 'subiendo';
        pintarPreviews();

        const r = await subirdocumento(item.archivo, (pct) => {
            item.progreso = pct;
            pintarPreviews();
        });

        if (r.ok) {
            item.estado = 'hecho';
        } else {
            item.estado = 'error';
            item.error = r.error;
        }
        pintarPreviews();
        return r;
    }

    $('documentos-subida')?.addEventListener('submit', async (e) => {
        // Con JavaScript se sube por aquÃ­, de una en una y con progreso. Sin
        // JavaScript el formulario se envÃ­a solo y lo atiende el POST de PHP.
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
            if (r.ok) { ok++; anadirTarjeta(r.documento); }
            else fallos.push(item.archivo.name + ': ' + r.error);
        }

        subiendo = false;
        pintarPreviews();

        // Resumen honesto: cuÃ¡ntas entraron y cuÃ¡les no, con el motivo.
        if (ok && !fallos.length) {
            avisar(ok === 1 ? 'documento subida a la documentos.' : ok + ' documentos subidas a la documentos.');
            setTimeout(() => { limpiarSeleccion(); cerrarModal(); }, 700);
        } else if (ok) {
            avisar(ok + ' subida(s). ' + fallos.length + ' con problemas.', 'warning');
        } else {
            avisar(fallos[0] || 'No se pudo subir ninguna documento.', 'error');
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
        let profundidad = 0;      // dragenter/dragleave se disparan en cascada
        let arrastreInterno = false;

        // Arrastrar una documento DE LA PROPIA pagina no es subir nada. El
        // navegador la ofrece como archivo, asi que sin esta marca el velo
        // aparecia igual y luego no se iba.
        document.addEventListener('dragstart', () => { arrastreInterno = true; });
        document.addEventListener('dragend', () => { arrastreInterno = false; quitarVelo(); });

        function quitarVelo() {
            profundidad = 0;
            if (velo) { velo.remove(); velo = null; }
        }

        function ponerVelo() {
            if (velo) return;
            velo = document.createElement('div');
            velo.className = 'documentos-soltar';
            velo.innerHTML = '<span><i class="bi bi-cloud-arrow-up-fill"></i> Suelta las documentos para subirlas</span>';
            document.body.appendChild(velo);
        }

        const traeArchivos = (e) =>
            !!e.dataTransfer && Array.from(e.dataTransfer.types || []).includes('Files');

        window.addEventListener('dragenter', (e) => {
            if (arrastreInterno || !traeArchivos(e)) return;
            profundidad++;
            ponerVelo();
        });

        window.addEventListener('dragover', (e) => {
            if (arrastreInterno || !traeArchivos(e)) return;
            e.preventDefault();   // sin esto el navegador abre el archivo
            ponerVelo();
        });

        window.addEventListener('dragleave', () => {
            profundidad = Math.max(0, profundidad - 1);
            if (profundidad === 0) quitarVelo();
        });

        window.addEventListener('drop', (e) => {
            // El velo se retira SIEMPRE, incluso si lo soltado no eran
            // archivos: antes se salia antes de llegar aqui y se quedaba
            // pegado tapando la pantalla, con lo que parecia colgada.
            quitarVelo();
            arrastreInterno = false;

            if (!e.dataTransfer || !e.dataTransfer.files.length) return;
            e.preventDefault();
            if (modal.classList.contains('is-hidden')) abrirModal();
            anadirArchivos(e.dataTransfer.files);
        });

        // Ultima red: si el arrastre acaba fuera de la ventana no llega ningun
        // evento y el velo se quedaria puesto.
        window.addEventListener('blur', quitarVelo);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) quitarVelo();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') quitarVelo();
        });
    }

    /* ---------- TARJETA NUEVA SIN RECARGAR ---------- */

    function anadirTarjeta(doc) {
        if (!grid || !doc) return;
        const vacia = document.querySelector('.documentos-vacia');
        if (vacia) vacia.remove();

        const ext = (doc.nombre || '').split('.').pop().toLowerCase();
        let doc_icon = 'bi-file-earmark-fill';
        let doc_color = '#6c757d';
        if (ext === 'pdf') { doc_icon = 'bi-file-earmark-pdf-fill'; doc_color = '#dc3545'; }
        else if (['doc', 'docx'].includes(ext)) { doc_icon = 'bi-file-earmark-word-fill'; doc_color = '#0d6efd'; }
        else if (['xls', 'xlsx'].includes(ext)) { doc_icon = 'bi-file-earmark-excel-fill'; doc_color = '#198754'; }
        else if (ext === 'xml') { doc_icon = 'bi-file-earmark-code-fill'; doc_color = '#fd7e14'; }

        const fig = document.createElement('figure');
        fig.className = 'documentos-card is-nueva';
        fig.dataset.nombre = (doc.nombre || '').toLowerCase();
        fig.dataset.fecha = String(Math.floor(Date.now() / 1000));
        fig.dataset.peso = '0';
        fig.dataset.uso = 'sin-usar';
        fig.dataset.ruta = doc.ruta;

        fig.innerHTML =
            '<label class="documentos-card__marca">' +
                '<input type="checkbox" class="js-marcar" aria-label="Seleccionar ' + doc.nombre + '">' +
                '<span aria-hidden="true"></span>' +
            '</label>' +
            '<button type="button" class="documentos-card__img js-ver-documento" ' +
                    'data-src="../' + doc.ruta + '" ' +
                    'data-nombre="' + doc.nombre + '" ' +
                    'data-ruta="' + doc.ruta + '" ' +
                    'data-peso="' + doc.peso + '" ' +
                    'data-fecha="' + doc.fecha + '" ' +
                    'data-tipo="' + ext.toUpperCase() + '" ' +
                    'data-usos="" ' +
                    'aria-label="Abrir ' + doc.nombre + '">' +
                '<i class="bi ' + doc_icon + '" style="font-size:3.5rem; color:' + doc_color + ';"></i>' +
                '<span class="documentos-card__ext">' + ext.toUpperCase() + '</span>' +
            '</button>' +
            '<figcaption class="documentos-card__info">' +
                '<span class="documentos-card__nombre" title="' + doc.nombre + '">' + doc.nombre + '</span>' +
                '<span class="documentos-card__meta">' + doc.peso + ' · ' + doc.fecha + '</span>' +
                '<span class="documentos-card__uso"><i class="bi bi-dash-circle"></i> Sin usar</span>' +
            '</figcaption>' +
            '<div class="documentos-card__acciones">' +
                '<button type="button" class="btn btn-sm btn-outline js-copiar-ruta" data-ruta="' + doc.ruta + '" ' +
                        'aria-label="Copiar la ruta de ' + doc.nombre + '"><i class="bi bi-clipboard" aria-hidden="true"></i> Copiar ruta</button>' +
            '</div>';

        grid.prepend(fig);
        enlazarTarjeta(fig);
        actualizarConteo();
    }

    /* ---------- FILTRAR, ORDENAR Y VER ---------- */

    let filtroUso = 'todas';

    function tarjetas() {
        return grid ? Array.from(grid.querySelectorAll('.documentos-card')) : [];
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
        const el = $('documentos-conteo');
        if (el) el.textContent = visibles + (visibles === 1 ? ' documento' : ' documentos');
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

    $('documentos-orden')?.addEventListener('change', (e) => {
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

    // Solo los BOTONES del conmutador: la rejilla tambiÃ©n lleva data-vista
    // para saber cÃ³mo pintarse, y sin acotar aquÃ­ acababa escuchando clics.
    document.querySelectorAll('.documentos-vistas [data-vista]').forEach((b) => {
        b.addEventListener('click', () => {
            document.querySelectorAll('.documentos-vistas [data-vista]').forEach((o) => {
                o.classList.toggle('is-activo', o === b);
                o.setAttribute('aria-pressed', String(o === b));
            });
            // Cambiar de vista no toca los filtros ni lo que estÃ© marcado.
            if (grid) grid.dataset.vista = b.dataset.vista;
            try { localStorage.setItem('itb-documentos-vista', b.dataset.vista); } catch (err) {}
        });
    });

    // Se recuerda la vista elegida entre visitas.
    try {
        const guardada = localStorage.getItem('itb-documentos-vista');
        if (guardada === 'lista') document.querySelector('.documentos-vistas [data-vista="lista"]')?.click();
    } catch (err) {}

    /* ---------- SELECCIÃ“N MÃšLTIPLE ---------- */

    const lote        = $('documentos-lote');
    const loteTexto   = $('documentos-lote-texto');
    const loteCancel  = $('documentos-lote-cancelar');
    const loteBorrar  = $('documentos-lote-eliminar');

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
                ? '1 documento seleccionado'
                : n + ' documentos seleccionados';
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
                    avisar(j.borradas.length === 1 ? 'documento eliminado.' : j.borradas.length + ' documentos eliminados.');
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
            ? 'Vas a eliminar 1 documento. Esta acción no se puede deshacer.'
            : 'Vas a eliminar ' + sel.length + ' documentos. Esta acción no se puede deshacer.';

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
        card.querySelector('.js-ver-documento')?.addEventListener('click', function () { abrirVisor(this); });
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

    const visor       = $('documentos-visor');
    const visorImg    = $('documentos-visor-img');
    const visorNombre = $('documentos-visor-nombre');
    const visorCerrar = $('documentos-visor-cerrar');
    let focoAntesVisor = null;

    const visorDim    = $('documentos-visor-dim');
    const visorPeso   = $('documentos-visor-peso');
    const visorTipo   = $('documentos-visor-tipo');
    const visorFecha  = $('documentos-visor-fecha');
    const visorRuta   = $('documentos-visor-ruta');
    const visorCopiar = $('documentos-visor-copiar');
    const visorUsos   = $('documentos-visor-usos');
    const visorAbrir  = $('documentos-visor-abrir');
    const visorPrev   = $('documentos-visor-prev');
    const visorNext   = $('documentos-visor-next');

    let botonActual = null;

    // Solo las que se ven ahora: si hay un filtro puesto, las flechas
    // recorren ese subconjunto y no la documentos entera.
    function botonesVisibles() {
        return tarjetas()
            .filter((c) => !c.hidden)
            .map((c) => c.querySelector('.js-ver-documento'))
            .filter(Boolean);
    }

    function rellenarFicha(boton) {
        const d = boton.dataset;

        visorNombre.textContent = d.nombre || '';
        if (visorPeso)  visorPeso.textContent  = d.peso || 'â€”';
        if (visorTipo)  visorTipo.textContent  = d.tipo || 'â€”';
        if (visorFecha) visorFecha.textContent = d.fecha || 'â€”';

        if (visorRuta)   visorRuta.textContent = d.ruta || '';
        if (visorCopiar) visorCopiar.dataset.ruta = d.ruta || '';
        if (visorAbrir)  visorAbrir.href = d.src || '#';

        // El tamaÃ±o real se lee de la propia documento al cargarla: pedÃ­rselo al
        // servidor para las 107 de la rejilla ralentizarÃ­a la pantalla.
        if (visorDim) {
            visorDim.textContent = 'Midiendoâ€¦';
            const medir = () => {
                visorDim.textContent = visorImg.naturalWidth
                    ? visorImg.naturalWidth + ' Ã— ' + visorImg.naturalHeight + ' px'
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
                li.textContent = 'No se estÃ¡ usando en ninguna secciÃ³n. Se puede eliminar.';
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
        if (!boton.dataset.src) return;
        window.open(boton.dataset.src, '_blank');
    }

    function mover(paso) {
        const lista = botonesVisibles();
        const i = lista.indexOf(botonActual);
        const siguiente = lista[i + paso];
        if (siguiente) abrirVisor(siguiente);
    }

    visorPrev?.addEventListener('click', () => mover(-1));
    visorNext?.addEventListener('click', () => mover(1));

    // Fondo de la vista previa. Por defecto liso; quien necesite comprobar la
    // transparencia de un logo puede poner el tablero solo mientras lo mira.
    const lienzo = document.querySelector('.documentos-visor__lienzo');

    function ponerFondo(nombre) {
        if (lienzo) lienzo.dataset.fondo = nombre;
        document.querySelectorAll('[data-fondo]').forEach((b) => {
            if (!b.matches('.documentos-fondo')) return;
            const activo = b.dataset.fondo === nombre;
            b.classList.toggle('is-activo', activo);
            b.setAttribute('aria-pressed', String(activo));
        });
        try { localStorage.setItem('itb-documentos-fondo', nombre); } catch (err) {}
    }

    document.querySelectorAll('.documentos-fondo').forEach((b) => {
        b.addEventListener('click', () => ponerFondo(b.dataset.fondo));
    });

    try {
        const guardado = localStorage.getItem('itb-documentos-fondo');
        if (guardado) ponerFondo(guardado);
    } catch (err) {}
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

    // Escape cierra la ventana que estÃ© abierta, la de subir o la del visor.
    document.addEventListener('keydown', (e) => {
        const visorAbierto = visor && !visor.classList.contains('is-hidden');

        if (e.key === 'Escape') {
            if (visorAbierto) cerrarVisor();
            else if (modal && !modal.classList.contains('is-hidden')) cerrarModal();
            return;
        }

        // Recorrer la documentos con las flechas, como en cualquier visor.
        if (!visorAbierto) return;
        if (e.key === 'ArrowLeft')  { e.preventDefault(); mover(-1); }
        if (e.key === 'ArrowRight') { e.preventDefault(); mover(1); }
    });
})();


