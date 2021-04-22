<?php

use Octopy\LaraPersonate\Impersonate;

return [
    /*
    |--------------------------------------------------------------------------
    | Impersonate
    |--------------------------------------------------------------------------
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled'  => env('IMPERSONATE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Impersonate
    |--------------------------------------------------------------------------
    | You can provide an array of URI's that must be ignored (eg. 'api/*')
    |
    */
    'except'   => [
        'telescope*', 'horizon*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Toggle Position
    |--------------------------------------------------------------------------
    | This section provides options for determining the position of the Impersonate
    | toggle on the screen.
    |
    | - Impersonate::POSITION_LEFT
    | - Impersonate::POSITION_RIGHT
    |
    */
    'position' => Impersonate::POSITION_LEFT,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Path to the application User model. This will be used to retrieve the users
    | displayed in the select dropdown. This must be an Eloquent Model instance.
    |
    */
    'model'    => App\Models\User::class,

    'field' => [
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
    'delay' => 250,

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
    'limit' => env('IMPERSONATE_MAX_DISPLAY', 5),
];
