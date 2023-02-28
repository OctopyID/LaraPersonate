<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Impersonation Interface
    |--------------------------------------------------------------------------
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled' => env('IMPERSONATE_ENABLED', true),

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
    'trashed' => true,

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
        | Field Name
        |--------------------------------------------------------------------------
        |
        | Name column in the table containing the data to be displayed.
        | for example such as `name`, `user_name`, `full_name`, `email` or etc.
        |
        | You can also use fields from other tables through relations,
        | for example: `department.name`.
        |
        | If the relation hasMany or similar, use an index
        | for example: `roles.0.name`
        |
        | You can use multiple fields, which will be merged when displayed
        | on the interface, use a separator to separate each one.
        |
        */
        'fields' => [
            'name',
        ],

        'separator'  => ' - ',

        /*
        |--------------------------------------------------------------------------
        | Searchable Keys
        |--------------------------------------------------------------------------
        |
        | The following is useful for performing user searches through the interface,
        | You can use fields in relations freely using dot notation,
        | for example: `roles.name`, `department.name`.
        |
        */
        'searchable' => [
            'name', 'email',
        ],

        /*
        |--------------------------------------------------------------------------
        | Interface Width
        |--------------------------------------------------------------------------
        |
        | You are free to determine the required interface width.
        | This is very useful for avoiding hard wraps on the interface.
        |
        */
        'width'      => env('IMPERSONATE_WIDTH', '350px'),

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
        'delay'      => env('IMPERSONATE_SEARCH_DELAY', 500),

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
        'limit'      => env('IMPERSONATE_MAX_DISPLAY', 20),
    ],
];
