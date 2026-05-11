/**
 * WeCar — Entity Select Dropdown
 *
 * Reemplaza el campo "Partner" de Vehica por un <select> dinámico
 * que cambia según el Origen seleccionado (PARTNER, PARTICULAR, PROPIO).
 * Mantiene el input original oculto para no romper Vue.
 */
(function ($) {
    'use strict';

    var metaKey      = wecarEntityData.metaKey;
    var selected     = wecarEntityData.selected;
    var origenTax    = wecarEntityData.origenTax;
    var partners     = wecarEntityData.partners;
    var particulares = wecarEntityData.particulares;
    var propios      = wecarEntityData.propios;

    function escHtml(str) {
        return $('<span>').text(str).html();
    }

    function getOrigenValue() {
        var $select = $('select[name="' + origenTax + '"]');
        if ($select.length > 0) {
            return $select.val();
        }
        var $radio = $('input[name="' + origenTax + '"]:checked');
        if ($radio.length > 0) {
            return $radio.val();
        }
        return '';
    }

    function getEntitiesForOrigen(origen) {
        switch (origen) {
            case 'partner':
                return { items: partners, label: 'Seleccionar partner' };
            case 'particular':
                return { items: particulares, label: 'Seleccionar particular' };
            case 'propio':
                return { items: propios, label: 'Seleccionar concesionaria propia' };
            default:
                return { items: [], label: '— Seleccionar entidad —' };
        }
    }

    function buildSelect(currentVal, entities, label) {
        var html = '<select class="wecar-entity-select" name="' + metaKey + '" style="width:100%;max-width:400px;">';
        html += '<option value="">' + escHtml(label) + '</option>';

        for (var i = 0; i < entities.length; i++) {
            var e = entities[i];
            var sel = (String(e.id) === String(currentVal)) ? ' selected' : '';
            html += '<option value="' + e.id + '"' + sel + '>' + escHtml(e.title) + '</option>';
        }

        html += '</select>';
        return html;
    }

    function replaceField() {
        var $input = $('input[name="' + metaKey + '"]');
        if ($input.length === 0) return false;

        var $wrapper = $input.closest('.vehica-field');
        if ($wrapper.length === 0) $wrapper = $input.closest('div');

        if ($wrapper.find('.wecar-entity-select').length > 0) {
            return true;
        }

        var origen     = getOrigenValue();
        var entityData = getEntitiesForOrigen(origen);
        var currentVal = $input.val() || selected;

        $input.hide();

        var $sectionInner = $input.closest('.vehica-edit__section__inner');
        if ($sectionInner.length > 0) {
            $sectionInner.append(buildSelect(currentVal, entityData.items, entityData.label));
        } else {
            $input.after(buildSelect(currentVal, entityData.items, entityData.label));
        }

        $wrapper.on('change', '.wecar-entity-select', function () {
            $input.val($(this).val());
        });

        return true;
    }

    function updateSelect() {
        var $input = $('input[name="' + metaKey + '"]');
        if ($input.length === 0) return;

        var $select    = $('.wecar-entity-select');
        var origen     = getOrigenValue();
        var entityData = getEntitiesForOrigen(origen);
        var currentVal = $input.val() || selected;

        if ($select.length === 0) {
            replaceField();
            return;
        }

        var $newSelect = $(buildSelect(currentVal, entityData.items, entityData.label));
        $select.replaceWith($newSelect);

        $newSelect.on('change', function () {
            $input.val($(this).val());
        });
    }

    function init() {
        if (replaceField()) {
            $(document).on('change', 'select[name="' + origenTax + '"]', updateSelect);
            $(document).on('change', 'input[name="' + origenTax + '"]', updateSelect);
            return;
        }

        var observer = new MutationObserver(function () {
            if (replaceField()) {
                observer.disconnect();
                $(document).on('change', 'select[name="' + origenTax + '"]', updateSelect);
                $(document).on('change', 'input[name="' + origenTax + '"]', updateSelect);
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

    $(document).ready(init);
})(jQuery);
