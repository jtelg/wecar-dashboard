/**
 * WeCar — Entity Select (Propietario) v9
 */
(function ($) {
    'use strict';

    console.log('[WeCar] v9 loaded');

    var D = window.wecarEntityData || {};
    var metaKey      = D.metaKey      || 'vehica_41299';
    var selected     = D.selected     || '';
    var partners     = D.partners     || [];
    var particulares = D.particulares || [];
    var propios      = D.propios      || [];

    var ORIGINS = ['propio', 'partner', 'particular'];
    var currentSelection = selected;
    var lastOrigen = '';

    function esc(str) {
        return $('<span>').text(str).html();
    }

    // ─── Origen detection ──────────────────────────────────────────
    function getOrigen() {
        // 1. Buscar por name exacto (Vehica usa name="vehica_41298")
        var $byName = $('select[name="vehica_41298"], select[name*="41298"], select[name*="origen"]');
        if ($byName.length) {
            var v = String($byName.first().val()).toLowerCase().trim();
            if (ORIGINS.indexOf(v) !== -1) return v;
            // Si el value es un ID, mapear por texto del option seleccionado
            var $opt = $byName.first().find('option:selected');
            if ($opt.length) {
                var t = $opt.text().toLowerCase().trim();
                if (t.indexOf('propio') !== -1) return 'propio';
                if (t.indexOf('partner') !== -1) return 'partner';
                if (t.indexOf('particular') !== -1) return 'particular';
            }
        }

        // 2. Fallback: buscar en todos los selects
        var found = '';
        $('select').each(function () {
            var v = String($(this).val()).toLowerCase().trim();
            if (ORIGINS.indexOf(v) !== -1) found = v;
        });
        return found;
    }

    function setOrigen(type) {
        var $sel = $('select[name="vehica_41298"], select[name*="41298"], select[name*="origen"]').first();
        if (!$sel.length) {
            $('select').each(function () {
                var $opts = $(this).find('option');
                var has = false;
                $opts.each(function () {
                    var t = $(this).text().toLowerCase();
                    if (t.indexOf('propio') !== -1 || t.indexOf('partner') !== -1 || t.indexOf('particular') !== -1) {
                        has = true;
                    }
                });
                if (has) { $sel = $(this); return false; }
            });
        }
        if (!$sel.length) { console.log('[WeCar] setOrigen: no select found'); return; }

        // Buscar option con texto que coincida
        var targetVal = type;
        $sel.find('option').each(function () {
            var t = $(this).text().toLowerCase().trim();
            if (t.indexOf(type) !== -1) {
                targetVal = $(this).val();
                return false;
            }
        });

        console.log('[WeCar] setOrigen setting to:', targetVal, '(type:', type, ')');
        
        var el = $sel[0];
        
        // Method 1: jQuery val + trigger
        $sel.val(targetVal).trigger('change');
        
        // Method 2: Native value + HTMLEvents (legacy API more reliable)
        try {
            el.value = targetVal;
            var evt = document.createEvent('HTMLEvents');
            evt.initEvent('change', true, true);
            el.dispatchEvent(evt);
        } catch(e) {}
        
        // Method 3: Standard Event API
        try {
            el.dispatchEvent(new Event('change', { bubbles: true, cancelable: true }));
            el.dispatchEvent(new Event('input', { bubbles: true, cancelable: true }));
        } catch(e) {}
        
        // Method 4: Focus + change + blur (simulates real user interaction)
        try {
            el.focus();
            setTimeout(function() {
                try { el.dispatchEvent(new Event('change', { bubbles: true })); } catch(e) {}
            }, 50);
        } catch(e) {}
    }

    // ─── Search / Filter ──────────────────────────────────────────
    function buildOptions(list, filterText) {
        var html = '';
        for (var i = 0; i < list.length; i++) {
            var title = list[i].title || '';
            if (filterText) {
                var lower = title.toLowerCase();
                var q = filterText.toLowerCase();
                if (lower.indexOf(q) === -1) continue;
            }
            // highlight matching text
            if (filterText) {
                var re = new RegExp('(' + filterText.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
                title = title.replace(re, '<mark>$1</mark>');
            }
            html += '<option value="' + esc(list[i].id) + '">' + title + '</option>';
        }
        return html;
    }

    // ─── Build entity panel ──────────────────────────────────────────
    function build(currentType, skipDropdown) {
        var list = [];
        if (currentType === 'partner')     list = partners;
        else if (currentType === 'particular') list = particulares;
        else if (currentType === 'propio')  list = propios;
        else list = [].concat(partners, particulares, propios);

        var $container = $('#wecar-entity-container');
        if (!$container.length) {
            $container = $('<div id="wecar-entity-container"></div>');
            // Insertar DESPUÉS del select de Origen
            var $origen = $('select[name="vehica_41298"], select[name*="41298"], select[name*="origen"]').first();
            var $wrapper = $origen.closest('.vehica-edit__section__inner');
            if ($wrapper.length) {
                $wrapper.append($container);
            } else {
                $origen.parent().append($container);
            }
        }
        $container.show();

        var selectedId = currentSelection;

        // Origen context
        var origenLabel = '';
        if (currentType === 'partner')     origenLabel = 'Partner';
        else if (currentType === 'particular') origenLabel = 'Particular';
        else if (currentType === 'propio')  origenLabel = 'Propio';
        else origenLabel = 'Entidad';

        var html = '<div class="wecar-entity-wrap" style="margin-top:12px;">';
        html += '<label style="font-weight:600;display:block;margin-bottom:4px;">' + esc(origenLabel) + '</label>';

        // Search box
        var searchValue = '';
        var $searchInput = $container.find('.wecar-entity-search');
        if ($searchInput.length) {
            searchValue = $searchInput.val() || '';
        }
        html += '<input type="text" class="wecar-entity-search" placeholder="Buscar ' + origenLabel.toLowerCase() + '..." value="' + esc(searchValue) + '" style="width:100%;margin-bottom:6px;padding:6px;border:1px solid #ddd;border-radius:4px;" />';

        // Select dropdown
        html += '<select class="wecar-entity-select" style="width:100%;">';
        html += '<option value="">— Seleccionar —</option>';
        html += buildOptions(list, searchValue);
        html += '</select>';
        html += '<input type="hidden" name="' + esc(metaKey) + '" value="' + esc(selectedId) + '" />';
        html += '</div>';

        // Replace content (not full container, just the inner wrap)
        var $existing = $container.find('.wecar-entity-wrap');
        if ($existing.length) {
            $existing.replaceWith($(html));
        } else {
            $container.html(html);
        }

        // Set value and wire search
        var $select = $container.find('.wecar-entity-select');
        $select.val(selectedId);

        // Re-bind search
        $container.find('.wecar-entity-search').off('input.wecar').on('input.wecar', function () {
            var q = $(this).val();
            build(currentType, true);
        });

        // Save on select change
        $select.off('change.wecar').on('change.wecar', function () {
            var val = $(this).val();
            currentSelection = val;
            $container.find('input[name="' + metaKey + '"]').val(val);
            // Also set on the original hidden input if exists
            var $orig = $('input[name="' + metaKey + '"]').first();
            if ($orig.length) $orig.val(val);
        });
    }

    // ─── Init ──────────────────────────────────────────
    function init() {
        if (typeof jQuery === 'undefined') { setTimeout(init, 100); return; }
        if (!$('#post').length) return;

        // Esperar a que Vue renderice el campo de Origen
        var checkExist = setInterval(function () {
            var $origenSelect = $('select[name="vehica_41298"], select[name*="41298"]').first();
            if ($origenSelect.length && $origenSelect.find('option').length > 1) {
                clearInterval(checkExist);
                afterOrigenReady();
            }
        }, 200);
    }

    function afterOrigenReady() {
        // Detectar Origen inicial
        var type = getOrigen();
        if (type) {
            build(type);
            lastOrigen = type;
        }

        // Observar cambios en el select de Origen (Vue lo re-renderiza)
        var $origen = $('select[name="vehica_41298"], select[name*="41298"]').first();
        var origenEl = $origen[0];

        // MutationObserver por si Vue reemplaza el DOM
        var observer = new MutationObserver(function () {
            var newType = getOrigen();
            if (newType && newType !== lastOrigen) {
                lastOrigen = newType;
                build(newType);
            }
        });
        if (origenEl) {
            observer.observe(origenEl, { attributes: true, childList: true, subtree: true });
        }

        // Tambien escuchar change por si Vue lo dispara
        $(document).on('change', 'select[name="vehica_41298"], select[name*="41298"]', function () {
            var newType = getOrigen();
            if (newType && newType !== lastOrigen) {
                lastOrigen = newType;
                build(newType);
            }
        });
    }

    $(init);
})(jQuery);
