/**
 * WeCar — Entity Select with Live Search Filter
 *
 * Reemplaza el campo "Partner" de Vehica por un select buscable
 * que cambia según el Origen seleccionado.
 */
(function ($) {
    'use strict';

    var D = wecarEntityData || window.wecarEntityData || {};

    var metaKey      = D.metaKey      || 'vehica_41299';
    var selected     = D.selected     || '';
    var origenTax    = D.origenTax    || 'vehica_41298';
    var partners     = D.partners     || [];
    var particulares = D.particulares || [];
    var propios      = D.propios      || [];

    function esc(str) {
        return $('<span>').text(str).html();
    }

    function getOrigen() {
        var v = '';
        // Intentar todas las formas posibles
        $('select[name="' + origenTax + '"], select[name="tax_input[' + origenTax + ']"], .vehica-field[data-id="41298"] select').each(function () {
            if ($(this).val()) v = $(this).val();
        });
        if (v) return v;
        $('input[name="' + origenTax + '"]:checked').each(function () {
            v = $(this).val();
        });
        return v || '';
    }

    function getList() {
        var o = getOrigen();
        if (o === 'partner')    return { items: partners,     label: 'Partner' };
        if (o === 'particular') return { items: particulares, label: 'Particular' };
        if (o === 'propio')     return { items: propios,      label: 'Concesionaria propia' };
        // Fallback: mostrar todos combinados
        var all = [];
        $.each(partners, function (_, e) { all.push({ id: e.id, title: '🏢 ' + e.title }); });
        $.each(particulares, function (_, e) { all.push({ id: e.id, title: '👤 ' + e.title }); });
        $.each(propios, function (_, e) { all.push({ id: e.id, title: '🏠 ' + e.title }); });
        return { items: all, label: 'Entidad' };
    }

    function buildHTML(list, cur) {
        var label = list.label;
        var items = list.items;
        var uid   = 'wec-' + Math.random().toString(36).substr(2, 6);

        var h = '';
        // Input de búsqueda
        h += '<div class="wec-wrap" data-uid="' + uid + '">';
        h += '<input type="text" class="wec-search" placeholder="🔍 Buscar ' + label.toLowerCase() + '..."';
        h += ' style="width:100%;max-width:400px;padding:6px 10px;border:1px solid #bbb;border-radius:4px;font-size:13px;box-sizing:border-box;margin-bottom:4px;"';
        h += ' autocomplete="off">';

        // Select con TODAS las opciones
        h += '<select class="wec-select" name="' + metaKey + '"';
        h += ' style="width:100%;max-width:400px;height:0;padding:0;border:none;overflow:hidden;position:absolute;opacity:0;">';
        h += '<option value="">— Sin ' + label.toLowerCase() + ' —</option>';
        for (var i = 0; i < items.length; i++) {
            var s = (String(items[i].id) === String(cur)) ? ' selected' : '';
            h += '<option value="' + items[i].id + '"' + s + '>' + esc(items[i].title) + '</option>';
        }
        h += '</select>';

        // Lista visual de opciones
        h += '<div class="wec-options" style="width:100%;max-width:400px;max-height:180px;overflow-y:auto;border:1px solid #ddd;border-radius:4px;background:#fff;display:none;">';
        var hasSelected = false;
        for (var i = 0; i < items.length; i++) {
            var s = (String(items[i].id) === String(cur)) ? ' selected' : '';
            var selClass = s ? ' wec-opt-sel' : '';
            if (s) hasSelected = true;
            h += '<div class="wec-opt' + selClass + '" data-val="' + items[i].id + '"';
            h += ' style="padding:6px 10px;cursor:pointer;font-size:13px;border-bottom:1px solid #f0f0f0;">';
            h += esc(items[i].title) + '</div>';
        }
        if (!hasSelected && cur) {
            // Mostrar el valor actual aunque no esté en la lista
        }
        h += '</div>';

        // Valor seleccionado visible
        var showLabel = '— Sin ' + label.toLowerCase() + ' —';
        for (var i = 0; i < items.length; i++) {
            if (String(items[i].id) === String(cur)) {
                showLabel = items[i].title;
                break;
            }
        }
        h += '<div class="wec-selected" style="padding:6px 10px;border:1px solid #bbb;border-radius:4px;font-size:13px;background:#fff;cursor:pointer;width:100%;max-width:400px;box-sizing:border-box;">';
        h += esc(showLabel);
        h += ' <span style="float:right;color:#999;">▼</span></div>';
        h += '</div>';

        return h;
    }

    function bindEvents($wrap, $input) {
        var $search   = $wrap.find('.wec-search');
        var $select   = $wrap.find('.wec-select');
        var $opts     = $wrap.find('.wec-options');
        var $selected = $wrap.find('.wec-selected');

        // Click en el selected muestra/oculta opciones
        $selected.on('click', function (e) {
            e.stopPropagation();
            $opts.slideToggle(120);
            $search.val('').show().focus();
            filterOptions($search, $opts);
        });

        // Búsqueda filtra opciones
        $search.on('input', function () {
            filterOptions($(this), $opts);
        });

        // Click en opción selecciona
        $opts.on('click', '.wec-opt', function () {
            var val = $(this).data('val');
            var txt = $(this).text();
            $select.val(val);
            $input.val(val);
            $selected.html(esc(txt) + ' <span style="float:right;color:#999;">▼</span>');
            $opts.slideUp(120);
            $search.hide();
        });

        // Click afuera cierra
        $(document).on('click', function () {
            $opts.slideUp(120);
            $search.hide();
        });
    }

    function filterOptions($search, $opts) {
        var q = $search.val().toLowerCase();
        $opts.find('.wec-opt').each(function () {
            var t = $(this).text().toLowerCase();
            $(this).toggle(t.indexOf(q) !== -1);
        });
    }

    function replaceField() {
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) return false;

        var $wrapper = $input.closest('.vehica-field') || $input.closest('div');
        if ($wrapper.find('.wec-wrap').length) return true;

        var cur  = $input.val() || selected;
        var list = getList();

        $input.hide();

        var $inner = $input.closest('.vehica-edit__section__inner');
        var $html  = $(buildHTML(list, cur));

        if ($inner.length) {
            $inner.append($html);
        } else {
            $input.after($html);
        }

        bindEvents($wrapper.find('.wec-wrap'), $input);
        return true;
    }

    function rebuild() {
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) return;
        var cur  = $input.val() || selected;
        var list = getList();

        var $old = $('.wec-wrap');
        var $html = $(buildHTML(list, cur));
        $old.replaceWith($html);

        bindEvents($('.wec-wrap'), $input);
    }

    function listenOrigen() {
        var sels = [
            'select[name="' + origenTax + '"]',
            'select[name="tax_input[' + origenTax + ']"]',
            '.vehica-field[data-id="41298"] select',
            '.vehica-field:has(label:contains("Origen")) select',
        ];
        $.each(sels, function (_, sel) {
            $(document).on('change', sel, rebuild);
        });
        $(document).on('change', 'input[name="' + origenTax + '"]', rebuild);
    }

    function init() {
        if (replaceField()) { listenOrigen(); return; }
        var obs = new MutationObserver(function () {
            if (replaceField()) { obs.disconnect(); listenOrigen(); }
        });
        obs.observe(document.body, { childList: true, subtree: true });
        setTimeout(function () { obs.disconnect(); }, 15000);
    }

    $(init);
})(jQuery);
