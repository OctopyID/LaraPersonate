<?php

use Octopy\LaraPersonate\Impersonate;

return [
    /*
    |--------------------------------------------------------------------------
    | LaraPersonate
    |--------------------------------------------------------------------------
    | LaraPersonate is enabled by default, when debug is set to true in app.php.
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled' => env('IMPERSONATE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Path to the application User model. This will be used to retrieve the users
    | displayed in the select dropdown. This must be an Eloquent Model instance.
    |
    */
    'model'   => App\Models\User::class,

    'fields' => [
        /*
        |--------------------------------------------------------------------------
        | Field Name
        |--------------------------------------------------------------------------
        |
        | Data fields for user names from table to display in the list,
        | for example such as `name`, `user_name`, `full_name`, etc.
        |
        */
        Impersonate::FIELD_NAME => 'name',
    ],
];
