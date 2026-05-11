/**
 * WeCar — Entity Select (Propietario)
 *
 * Reemplaza el campo "Partner" por "Propietario" con select buscable.
 * Muestra partners/particulares/propios según el Origen elegido.
 */
(function ($) {
    'use strict';

    var D = window.wecarEntityData || {};
    var metaKey      = D.metaKey      || 'vehica_41299';
    var selected     = D.selected     || '';
    var partners     = D.partners     || [];
    var particulares = D.particulares || [];
    var propios      = D.propios      || [];

    var ORIGINS = ['propio', 'partner', 'particular'];

    function esc(str) {
        return $('<span>').text(str).html();
    }

    /**
     * Detectar Origen: buscar en TODOS los selectores posibles
     */
    function getOrigen() {
        var val = '';
        // Buscar en todos los select
        $('select').each(function () {
            var v = $(this).val();
            if (ORIGINS.indexOf(v) !== -1) val = v;
        });
        if (val) return val;
        // Buscar en radios
        $('input[type="radio"]:checked').each(function () {
            var v = $(this).val();
            if (ORIGINS.indexOf(v) !== -1) val = v;
        });
        return val;
    }

    function getEntities() {
        var o = getOrigen();
        if (o === 'partner')    return { items: partners,     label: 'Partner' };
        if (o === 'particular') return { items: particulares, label: 'Particular' };
        if (o === 'propio')     return { items: propios,      label: 'Propio' };
        return { items: [], label: 'Entidad' };
    }

    function findLabel($input) {
        var $field = $input.closest('.vehica-field');
        if ($field.length) {
            var $lbl = $field.find('label').first();
            if ($lbl.length) return $lbl;
        }
        return null;
    }

    function buildPanel(items, cur) {
        var uid = 'wec-' + Math.random().toString(36).substr(2, 6);
        var h = '<div class="wec-panel" data-uid="' + uid + '" style="display:none;">';
        h += '<div class="wec-panel-inner" style="border:1px solid #ccc;border-radius:4px;background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.15);">';

        // Buscador
        h += '<div style="padding:8px;border-bottom:1px solid #eee;">';
        h += '<input type="text" class="wec-search" placeholder="Buscar..."';
        h += ' style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:3px;font-size:13px;box-sizing:border-box;"';
        h += ' autocomplete="off">';
        h += '</div>';

        // Opciones
        h += '<div class="wec-list" style="max-height:200px;overflow-y:auto;">';
        h += '<div class="wec-opt" data-val="" style="padding:8px 12px;cursor:pointer;font-size:13px;color:#888;border-bottom:1px solid #f5f5f5;">— Sin seleccionar —</div>';
        for (var i = 0; i < items.length; i++) {
            var s = (String(items[i].id) === String(cur)) ? ' style="background:#e5f0fa;font-weight:600;"' : '';
            h += '<div class="wec-opt" data-val="' + items[i].id + '"' + s;
            h += ' style="padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid #f5f5f5;">';
            h += esc(items[i].title) + '</div>';
        }
        h += '</div>';
        h += '</div></div>';
        return h;
    }

    function buildTrigger(label, cur, items) {
        var txt = '— Seleccionar ' + label.toLowerCase() + ' —';
        for (var i = 0; i < items.length; i++) {
            if (String(items[i].id) === String(cur)) {
                txt = items[i].title;
                break;
            }
        }
        return '<div class="wec-trigger" style="padding:8px 12px;border:1px solid #bbb;border-radius:4px;background:#fff;cursor:pointer;font-size:13px;width:100%;max-width:400px;box-sizing:border-box;position:relative;z-index:1;">' +
            esc(txt) + ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">▼</span>' +
            '</div>';
    }

    function render($input) {
        var $field  = $input.closest('.vehica-field') || $input.closest('div');
        var $inner  = $input.closest('.vehica-edit__section__inner');
        var cur     = $input.val() || selected;
        var list    = getEntities();

        // Cambiar label a "Propietario"
        var $lbl = findLabel($input);
        if ($lbl && $lbl.text().indexOf('Propietario') === -1) {
            $lbl.text('Propietario');
        }

        // Input oculto real
        $input.hide();

        // HTML del componente
        var html = '<div class="wec-wrap" style="position:relative;">';
        html += buildTrigger(list.label, cur, list.items);
        html += '<select name="' + metaKey + '" style="display:none;">';
        html += '<option value="">—</option>';
        for (var i = 0; i < list.items.length; i++) {
            var s = (String(list.items[i].id) === String(cur)) ? ' selected' : '';
            html += '<option value="' + list.items[i].id + '"' + s + '>' + esc(list.items[i].title) + '</option>';
        }
        html += '</select>';
        html += buildPanel(list.items, cur);
        html += '</div>';

        var $html = $(html);

        if ($inner.length) {
            $inner.append($html);
        } else {
            $input.after($html);
        }

        bindEvents($html, $input);
    }

    function bindEvents($wrap, $input) {
        var $trigger = $wrap.find('.wec-trigger');
        var $panel   = $wrap.find('.wec-panel');
        var $search  = $wrap.find('.wec-search');
        var $select  = $wrap.find('select');

        // Abrir/cerrar panel
        $trigger.on('click', function (e) {
            e.stopPropagation();
            $('.wec-panel').not($panel).hide();
            $panel.toggle();
            if ($panel.is(':visible')) {
                $search.val('').focus();
                filterOptions($search, $panel);
            }
        });

        // Filtrar al escribir
        $search.on('input', function () {
            filterOptions($(this), $panel);
        });

        // Seleccionar opción
        $panel.on('click', '.wec-opt', function (e) {
            e.stopPropagation();
            var val = $(this).data('val');
            var txt = $(this).text();
            $select.val(val);
            $input.val(val);
            $trigger.html(esc(txt) + ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">▼</span>');
            $panel.hide();
        });

        // Cerrar al click afuera
        $(document).on('click', function () {
            $panel.hide();
        });
    }

    function filterOptions($search, $panel) {
        var q = $search.val().toLowerCase();
        $panel.find('.wec-opt').each(function () {
            var t = $(this).text().toLowerCase();
            $(this).toggle(t.indexOf(q) !== -1);
        });
    }

    function rebuild() {
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) return;

        var cur  = $input.val() || selected;
        var list = getEntities();

        var $wrap = $input.siblings('.wec-wrap');
        if (!$wrap.length) { render($input); return; }

        // Solo reemplazar las opciones, no todo el componente
        var $panel = $wrap.find('.wec-panel');
        var $trigger = $wrap.find('.wec-trigger');

        // Nuevo panel
        var $newPanel = $(buildPanel(list.items, cur));
        $panel.replaceWith($newPanel);

        // Actualizar trigger
        var txt = '— Seleccionar ' + list.label.toLowerCase() + ' —';
        for (var i = 0; i < list.items.length; i++) {
            if (String(list.items[i].id) === String(cur)) {
                txt = list.items[i].title;
                break;
            }
        }
        $trigger.html(esc(txt) + ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">▼</span>');

        // Actualizar select oculto
        var $select = $wrap.find('select');
        var opts = '<option value="">—</option>';
        for (var i = 0; i < list.items.length; i++) {
            var s = (String(list.items[i].id) === String(cur)) ? ' selected' : '';
            opts += '<option value="' + list.items[i].id + '"' + s + '>' + esc(list.items[i].title) + '</option>';
        }
        $select.html(opts);

        bindEvents($wrap, $input);
    }

    function init() {
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) {
            // Esperar a que Vue renderice
            var obs = new MutationObserver(function () {
                $input = $('input[name="' + metaKey + '"]');
                if ($input.length) { obs.disconnect(); render($input); watchOrigen(); }
            });
            obs.observe(document.body, { childList: true, subtree: true });
            setTimeout(function () { obs.disconnect(); }, 15000);
            return;
        }
        render($input);
        watchOrigen();
    }

    /**
     * Observar cambios en el campo Origen usando MutationObserver
     * (más confiable que eventos con Vue)
     */
    function watchOrigen() {
        // Observar TODOS los selectores del documento
        var obs = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    var val = $(mutation.target).val();
                    if (ORIGINS.indexOf(val) !== -1) {
                        rebuild();
                    }
                }
            });
        });

        $('select').each(function () {
            obs.observe(this, { attributes: true, attributeFilter: ['value'] });
        });

        // También escuchar eventos change como fallback
        $(document).on('change', 'select', function () {
            var v = $(this).val();
            if (ORIGINS.indexOf(v) !== -1) rebuild();
        });

        // Y escuchar inputs (Vue a veces dispara input en vez de change)
        $(document).on('input', 'select', function () {
            var v = $(this).val();
            if (ORIGINS.indexOf(v) !== -1) rebuild();
        });
    }

    $(init);
})(jQuery);
