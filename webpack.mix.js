const min = require('minifier');
const mix = require('laravel-mix');

mix.sass('resources/assets/sass/app.scss', 'resources/assets/dist/sudo.css');

mix.then(() => {
    min.minify('resources/assets/dist/sudo.css');
});

mix.copy('node_modules/tail.select/js/tail.select.min.js', 'resources/assets/dist/tail.min.js');
mix.copy('node_modules/tail.select/css/modern/tail.select-light.min.css', 'resources/assets/dist/tail.min.css');
