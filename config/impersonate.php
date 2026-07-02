<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Exclude Impersonate
    |--------------------------------------------------------------------------
    | You can provide an array of URI's that must be ignored (eg. 'api/*')
    |
    */
    'except'  => [
        'telescope*', 'horizon*', 'api/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Path to the application User model. This will be used to retrieve the
    | users displayed in the select dropdown.
    |
    | This must be an Eloquent Model instance.
    |
    */
    'model'   => config('auth.providers.users.model'),

    /*
    |--------------------------------------------------------------------------
    | Show Trashed Users
    |--------------------------------------------------------------------------
    |
    | If you are using the SoftDeletes trait on your User model, you can
    | set this to true to show trashed users in the select dropdown.
    |
    */
    'trashed' => false,

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    */
    'guard'   => config('auth.defaults.guard'),

    'interface' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Interface
        |--------------------------------------------------------------------------
        |
        | You can easily disable the impersonation UI if you only want to use
        | the backend impersonation logic without exposing the interface.
        |
        */
        'enabled' => env('IMPERSONATE_UI_ENABLED', true),

        /*
        |--------------------------------------------------------------------------
        | Interface Width
        |--------------------------------------------------------------------------
        |
        | You are free to determine the required interface width.
        | This is very useful for avoiding hard wraps on the interface.
        |
        */
        'width' => env('IMPERSONATE_UI_WIDTH', '21rem'),

        /*
        |--------------------------------------------------------------------------
        | Rate-limiting Requests
        |--------------------------------------------------------------------------
        |
        | You can tell LaraPersonate to wait until the user has finished typing
        | their search term before triggering the AJAX request.
        |
        | Simply use the delay configuration option to tell how long
        | to wait after a user has stopped typing before sending the request
        |
        */
        'delay' => env('IMPERSONATE_UI_DELAY', 300),
    ],
];
