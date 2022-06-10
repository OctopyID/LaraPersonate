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
    'model'   => App\Models\User::class,

    'display' => [
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
            'name', 'email',
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
        'delay'      => 250,

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
        'limit'      => env('IMPERSONATE_MAX_DISPLAY', 10),
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
];
