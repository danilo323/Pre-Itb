// js/oferta-programas.js
//
// Filtrado, búsqueda, orden y paginación del buscador de Oferta Académica
// (includes/oferta-programas.php). Todo pasa en el navegador sobre las
// tarjetas .oferta-card que PHP ya pintó: no hay recarga de página ni
// llamadas al servidor por cada clic.
(function () {
    const grid = document.getElementById('oferta-buscador-grid');
    if (!grid) return; // La sección no está en esta página (o está oculta).

    const tarjetas = Array.from(grid.querySelectorAll('.oferta-card'));
    const inputBuscar = document.getElementById('oferta-buscador-input');
    const selectOrden = document.getElementById('oferta-buscador-orden');
    const contador = document.getElementById('oferta-buscador-contador');
    const vacio = document.getElementById('oferta-buscador-vacio');
    const paginacion = document.getElementById('oferta-buscador-paginacion');
    const btnBorrar = document.getElementById('oferta-buscador-borrar');
    const radiosTipo = document.querySelectorAll('[data-filtro-tipo]');
    const checksFiltro = document.querySelectorAll('[data-filtro]');

    const POR_PAGINA = 4;
    let paginaActual = 1;

    // Al cambiar de página, la vista sube al principio de los resultados.
    //
    // Antes se llevaba a la paginación, que está al final de la lista: como
    // suele quedar fuera de pantalla, el navegador bajaba la vista para
    // enseñarla y parecía que la página "se iba hacia abajo" sola. Y si la
    // página nueva traía menos tarjetas, el salto era aún mayor.
    //
    // Solo se mueve si el principio de la lista quedó por encima de la
    // ventana: si ya lo estás viendo, no tiene sentido moverte nada.
    function subirAResultados() {
        const ancla = document.querySelector('.oferta-buscador__resultados-head') || grid;
        if (!ancla) return;

        const margen = 110; // hueco para la cabecera fija
        const arriba = ancla.getBoundingClientRect().top;
        if (arriba >= margen) return;

        const destino = Math.max(0, window.scrollY + arriba - margen);
        const sinAnimacion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: destino, behavior: sinAnimacion ? 'auto' : 'smooth' });
    }

    function filtrosActivos(nombreFiltro) {
        return Array.from(checksFiltro)
            .filter((c) => c.dataset.filtro === nombreFiltro && c.checked)
            .map((c) => c.value);
    }

    function aplicarFiltros() {
        const tipo = document.querySelector('[data-filtro-tipo]:checked')?.value || 'Programa';
        const modalidades = filtrosActivos('modalidad');
        const anios = filtrosActivos('anio');
        const campos = filtrosActivos('campo');
        const texto = (inputBuscar?.value || '').trim().toLowerCase();

        return tarjetas.filter((card) => {
            if (card.dataset.tipo !== tipo) return false;
            if (modalidades.length && !modalidades.includes(card.dataset.modalidad)) return false;
            if (anios.length && !anios.includes(card.dataset.anio)) return false;
            if (campos.length && !campos.includes(card.dataset.campo)) return false;
            if (texto && !card.dataset.nombre.includes(texto)) return false;
            return true;
        });
    }

    function ordenar(lista) {
        const modo = selectOrden?.value || 'relevancia';
        if (modo === 'relevancia') return lista;
        const copia = lista.slice();
        copia.sort((a, b) => {
            const cmp = a.querySelector('.oferta-card__title').textContent
                .localeCompare(b.querySelector('.oferta-card__title').textContent, 'es');
            return modo === 'az' ? cmp : -cmp;
        });
        return copia;
    }

    function pintarPaginacion(total, paginas) {
        paginacion.innerHTML = '';
        if (paginas <= 1) return;

        const crearBoton = (etiqueta, pagina, opciones = {}) => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.textContent = etiqueta;

            // Los números se leen solos, pero las flechas no dicen nada: sin
            // esto, quien use lector de pantalla oye "botón" y poco más.
            boton.setAttribute('aria-label', opciones.etiquetaLarga || `Ir a la página ${pagina}`);

            if (opciones.activa) {
                boton.classList.add('is-activa');
                // Así es como un lector de pantalla anuncia "página actual".
                boton.setAttribute('aria-current', 'page');
                boton.setAttribute('aria-label', `Página ${pagina}, página actual`);
                // Ya estás en ella: pulsarla no lleva a ninguna parte.
                boton.disabled = true;
            }
            if (opciones.deshabilitada) boton.disabled = true;

            boton.addEventListener('click', () => {
                paginaActual = pagina;
                render({ animar: true });
                subirAResultados();
                // Tras repintar, el foco vuelve al número de la página en la que
                // se acaba de entrar, para no perderlo al principio del documento.
                const nuevoActivo = paginacion.querySelector('.is-activa');
                if (nuevoActivo) nuevoActivo.focus({ preventScroll: true });
            });
            return boton;
        };

        paginacion.appendChild(crearBoton('‹', paginaActual - 1, {
            deshabilitada: paginaActual === 1,
            etiquetaLarga: 'Página anterior'
        }));
        for (let p = 1; p <= paginas; p++) {
            paginacion.appendChild(crearBoton(String(p), p, { activa: p === paginaActual }));
        }
        paginacion.appendChild(crearBoton('›', paginaActual + 1, {
            deshabilitada: paginaActual === paginas,
            etiquetaLarga: 'Página siguiente'
        }));

        // "Página 2 de 3" para quien no ve los botones. No se muestra en
        // pantalla; lo lee el lector cuando cambia.
        const estado = document.createElement('span');
        estado.className = 'oferta-buscador__paginacion-estado';
        estado.textContent = `Página ${paginaActual} de ${paginas}`;
        paginacion.appendChild(estado);
    }

    function render(opciones = {}) {
        const filtradas = ordenar(aplicarFiltros());
        const total = filtradas.length;
        const paginas = Math.max(1, Math.ceil(total / POR_PAGINA));
        if (paginaActual > paginas) paginaActual = paginas;

        tarjetas.forEach((card) => {
            card.hidden = true;
            card.classList.remove('is-entrando');
            card.style.removeProperty('--orden-entrada');
        });

        const inicio = (paginaActual - 1) * POR_PAGINA;
        const visibles = filtradas.slice(inicio, inicio + POR_PAGINA);
        visibles.forEach((card) => { card.hidden = false; });

        // Las tarjetas nuevas entran con una animación corta y escalonada, para
        // que se note que la lista cambió. Solo tras una acción (cambiar de
        // página, filtrar, buscar): en la carga inicial no hace falta.
        if (opciones.animar && visibles.length) {
            // Forzar un reflujo antes de poner la clase, o el navegador agrupa
            // el quitar y el poner y la animación no llega a verse.
            void grid.offsetWidth;
            visibles.forEach((card, i) => {
                card.style.setProperty('--orden-entrada', String(i));
                card.classList.add('is-entrando');
            });
        }

        if (contador) {
            if (total === 0) {
                contador.textContent = 'No hay resultados';
            } else {
                const desde = inicio + 1;
                const hasta = Math.min(inicio + POR_PAGINA, total);
                let texto = `Mostrando ${desde}-${hasta} de ${total} resultado${total === 1 ? '' : 's'}`;
                // Con más de una página se dice en cuál estás, además de
                // resaltarla en los botones de abajo.
                if (paginas > 1) texto += ` · Página ${paginaActual} de ${paginas}`;
                contador.textContent = texto;
            }
        }
        if (vacio) vacio.hidden = total !== 0;

        pintarPaginacion(total, paginas);
    }

    function reiniciarYRenderizar() {
        paginaActual = 1;
        render({ animar: true });
    }

    inputBuscar?.addEventListener('input', reiniciarYRenderizar);
    selectOrden?.addEventListener('change', reiniciarYRenderizar);
    radiosTipo.forEach((r) => r.addEventListener('change', reiniciarYRenderizar));
    checksFiltro.forEach((c) => c.addEventListener('change', reiniciarYRenderizar));

    btnBorrar?.addEventListener('click', () => {
        checksFiltro.forEach((c) => { c.checked = false; });
        if (inputBuscar) inputBuscar.value = '';
        if (selectOrden) selectOrden.value = 'relevancia';
        const radioProgramas = document.querySelector('[data-filtro-tipo][value="Programa"]');
        if (radioProgramas) radioProgramas.checked = true;
        reiniciarYRenderizar();
    });

    render();
})();
