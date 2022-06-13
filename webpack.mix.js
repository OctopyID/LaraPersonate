const mix = require('laravel-mix');

mix.options({
    terser: {
        terserOptions: {
            compress: {
                drop_console: true,
            },
        },
    },
})
    .options({
        processCssUrls: false
    })
    .setPublicPath('public')
    .js('resources/js/octopy.js', 'public')
    .sass('resources/sass/octopy.scss', 'public', [
        //
    ])
    .version();
