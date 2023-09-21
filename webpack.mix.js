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
    .js('resources/asset/octopy.js', 'public')
    .sass('resources/asset/octopy.scss', 'public')
    .version();
