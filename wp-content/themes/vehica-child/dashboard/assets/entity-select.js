/**
 * WeCar — Entity Select Dropdown with Search
 *
 * Reemplaza el campo "Partner" de Vehica por un <select> con buscador
 * que cambia según el Origen seleccionado (PARTNER, PARTICULAR, PROPIO).
 * Mantiene el input original oculto para no romper Vue.
 */
(function ($) {
    'use strict';

    var metaKey      = wecarEntityData.metaKey;
    var selected     = wecarEntityData.selected;
    var origenTax    = wecarEntityData.origenTax;
    var partners     = wecarEntityData.partners  || [];
    var particulares = wecarEntityData.particulares || [];
    var propios      = wecarEntityData.propios   || [];

    /**
     * Obtener el valor actual del campo Origen usando múltiples estrategias
     */
    function getOrigenValue() {
        // Estrategia 1: select con name del taxonomy slug
        var $el = $('select[name="' + origenTax + '"]');
        if ($el.length > 0 && $el.val()) return $el.val();

        // Estrategia 2: select con name="tax_input[{tax}]"
        $el = $('select[name="tax_input[' + origenTax + ']"]');
        if ($el.length > 0 && $el.val()) return $el.val();

        // Estrategia 3: radio buttons
        var $radio = $('input[name="' + origenTax + '"]:checked');
        if ($radio.length > 0) return $radio.val();

        // Estrategia 4: cualquier campo dentro de .vehica-field con data-id que contenga 41298
        $el = $('.vehica-field[data-id="41298"] select, .vehica-field[data-id="41298"] input[type="radio"]:checked');
        if ($el.length > 0 && $el.val()) return $el.val();

        // Estrategia 5: buscar por label que contenga "Origen"
        var $field = $('.vehica-field:has(label:contains("Origen"))');
        $el = $field.find('select');
        if ($el.length > 0 && $el.val()) return $el.val();
        $el = $field.find('input[type="radio"]:checked');
        if ($el.length > 0) return $el.val();

        return '';
    }

    function getOrigenLabel(origen) {
        switch (origen) {
            case 'partner':    return 'partner';
            case 'particular': return 'particular';
            case 'propio':     return 'concesionaria propia';
            default:           return 'entidad';
        }
    }

    function getEntitiesForOrigen(origen) {
        switch (origen) {
            case 'partner':    return { items: partners,     label: 'Seleccionar partner' };
            case 'particular': return { items: particulares, label: 'Seleccionar particular' };
            case 'propio':     return { items: propios,      label: 'Seleccionar concesionaria propia' };
            default:           return { items: [],           label: '— Seleccionar entidad —' };
        }
    }

    function escHtml(str) {
        return $('<span>').text(str).html();
    }

    /**
     * Construye un select buscable: input + datalist
     */
    function buildSearchableSelect(currentVal, entities, placeholder) {
        var uid = 'wecar-entity-' + Math.random().toString(36).substr(2, 9);
        var listId = uid + '-list';

        var html = '<div class="wecar-entity-select-wrap">';
        html += '<input type="text" id="' + uid + '" class="wecar-entity-search" list="' + listId + '"';
        html += ' placeholder="' + escHtml(placeholder) + '"';
        html += ' style="width:100%;max-width:400px;padding:6px 10px;border:1px solid #ddd;border-radius:4px;font-size:13px;box-sizing:border-box;"';
        html += ' autocomplete="off"';

        // Si hay un valor actual, mostrar el nombre
        if (currentVal) {
            for (var i = 0; i < entities.length; i++) {
                if (String(entities[i].id) === String(currentVal)) {
                    html += ' value="' + escHtml(entities[i].title) + '"';
                    break;
                }
            }
        }

        html += '>';
        html += '<datalist id="' + listId + '">';
        for (var i = 0; i < entities.length; i++) {
            html += '<option value="' + escHtml(entities[i].title) + '" data-id="' + entities[i].id + '">';
        }
        html += '</datalist>';

        // Select oculto para mantener compatibilidad con el formulario
        html += '<select name="' + metaKey + '" class="wecar-entity-select-hidden" style="display:none;">';
        html += '<option value="">' + escHtml(placeholder) + '</option>';
        for (var i = 0; i < entities.length; i++) {
            var sel = (String(entities[i].id) === String(currentVal)) ? ' selected' : '';
            html += '<option value="' + entities[i].id + '"' + sel + '>' + escHtml(entities[i].title) + '</option>';
        }
        html += '</select>';

        html += '</div>';
        return html;
    }

    function replaceField() {
        var $input = $('input[name="' + metaKey + '"]');
        if ($input.length === 0) return false;

        var $wrapper = $input.closest('.vehica-field');
        if ($wrapper.length === 0) $wrapper = $input.closest('div');

        // Ya reemplazamos? No hacerlo de nuevo
        if ($wrapper.find('.wecar-entity-select-wrap').length > 0) return true;

        var origen     = getOrigenValue() || 'partner';
        var entityData = getEntitiesForOrigen(origen);
        var currentVal = $input.val() || selected;

        $input.hide();

        var $sectionInner = $input.closest('.vehica-edit__section__inner');
        var $selectHtml = $(buildSearchableSelect(currentVal, entityData.items, entityData.label));

        if ($sectionInner.length > 0) {
            $sectionInner.append($selectHtml);
        } else {
            $input.after($selectHtml);
        }

        // Sincronizar: cuando se selecciona del datalist, actualizar el select oculto y el input
        bindSearchEvents($wrapper, $input);

        return true;
    }

    function bindSearchEvents($wrapper, $input) {
        var $search = $wrapper.find('.wecar-entity-search');
        var $hidden = $wrapper.find('.wecar-entity-select-hidden');

        $search.on('input', function () {
            var val = $(this).val();
            var $opt = $hidden.find('option').filter(function () {
                return $(this).text() === val;
            });
            if ($opt.length > 0) {
                $hidden.val($opt.val());
                $input.val($opt.val());
            } else {
                $hidden.val('');
                $input.val('');
            }
        });

        // Si el usuario borra el campo, limpiar también
        $search.on('change', function () {
            var val = $(this).val();
            var $opt = $hidden.find('option').filter(function () {
                return $(this).text() === val;
            });
            if ($opt.length === 0) {
                $hidden.val('');
                $input.val('');
            }
        });
    }

    function updateSelect() {
        var $input = $('input[name="' + metaKey + '"]');
        if ($input.length === 0) return;

        var $wrap     = $('.wecar-entity-select-wrap');
        var origen    = getOrigenValue();
        var entityData = getEntitiesForOrigen(origen);
        var currentVal = $input.val() || selected;

        if ($wrap.length === 0) {
            replaceField();
            return;
        }

        // Reemplazar el contenido del wrap
        var $newHtml = $(buildSearchableSelect(currentVal, entityData.items, entityData.label));
        $wrap.replaceWith($newHtml);

        // Re-bindear eventos
        var $newWrap = $('.wecar-entity-select-wrap');
        var $newInput = $('input[name="' + metaKey + '"]');
        bindSearchEvents($newWrap, $newInput);
    }

    function init() {
        if (replaceField()) {
            // Escuchar cambios en el Origen
            listenOrigenChanges();
            return;
        }

        // Esperar a que Vue renderice los campos
        var observer = new MutationObserver(function () {
            if (replaceField()) {
                observer.disconnect();
                listenOrigenChanges();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });

        setTimeout(function () {
            observer.disconnect();
        }, 15000);
    }

    function listenOrigenChanges() {
        // Escuchar en TODOS los posibles selectores del campo Origen
        var selectors = [
            'select[name="' + origenTax + '"]',
            'select[name="tax_input[' + origenTax + ']"]',
            '.vehica-field[data-id="41298"] select',
            '.vehica-field:has(label:contains("Origen")) select',
        ];

        for (var i = 0; i < selectors.length; i++) {
            $(document).on('change', selectors[i], function () {
                updateSelect();
            });
        }

        // También escuchar radio buttons
        $(document).on('change', 'input[name="' + origenTax + '"]', function () {
            updateSelect();
        });
    }

    $(document).ready(init);
})(jQuery);
