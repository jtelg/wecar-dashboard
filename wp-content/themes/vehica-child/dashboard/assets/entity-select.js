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
            el.value = targetVal;
            el.dispatchEvent(new Event('change', { bubbles: true }));
            el.blur();
        } catch(e) {}
        
        // Method 5: Vue internals
        try {
            if (el.__vue__) {
                var vue = el.__vue__;
                if (vue.$data) {
                    vue.$data.value = targetVal;
                    vue.$forceUpdate && vue.$forceUpdate();
                }
            }
            // Try parent element
            var $parent = $sel.closest('.vehica-field, [data-v-], .v-select');
            if ($parent.length && $parent[0].__vue__) {
                var pVue = $parent[0].__vue__;
                if (pVue.$data) {
                    if (pVue.$data.value !== undefined) pVue.$data.value = targetVal;
                    if (pVue.$data.selected !== undefined) pVue.$data.selected = targetVal;
                    pVue.$forceUpdate && pVue.$forceUpdate();
                }
            }
        } catch(e) {}
    }

    function entityTypeForId(id) {
        for (var i = 0; i < partners.length; i++) if (String(partners[i].id) === String(id)) return 'partner';
        for (var i = 0; i < particulares.length; i++) if (String(particulares[i].id) === String(id)) return 'particular';
        for (var i = 0; i < propios.length; i++) if (String(propios[i].id) === String(id)) return 'propio';
        return '';
    }

    function getEntities() {
        var o = getOrigen();
        console.log('[WeCar] getOrigen:', o);
        if (o === 'partner')    return { items: partners,     label: 'Partner' };
        if (o === 'particular') return { items: particulares, label: 'Particular' };
        if (o === 'propio')     return { items: propios,      label: 'Propio' };
        var all = [];
        for (var i = 0; i < partners.length; i++)     all.push({ id: partners[i].id,     title: '\uD83C\uDFE2 ' + partners[i].title,     type: 'partner' });
        for (var i = 0; i < particulares.length; i++) all.push({ id: particulares[i].id, title: '\uD83D\uDC64 ' + particulares[i].title, type: 'particular' });
        for (var i = 0; i < propios.length; i++)      all.push({ id: propios[i].id,      title: '\uD83C\uDFE0 ' + propios[i].title,      type: 'propio' });
        return { items: all, label: 'Propietario' };
    }

    // ─── Build UI ──────────────────────────────────────────────────
    function buildPanel(items, cur) {
        var h = '<div class="wec-panel" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:9999;">';
        h += '<div style="border:1px solid #ccc;border-radius:4px;background:#fff;box-shadow:0 4px 12px rgba(0,0,0,0.15);">';
        h += '<div style="padding:8px;border-bottom:1px solid #eee;">';
        h += '<input type="text" class="wec-search" placeholder="Buscar..." style="width:100%;padding:6px 8px;border:1px solid #ddd;border-radius:3px;font-size:13px;box-sizing:border-box;" autocomplete="off">';
        h += '</div>';
        h += '<div class="wec-list" style="max-height:200px;overflow-y:auto;">';
        h += '<div class="wec-opt" data-val="" style="padding:8px 12px;cursor:pointer;font-size:13px;color:#888;border-bottom:1px solid #f5f5f5;">— Sin seleccionar —</div>';
        for (var i = 0; i < items.length; i++) {
            var s = (String(items[i].id) === String(cur)) ? 'background:#e5f0fa;font-weight:600;' : '';
            var typeAttr = items[i].type ? ' data-type="' + items[i].type + '"' : '';
            h += '<div class="wec-opt" data-val="' + items[i].id + '"' + typeAttr + ' style="padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid #f5f5f5;' + s + '">';
            if (items[i].type) h += '<span style="font-size:10px;color:#999;margin-right:4px;">[' + items[i].type + ']</span>';
            h += esc(items[i].title) + '</div>';
        }
        h += '</div></div></div>';
        return h;
    }

    function buildTrigger(label, cur, items) {
        var txt = '\u2014 Seleccionar ' + label.toLowerCase() + ' \u2014';
        for (var i = 0; i < items.length; i++) {
            if (String(items[i].id) === String(cur)) { txt = items[i].title; break; }
        }
        return '<div class="wec-trigger" style="padding:8px 12px;border:1px solid #bbb;border-radius:4px;background:#fff;cursor:pointer;font-size:13px;width:100%;max-width:400px;box-sizing:border-box;position:relative;">' +
            esc(txt) + ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">\u25BC</span></div>';
    }

    // ─── Render & Events ───────────────────────────────────────────
    function render($input) {
        var cur = currentSelection || $input.val() || selected;
        currentSelection = cur;
        var list = getEntities();

        // Cambiar label a "Propietario"
        var $field = $input.closest('.vehica-field');
        if ($field.length) {
            var $lbl = $field.find('label').first();
            if ($lbl.length && $lbl.text().indexOf('Propietario') === -1) $lbl.text('Propietario');
        }

        $input.hide();
        $input.siblings('.wec-wrap').remove();

        var html = '<div class="wec-wrap" style="position:relative;">';
        html += buildTrigger(list.label, cur, list.items);
        html += '<select name="' + metaKey + '" style="display:none;">';
        html += '<option value="">\u2014</option>';
        for (var i = 0; i < list.items.length; i++) {
            var s = (String(list.items[i].id) === String(cur)) ? ' selected' : '';
            html += '<option value="' + list.items[i].id + '"' + s + '>' + esc(list.items[i].title) + '</option>';
        }
        html += '</select>';
        html += buildPanel(list.items, cur);
        html += '</div>';

        var $html = $(html);
        var $inner = $input.closest('.vehica-edit__section__inner');
        if ($inner.length) { $inner.append($html); }
        else { $input.after($html); }

        attachEvents($input);
    }

    function attachEvents($input) {
        var $wrap = $input.siblings('.wec-wrap');
        var $trigger = $wrap.find('.wec-trigger');
        var $panel = $wrap.find('.wec-panel');
        var $search = $wrap.find('.wec-search');
        var $select = $wrap.find('select');

        $trigger.off('click.wec').on('click.wec', function (e) {
            e.stopPropagation();
            $('.wec-panel').not($panel).hide();
            $panel.toggle();
            if ($panel.is(':visible')) { $search.val('').focus(); filter($search, $panel); }
        });

        $search.off('input.wec').on('input.wec', function () { filter($(this), $panel); });

        $panel.off('click.wec').on('click.wec', '.wec-opt', function (e) {
            e.stopPropagation();
            var val = $(this).data('val');
            var type = $(this).data('type') || '';
            var displayText = $(this).text().trim().replace(/^\[.*?\]\s*/, '').replace(/^[\uD83C\uDFE2\uD83D\uDC64\uD83C\uDFE0]\s*/, '');

            currentSelection = val;
            $input.val(val);
            $select.val(val);

            var detected = type || entityTypeForId(val);
            var origenLabel = '';
            if (detected) {
                setOrigen(detected);
                if (detected === 'partner') origenLabel = 'Partner';
                else if (detected === 'particular') origenLabel = 'Particular';
                else if (detected === 'propio') origenLabel = 'Propio';
            }

            var triggerHtml = esc(displayText);
            if (origenLabel) {
                triggerHtml += ' <span style="font-size:10px;color:#666;margin-left:4px;">(' + origenLabel + ')</span>';
            }
            triggerHtml += ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">\u25BC</span>';
            $trigger.html(triggerHtml);
            $panel.hide();
        });

        $(document).off('click.wecClose').on('click.wecClose', function () { $panel.hide(); });
    }

    function filter($search, $panel) {
        var q = $search.val().toLowerCase();
        $panel.find('.wec-opt').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
        });
    }

    function rebuild() {
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) return;
        var list = getEntities();
        var cur = currentSelection || $input.val() || selected;
        currentSelection = cur;

        var $wrap = $input.siblings('.wec-wrap');
        if (!$wrap.length) { render($input); return; }

        $wrap.find('.wec-panel').replaceWith(buildPanel(list.items, cur));

        var txt = '\u2014 Seleccionar ' + list.label.toLowerCase() + ' \u2014';
        for (var i = 0; i < list.items.length; i++) {
            if (String(list.items[i].id) === String(cur)) { txt = list.items[i].title; break; }
        }
        $wrap.find('.wec-trigger').html(esc(txt) + ' <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#999;font-size:10px;">\u25BC</span>');

        var opts = '<option value="">\u2014</option>';
        for (var i = 0; i < list.items.length; i++) {
            var s = (String(list.items[i].id) === String(cur)) ? ' selected' : '';
            opts += '<option value="' + list.items[i].id + '"' + s + '>' + esc(list.items[i].title) + '</option>';
        }
        $wrap.find('select').html(opts);

        attachEvents($input);
    }

    function init() {
        console.log('[WeCar] init, data:', { partners: partners.length, particulares: particulares.length, propios: propios.length });
        var $input = $('input[name="' + metaKey + '"]');
        if (!$input.length) {
            var obs = new MutationObserver(function () {
                $input = $('input[name="' + metaKey + '"]');
                if ($input.length) { obs.disconnect(); render($input); watch(); }
            });
            obs.observe(document.body, { childList: true, subtree: true });
            setTimeout(function () { obs.disconnect(); }, 15000);
            return;
        }
        render($input);
        watch();
    }

    function watch() {
        setInterval(function () {
            var o = getOrigen();
            if (o !== lastOrigen) {
                console.log('[WeCar] Origen changed:', lastOrigen, '->', o);
                lastOrigen = o;
                rebuild();
            }
        }, 2000);

        $(document).on('change.wec', 'select', function () {
            var v = String($(this).val()).toLowerCase().trim();
            if (ORIGINS.indexOf(v) !== -1 && v !== lastOrigen) {
                lastOrigen = v;
                rebuild();
            }
        });
    }

    $(init);
})(jQuery);
