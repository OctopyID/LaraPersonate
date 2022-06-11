import $ from 'jquery';
import 'select2';

$.noConflict();

$(document).ready(function () {
    const content = $('.impersonate-content');
    const wrapper = $('.impersonate-wrapper');

    const toggle = $('.impersonate-toggle');
    const select = $('.impersonate-select');

    select.select2({
        dropdownParent: content,
        containerCssClass: 'impersonate-select-container',
    });

    select.select2().on('select2:open', () => {
        $('.select2-search__field').attr('placeholder', ' Search...');
    });

    toggle.click(function () {
        wrapper.toggleClass('impersonate-border');
        toggle.toggleClass('impersonate-toggle-active');

        content.toggle('fast', function () {
            content.toggleClass('impersonate-content-active');
        });
    });
});
