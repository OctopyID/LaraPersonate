<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Impersonate
    |--------------------------------------------------------------------------
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled' => env('IMPERSONATE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Impersonate
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
    | Path to the application User model. This will be used to retrieve the users
    | displayed in the select dropdown. This must be an Eloquent Model instance.
    |
    */
    'model'   => config('auth.providers.users.model', App\Models\User::class),

    'field'   => [
        /*
        |--------------------------------------------------------------------------
        | Field Name
        |--------------------------------------------------------------------------
        |
        | Name column in the table containing the data to be displayed.
        | for example such as `name`, `user_name`, `full_name`, `email` or etc.
        |
        */
        'display'     => 'name',

        /*
        |--------------------------------------------------------------------------
        | Search Keys
        |--------------------------------------------------------------------------
        |
        | The name of the column used as the search keys.
        |
        */
        'search_keys' => [
            'name', 'email',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | Storage is used to store impersonation data which will be reused
    | when exiting impersonation mode.
    |
    | Currently, only the `session` driver is available.
    |
    */
    'storage' => 'session',

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
    'delay'   => 250,

    /*
    |--------------------------------------------------------------------------
    | Maximum User Shown
    |--------------------------------------------------------------------------
    |
    | The maximum number of users displayed.
    |
    | Be careful, this might make your application crash if there is a lot of user data.
    |
    */
    'limit'   => env('IMPERSONATE_MAX_DISPLAY', 5),
];
