function oimToggleClass(element, condition) {
    condition ? element.classList.remove('oim-hidden') : element.classList.add('oim-hidden');
}

document.addEventListener('DOMContentLoaded', () => {
    const choice = new Choices('select', {

    });

    /////
    const root = document.querySelector('.oim-root');
    const header = document.querySelector('.oim-header');
    const footer = document.querySelector('.oim-footer');
    const toggle = document.querySelector('.oim-toggle');
    const container = document.querySelector('.oim-container');

    oimToggleClass(container, root.classList.contains(
        'oim-active'
    ));

    oimToggleClass(header, root.classList.contains(
        'oim-impersonated'
    ));

    oimToggleClass(footer, root.classList.contains(
        'oim-impersonated'
    ));

    if (root.classList.contains('oim-impersonated')) {
        root.style.borderTopRightRadius = '8px';
        toggle.classList.add('oim-toggle-active')
    }

    if (root.classList.contains('oim-active')) {
        toggle.classList.remove('oim-rounded');
    }

    //////////
    toggle.addEventListener('click', function () {
        root.classList.toggle('oim-active')

        oimToggleClass(container, root.classList.contains(
            'oim-active'
        ));

        root.classList.contains('oim-active') ? toggle.classList.remove('oim-rounded') : toggle.classList.add('oim-rounded');

        if (root.classList.contains('oim-impersonated')) {
            toggle.classList.add('oim-toggle-active')
        }
    });
});
