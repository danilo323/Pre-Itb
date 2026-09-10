/* =============================================
   PAGINA-INTERNA.JS — Lógica Específica
   PROPÓSITO: Este script SOLO se carga en las páginas internas (como Transparencia/Leyes).
   Se separa del main.js para no sobrecargar las demás páginas con código que no necesitan (Escalabilidad).
   Maneja: Acordeones, búsqueda y filtro anual de documentos.
   ============================================= */
document.addEventListener('DOMContentLoaded', function () {
    const accordion = document.getElementById('doc-accordion');
    if (!accordion) return;

    const normalize = (value) => (value || '')
        .toLocaleLowerCase('es')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');

    const headers = Array.from(accordion.querySelectorAll('.acc-item__header'));
    const searchInput = document.getElementById('doc-search');
    const searchButton = document.querySelector('.transparencia__search-btn');
    const yearFilter = document.getElementById('doc-year-filter');
    const rows = Array.from(accordion.querySelectorAll('.acc-table__row'));

    headers.forEach((header) => {
        header.setAttribute('aria-expanded', 'false');
        header.addEventListener('click', function () {
            const item = this.closest('.acc-item');
            const open = !item.classList.contains('is-open');
            item.classList.toggle('is-open', open);
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    function applyFilters() {
        const term = normalize(searchInput ? searchInput.value.trim() : '');
        const year = yearFilter ? yearFilter.value : '';

        rows.forEach((row) => {
            const matchesTerm = !term || normalize(row.dataset.nombre).includes(term);
            const matchesYear = !year || row.dataset.anio === year;
            row.classList.toggle('is-filtered-out', !(matchesTerm && matchesYear));
        });

        if (term || year) {
            accordion.querySelectorAll('.acc-item').forEach((item) => {
                const hasVisible = Array.from(item.querySelectorAll('.acc-table__row'))
                    .some((row) => !row.classList.contains('is-filtered-out'));
                if (hasVisible) {
                    item.classList.add('is-open');
                    const header = item.querySelector('.acc-item__header');
                    if (header) header.setAttribute('aria-expanded', 'true');
                }
            });
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (yearFilter) yearFilter.addEventListener('change', applyFilters);
    if (searchButton) searchButton.addEventListener('click', applyFilters);
});