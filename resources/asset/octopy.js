// noinspection JSUnresolvedReference

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
const oimXHR = (path, body) => {
    fetch(window.impersonate.config.route + path, {
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
    const root = $('.oim-root');
    const toggle = $('.oim-toggle');
    const select = $('.oim-select');
    const logout = $('.oim-logout');
    const container = $('.oim-container');

    select.select2().on('select2:open', () => {
        $('.select2-container--impersonate .select2-search__field').attr('placeholder', ' Search...');
    });

    container.css('width', window.impersonate.config.width)

    select.select2({
        theme: 'impersonate',
        search: true,
        dropdownParent: root,
        ajax: {
            dataType: 'JSON',
            url: '/_impersonate/users',
            delay: window.impersonate.config.delay,
            data: (params) => ({
                query: params.term, page: params.page || 1
            }),
            processResults: (res, params) => ({
                results: $.map(res.data, ({ key, val }) => ({
                    id: key, text: val
                })),
                pagination: {
                    more: res.links.next !== null
                }
            })
        },
    });

    select.select2('open').select2('close');

    toggle.on('click', function () {
        root.toggleClass('oim-active');

        toggle.find('img').toggleClass('oim-toggle-animate');

        container.toggle();
    });

    select.on('change', function () {
        oimXHR('/_impersonate/login', JSON.stringify({
            user: this.value
        }));
    });

    logout.on('click', function () {
        oimXHR('/_impersonate/leave');
    })
});
