/**
 * WeCar Dashboard — JS
 *
 * Funcionalidad frontend para el dashboard de wp-admin.
 */
(function ($) {
    'use strict';

    const STORAGE_KEY = 'wecar_filtro_fechas';

    /**
     * Leer filtro desde URL params o localStorage
     */
    function getFilter() {
        const params = new URLSearchParams(window.location.search);
        const desde = params.get('wf_desde');
        const hasta = params.get('wf_hasta');

        if (desde || hasta) {
            // Guardar en localStorage lo que vino por URL
            saveFilter(desde, hasta);
            return { desde, hasta };
        }

        // Leer de localStorage
        const stored = localStorage.getItem(STORAGE_KEY);
        if (stored) {
            try {
                return JSON.parse(stored);
            } catch (e) {
                return { desde: '', hasta: '' };
            }
        }

        return { desde: '', hasta: '' };
    }

    /**
     * Guardar filtro en localStorage
     */
    function saveFilter(desde, hasta) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ desde: desde || '', hasta: hasta || '' }));
    }

    /**
     * Inicializar el date filter en la página
     */
    function initDateFilter() {
        var $bar = $('.wecar-filter-bar');
        if (!$bar.length) return;

        var filter = getFilter();

        // Setear valores actuales en los inputs
        $bar.find('.wf-desde').val(filter.desde || '');
        $bar.find('.wf-hasta').val(filter.hasta || '');

        // Presets: llenar los inputs y submitear
        $bar.find('.wf-preset').on('click', function (e) {
            e.preventDefault();
            var days = parseInt($(this).data('days'), 10);
            var hasta = new Date();
            var desde = new Date();
            desde.setDate(desde.getDate() - days);

            var fmtDesde = formatDate(desde);
            var fmtHasta = formatDate(hasta);

            $bar.find('.wf-desde').val(fmtDesde);
            $bar.find('.wf-hasta').val(fmtHasta);
            $bar.find('.wf-submit').trigger('click');
        });

        // Limpiar filtro
        $bar.find('.wf-clear').on('click', function (e) {
            e.preventDefault();
            localStorage.removeItem(STORAGE_KEY);
            $bar.find('.wf-desde, .wf-hasta').val('');
            window.location.href = window.location.pathname + '?page=' + getPageParam();
        });

        // Submit del form: guardar en localStorage + recargar con params
        $bar.find('.wf-filter-form').on('submit', function (e) {
            e.preventDefault();
            var desde = $bar.find('.wf-desde').val();
            var hasta = $bar.find('.wf-hasta').val();
            saveFilter(desde, hasta);
            applyFilter(desde, hasta);
        });
    }

    /**
     * Aplicar filtro: redirigir con URL params
     */
    function applyFilter(desde, hasta) {
        var params = new URLSearchParams();
        params.set('page', getPageParam());
        if (desde) params.set('wf_desde', desde);
        if (hasta) params.set('wf_hasta', hasta);
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    /**
     * Obtener el param 'page' de la URL actual
     */
    function getPageParam() {
        var m = window.location.search.match(/[?&]page=([^&]+)/);
        return m ? m[1] : 'wecar-dashboard';
    }

    /**
     * Formatear fecha como YYYY-MM-DD
     */
    function formatDate(date) {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    $(function () {
        initDateFilter();

        // Si hay filtro guardado en localStorage pero NO en la URL,
        // auto-redirigir para aplicar el filtro (ej: paginación)
        var urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.get('wf_desde') && !urlParams.get('wf_hasta')) {
            var stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                try {
                    var f = JSON.parse(stored);
                    if (f.desde || f.hasta) {
                        applyFilter(f.desde, f.hasta);
                    }
                } catch (e) {}
            }
        }
    });

})(jQuery);
