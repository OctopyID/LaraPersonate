! ((current, url) => {
    'use strict';

    const App = {};

    App.GetUser = (tail) => {
        fetch(url).then(resp => resp.json()).then(data => {
            let items = [];
            Object.keys(data).forEach(role => {
                try {
                    data[role].forEach(user => {
                        items.push({
                            key: user.id,
                            value: user.name,
                            group: role.toUpperCase(),
                            selected: current === user.id
                        });
                    });
                } catch (error) {
                    items.push({
                        key: data[role].id,
                        value: data[role].name,
                        selected: current === data[role].id
                    });
                }
            });

            tail.config('items', items);
        });
    };

    App.LaraPersonateInit = () => {
        String.prototype.ucwords = function () {
            return this.toLowerCase().replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, $1 => $1.toUpperCase());
        };

        const button = document.getElementsByClassName('_impersonate-toggle');
        const element = document.getElementsByClassName('_impersonate-interface');
        button[0].addEventListener('click', () => element[0].classList.toggle('_impersonate-hidden'));
        document.addEventListener('DOMContentLoaded', () => {
            App.GetUser(
                tail.select(document.getElementsByClassName('_impersonate-select'), {
                    search: true,
                    width: '100%'
                })
            );
        });
    };

    App.LaraPersonateInit();

})(impersonate_current_user_id, impersonate_user_list_url);
