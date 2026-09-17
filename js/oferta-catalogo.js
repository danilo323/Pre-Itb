/* =============================================
   OFERTA-CATALOGO.JS — Catálogo de Oferta Académica
   PROPÓSITO: filtrar, buscar, ordenar y paginar las tarjetas de programa que
   includes/oferta-catalogo.php ya dejó pintadas en el HTML.

   Trabaja SIEMPRE sobre las tarjetas que ya están en la página: no pide nada al
   servidor. Por eso, si este archivo no carga, la página sigue mostrando todos
   los programas; solo se quedan quietos los filtros.
   ============================================= */
document.addEventListener('DOMContentLoaded', function () {
    const catalogo = document.querySelector('.js-catalogo');
    if (!catalogo) return;

    const lista       = catalogo.querySelector('.js-catalogo-lista');
    const tarjetas    = Array.from(catalogo.querySelectorAll('.js-catalogo-card'));
    const conteo      = catalogo.querySelector('.js-catalogo-conteo');
    const paginacion  = catalogo.querySelector('.js-catalogo-paginacion');
    const vacio       = catalogo.querySelector('.js-catalogo-vacio');
    const orden       = catalogo.querySelector('.js-catalogo-orden');
    const btnBorrar   = catalogo.querySelector('.js-filtros-borrar');
    const checks      = Array.from(catalogo.querySelectorAll('.js-filtro-check'));
    const radiosTipo  = Array.from(catalogo.querySelectorAll('.js-filtro-tipo'));

    // El buscador está fuera de .js-catalogo (vive en su propia franja gris)
    const seccion   = catalogo.closest('.catalogo') || document;
    const buscador  = seccion.querySelector('#catalogo-busqueda');
    const btnBuscar = seccion.querySelector('.js-catalogo-buscar');

    if (!lista || !tarjetas.length) return;

    const porPagina = Math.max(1, parseInt(catalogo.dataset.porPagina, 10) || 4);
    let paginaActual = 1;

    // Compara sin tildes ni mayúsculas: buscar "administracion" tiene que
    // encontrar "Administración".
    const normalizar = (valor) => (valor || '')
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    // Una tarjeta con "Presencial / Híbrida" cumple tanto el filtro Presencial
    // como el Híbrida, así que su dato se parte igual que en el PHP.
    const partes = (valor) => normalizar(valor).split('/').map((p) => p.trim()).filter(Boolean);

    function seleccionados(nombre) {
        return checks
            .filter((c) => c.checked && c.dataset.filtro === nombre)
            .map((c) => normalizar(c.value));
    }

    function tipoElegido() {
        const marcado = radiosTipo.find((r) => r.checked);
        return marcado ? normalizar(marcado.value) : '';
    }

    // Un grupo sin nada marcado no filtra: deja pasar todo.
    function coincide(tarjeta) {
        const termino = normalizar(buscador ? buscador.value.trim() : '');
        if (termino && !normalizar(tarjeta.dataset.busqueda).includes(termino)) return false;

        // El tipo se parte igual que la modalidad: un programa marcado como
        // "Cursos / Programas" tiene que salir con cualquiera de los dos radios.
        const tipo = tipoElegido();
        if (tipo && !partes(tarjeta.dataset.tipo).includes(tipo)) return false;

        const modalidades = seleccionados('modalidad');
        if (modalidades.length) {
            const propias = partes(tarjeta.dataset.modalidad);
            if (!modalidades.some((m) => propias.includes(m))) return false;
        }

        const anios = seleccionados('anio');
        if (anios.length && !anios.includes(normalizar(tarjeta.dataset.anio))) return false;

        const campos = seleccionados('campo');
        if (campos.length && !campos.includes(normalizar(tarjeta.dataset.campo))) return false;

        return true;
    }

    function ordenar(visibles) {
        const modo = orden ? orden.value : 'relevancia';
        const porTitulo = (a, b) => normalizar(a.dataset.titulo).localeCompare(normalizar(b.dataset.titulo), 'es');

        if (modo === 'az')   return visibles.slice().sort(porTitulo);
        if (modo === 'za')   return visibles.slice().sort((a, b) => porTitulo(b, a));
        if (modo === 'anio') return visibles.slice().sort((a, b) => (a.dataset.anio || '').localeCompare(b.dataset.anio || ''));

        // "Relevancia" = el orden con el que vinieron del panel
        return visibles.slice().sort((a, b) => (+a.dataset.orden) - (+b.dataset.orden));
    }

    function textoConteo(desde, hasta, total) {
        const plantilla = (conteo && conteo.dataset.plantilla) || 'Mostrando {desde}-{hasta} de {total} resultados';
        return plantilla
            .replace('{desde}', desde)
            .replace('{hasta}', hasta)
            .replace('{total}', total);
    }

    function boton(texto, { activa = false, desactivado = false, pagina = null, aria = '' } = {}) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'catalogo__pag-btn' + (activa ? ' is-activa' : '');
        b.innerHTML = texto;
        if (desactivado) b.disabled = true;
        if (aria) b.setAttribute('aria-label', aria);
        if (activa) b.setAttribute('aria-current', 'page');
        if (pagina !== null) {
            b.addEventListener('click', function () {
                paginaActual = pagina;
                render(false);
                // Sin esto, al pasar de página se sigue viendo el final de la
                // lista anterior y parece que no pasó nada.
                seccion.querySelector('.catalogo__barra')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        }
        return b;
    }

    // Con muchas páginas no caben todos los números: se muestran la primera,
    // las vecinas de la actual y la última, con "..." en los huecos.
    function numerosVisibles(total, actual) {
        if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);

        const nums = new Set([1, total, actual]);
        if (actual - 1 > 1) nums.add(actual - 1);
        if (actual + 1 < total) nums.add(actual + 1);
        if (actual <= 3) { nums.add(2); nums.add(3); nums.add(4); }
        if (actual >= total - 2) { nums.add(total - 1); nums.add(total - 2); nums.add(total - 3); }

        return Array.from(nums).filter((n) => n >= 1 && n <= total).sort((a, b) => a - b);
    }

    function pintarPaginacion(totalPaginas) {
        if (!paginacion) return;
        paginacion.innerHTML = '';
        if (totalPaginas <= 1) return;

        let anterior = 0;
        numerosVisibles(totalPaginas, paginaActual).forEach((n) => {
            if (n - anterior > 1) {
                const sep = document.createElement('span');
                sep.className = 'catalogo__pag-sep';
                sep.textContent = '..';
                paginacion.appendChild(sep);
            }
            paginacion.appendChild(boton(String(n), { activa: n === paginaActual, pagina: n }));
            anterior = n;
        });

        paginacion.appendChild(boton('<i class="fas fa-chevron-right" aria-hidden="true"></i>', {
            pagina: Math.min(totalPaginas, paginaActual + 1),
            desactivado: paginaActual === totalPaginas,
            aria: 'Página siguiente'
        }));
    }

    // reiniciar = true cuando cambia el filtro o la búsqueda: el resultado es
    // otro, así que se vuelve a la página 1. En false solo se cambia de página.
    function render(reiniciar = true) {
        if (reiniciar) paginaActual = 1;

        const visibles = ordenar(tarjetas.filter(coincide));
        const total = visibles.length;
        const totalPaginas = Math.max(1, Math.ceil(total / porPagina));
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;

        const inicio = (paginaActual - 1) * porPagina;
        const fin = Math.min(inicio + porPagina, total);
        const pagina = visibles.slice(inicio, fin);

        tarjetas.forEach((t) => t.classList.add('is-oculta'));
        // Reinsertar en orden deja el DOM igual que la lista ordenada: el
        // navegador respeta el orden de los hijos, no el de un array aparte.
        pagina.forEach((t) => {
            t.classList.remove('is-oculta');
            lista.appendChild(t);
        });

        if (conteo) conteo.textContent = textoConteo(total ? inicio + 1 : 0, fin, total);
        if (vacio) vacio.hidden = total > 0;
        pintarPaginacion(totalPaginas);
    }

    // ---- Eventos ----
    checks.forEach((c) => c.addEventListener('change', () => render()));
    radiosTipo.forEach((r) => r.addEventListener('change', () => render()));
    if (orden) orden.addEventListener('change', () => render());

    if (buscador) {
        buscador.addEventListener('input', () => render());
        buscador.addEventListener('keydown', (e) => {
            // El campo es type="search" dentro de la página: Enter no debe
            // recargar nada, solo filtrar.
            if (e.key === 'Enter') { e.preventDefault(); render(); }
        });
    }
    if (btnBuscar) btnBuscar.addEventListener('click', () => render());

    if (btnBorrar) {
        btnBorrar.addEventListener('click', function () {
            checks.forEach((c) => { c.checked = false; });
            if (radiosTipo.length) radiosTipo[0].checked = true;
            if (buscador) buscador.value = '';
            if (orden) orden.value = 'relevancia';
            render();
        });
    }

    // Plegar / desplegar cada grupo de filtros
    catalogo.querySelectorAll('.js-filtro-toggle').forEach((toggle) => {
        toggle.addEventListener('click', function () {
            const grupo = this.closest('.js-filtro-grupo');
            const cerrado = grupo.classList.toggle('is-cerrado');
            this.setAttribute('aria-expanded', cerrado ? 'false' : 'true');
        });
    });

    render();
});
