<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sudo
    |--------------------------------------------------------------------------
    | Sudo is enabled by default, when debug is set to true in app.php.
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled'     => env('APP_DEBUG', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed TLD
    |--------------------------------------------------------------------------
    |
    | This is to prevent mis-usage during production if debug mode is
    | unintentionally left active. The package will detect the site
    | URL and if the TLD isn't present in this array, it will not
    | activate. If your development TLD is different to .dev or
    | .local, simply add it to the arrow below.
    |
    | Fill "*" for wildcard tlds.
    |
    */
    'allowed_tld' => ['dev', 'local'],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Path to the application User model. This will be used to retrieve the users
    | displayed in the select dropdown. This must be an Eloquent Model instance.
    |
    */
    'user_model'  => App\User::class,

    /*
    |--------------------------------------------------------------------------
    | Maximum User Shown
    |--------------------------------------------------------------------------
    |
    | The maximum number of users is displayed
    |
    | Fill in "-1" to display all users
    |
    */
    'max_shown'   => 5,
];
