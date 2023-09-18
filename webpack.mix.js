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

    .sass('resources/asset/impersonate.scss', 'public', [
        //
    ])

    .scripts([
        'resources/asset/impersonate.js',
        'node_modules/choices.js/public/assets/scripts/choices.js'
        // 'node_modules/tail.select.js/js/tail.select.js'
    ], 'public/impersonate.js')

    .version();
