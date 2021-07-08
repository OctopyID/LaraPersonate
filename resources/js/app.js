// noinspection JSUnresolvedVariable

import $ from 'jquery';
import 'select2';

$.noConflict();

$(document).ready(() => {
    $('head link[rel="stylesheet"]').first().before(
        '<link rel="stylesheet" type="text/css"  href="/vendor/octopyid/impersonate/app.css">'
    );

    const toggle = $('.impersonate-toggle');
    const select = $('.impersonate-select');
    const logout = $('.impersonate-logout');

    toggle.on('click', () => {
        $('.impersonate-interface').toggleClass('impersonate-hidden');

        select.select2({
            dropdownParent: $('.impersonate'),
            ajax: {
                url: '/impersonate/list',
                dataType: 'json',
                delay: window.impersonate.rate,
                data: (params) => {
                    return {
                        search: params.term,
                    };
                },
                processResults: data => ({
                    results: $.map(data, ({ id, text }) => ({
                        id, text
                    }))
                })
            },
        });
    });

    select.select2().on('select2:open', () => {
        $('.select2-search__field').attr('placeholder', ' Search...');
    });

    const xhr = (url, body) => {
        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body
        })
            .then(() => {
                window.location = '';
            });
    }

    select.on('change', function () {
        xhr('/impersonate/signin', JSON.stringify({
            user: window.impersonate.user,
            take: $(this).val(),
            _token: window.impersonate.csrf,
        }));
    });

    logout.on('click', () => {
        xhr('/impersonate/logout', JSON.stringify({
            _token: window.impersonate.csrf,
        }));
    });
});
