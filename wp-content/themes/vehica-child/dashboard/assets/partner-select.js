/**
 * WeCar — Partner Select Dropdown
 *
 * Reemplaza el campo texto "Partner" de Vehica por un <select>
 * Mantiene el input original oculto para no romper Vue.
 */
(function ($) {
    'use strict';

    var metaKey = wecarPartnerData.metaKey;
    var selected = wecarPartnerData.selected;
    var partners = wecarPartnerData.partners;

    function escHtml(str) {
        return $('<span>').text(str).html();
    }

    function buildSelect(currentVal) {
        var html = '<select class="wecar-partner-select" name="' + metaKey + '" style="width:100%;max-width:400px;">';
        html += '<option value="">— Sin partner —</option>';

        for (var i = 0; i < partners.length; i++) {
            var p = partners[i];
            var sel = (String(p.id) === String(currentVal)) ? ' selected' : '';
            html += '<option value="' + p.id + '"' + sel + '>' + escHtml(p.title) + '</option>';
        }

        html += '</select>';
        return html;
    }

    function replaceField() {
        var $input = $('input[name="' + metaKey + '"]');
        if ($input.length === 0) return false;

        var $wrapper = $input.closest('.vehica-field');
        if ($wrapper.length === 0) $wrapper = $input.closest('div');

        if ($wrapper.find('.wecar-partner-select').length > 0) return true;

        var currentVal = $input.val() || selected;

        $input.hide();

        var $sectionInner = $input.closest('.vehica-edit__section__inner');
        if ($sectionInner.length > 0) {
            $sectionInner.append(buildSelect(currentVal));
        } else {
            $input.after(buildSelect(currentVal));
        }

        $wrapper.on('change', '.wecar-partner-select', function () {
            $input.val($(this).val());
        });

        return true;
    }

    function init() {
        if (replaceField()) return;

        var observer = new MutationObserver(function () {
            if (replaceField()) {
                observer.disconnect();
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