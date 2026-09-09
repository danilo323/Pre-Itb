Exit code: 0
Wall time: 1 seconds
Output:
/* js/pagina-interna.js
   Acordeón + búsqueda + filtro de año de la página de Transparencia / Leyes
   (y de cualquier otra página interna que reutilice el mismo markup). */
document.addEventListener('DOMContentLoaded', function () {
    const accordion = document.getElementById('doc-accordion');
    if (!accordion) return;

    // ---- Acordeón: un clic en el header abre/cierra ese item ----
    accordion.querySelectorAll('.acc-item__header').forEach(header => {
        header.addEventListener('click', function () {
            this.closest('.acc-item').classList.toggle('is-open');
        });
    });

    // ---- Búsqueda + filtro de año sobre las filas de las tablas ----
    const searchInput = document.getElementById('doc-search');
    const yearFilter = document.getElementById('doc-year-filter');
    const rows = accordion.querySelectorAll('.acc-table__row');

    function applyFilters() {
        const term = (searchInput?.value || '').trim().toLowerCase();
        const year = yearFilter?.value || '';

        rows.forEach(row => {
            const matchesTerm = !term || (row.dataset.nombre || '').includes(term);
            const matchesYear = !year || row.dataset.anio === year;
            row.classList.toggle('is-filtered-out', !(matchesTerm && matchesYear));
        });

        // Si una búsqueda deja resultados en un acordeón cerrado, lo abrimos
        // para que el usuario no crea que no hay nada.
        if (term || year) {
            accordion.querySelectorAll('.acc-item').forEach(item => {
                const hasVisible = [...item.querySelectorAll('.acc-table__row')]
                    .some(row => !row.classList.contains('is-filtered-out'));
                if (hasVisible) item.classList.add('is-open');
            });
        }
    }

    searchInput?.addEventListener('input', applyFilters);
    yearFilter?.addEventListener('change', applyFilters);
});

