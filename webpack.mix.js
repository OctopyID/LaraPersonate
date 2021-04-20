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
    .js('resources/js/app.js', 'public')
    .sass('resources/sass/app.scss', 'public', [
        //
    ])
    .version();
