const mix = require('laravel-mix');

mix.sass('resources/assets/impersonate.scss', 'resources/assets/dist/impersonate.css');

mix.scripts([
    'node_modules/tail.select/js/tail.select.min.js',
    'resources/assets/impersonate.js'
], 'resources/assets/dist/impersonate.js');
