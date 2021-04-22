import $ from 'jquery';
import 'select2';

$.noConflict();

$(document).ready(() => {
    const toggle = $('.impersonate-toggle');
    const select = $('.impersonate-select');
    const logout = $('.impersonate-logout');

    toggle.on('click', function () {
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
    })

    select.on('change', function () {
        fetch('/impersonate/signin', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                user: window.impersonate.user,
                take: $(this).val(),
                _token: window.impersonate.csrf,
            })
        })
            .then(() => {
                window.location = '';
            });
    });

    logout.on('click', function () {
        fetch('/impersonate/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                _token: window.impersonate.csrf,
            })
        }).then(() => {
            window.location = '';
        });
    });
});
