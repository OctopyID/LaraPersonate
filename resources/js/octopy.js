// noinspection JSUnresolvedVariable

import $ from 'jquery';
import 'select2';

$.noConflict();


/**
 * @var impersonate = {
 *     active: false,
 *     config: {
 *         token: 'foo',
 *         route: 'http://localhost:8000/subdir
 *         delay: '500',
 *         width: '100%',
 *     }
 * }
 */
const xhr = (url, body) => {
    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': impersonate.config.token
        },
        body: body
    })
        .then(() => {
            window.location.reload();
        })
        .catch(error => {
            console.error(error);
        });
}

$(document).ready(function () {
    const toggle = $('.impersonate-toggle');
    const select = $('.impersonate-select');

    const content = $('.impersonate-content');
    const wrapper = $('.impersonate-wrapper');

    const signout = $('.impersonate-footer-logout');

    // define interface width by config
    content.css('width', impersonate.config.width)

    select.select2().on('select2:open', () => {
        $('.select2-container--impersonate .select2-search__field').attr('placeholder', ' Search...');
    });

    if (impersonate.active) {
        toggle.addClass('impersonate-toggle-active')
    }

    if (select.hasClass('select2-hidden-accessible')) {
        wrapper.find('.select2-container').remove();
    }

    select.select2({
        theme: 'impersonate',
        ajax: {
            method: 'GET',
            dataType: 'JSON',
            url: impersonate.config.route + '/impersonate/users',
            delay: impersonate.config.delay,
            data: (params) => {
                return {
                    search: params.term,
                };
            },
            processResults: data => ({
                // parse the results into the format expected by Select2.
                results: $.map(data, ({ key, val }) => ({
                    id: key, text: val
                }))
            })
        },
    });

    select.select2('open');
    select.select2('close');

    toggle.click(function () {

        wrapper.toggleClass('impersonate-border');

        toggle
            .find('img').toggleClass('impersonate-animate');

        if (impersonate.active) {
            toggle.addClass('impersonate-toggle-active')
        } else {
            toggle.toggleClass('impersonate-toggle-active')
        }

        content.toggle('fast', function () {
            content.toggleClass('impersonate-content-toggled');
        });

        if (impersonate.active) {
            wrapper.addClass('impersonate-wrapper-active');
            content.addClass('impersonate-content-active');
        } else {
            wrapper.addClass('impersonate-wrapper-non-active');
            content.addClass('impersonate-content-non-active');
        }
    });

    select.on('change', function () {
        xhr(impersonate.config.route + '/impersonate/login', JSON.stringify({
            user: this.value
        }));
    });

    signout.on('click', function () {
        xhr(impersonate.config.route + '/impersonate/leave', {
            //
        });
    });
});
