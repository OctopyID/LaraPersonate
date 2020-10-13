<?php

return [

    /*
    |--------------------------------------------------------------------------
    | LaraPersonate
    |--------------------------------------------------------------------------
    | LaraPersonate is enabled by default, when debug is set to true in app.php.
    | You can override the value by setting enable to true or false instead of null.
    |
    */
    'enabled'     => env('IMPERSONATE', true),

    /*
    |--------------------------------------------------------------------------
    | Allowed TLD
    |--------------------------------------------------------------------------
    |
    | This is to prevent mis-usage during production if debug mode is
    | unintentionally left active. The package will detect the site
    | URL and if the TLD isn't present in this array, it will not
    | activate. If your development TLD is different to .dev or
    | .local, simply add it to the array below.
    |
    | Empty the array if you don't want any restrictions on the tlds.
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
    'user_model'  => App\Models\User::class,

    'fields'     => [

        /*
        |--------------------------------------------------------------------------
        | Field Primary ID
        |--------------------------------------------------------------------------
        |
        | Primary field from the user table, for example like `id`, `user_id`, etc.
        |
        */
        'id'   => 'id',

        /*
        |--------------------------------------------------------------------------
        | Field Name
        |--------------------------------------------------------------------------
        |
        | Data fields for user names from table to display in the list,
        | for example such as `name`, `user_name`, `full_name`, etc.
        |
        */
        'name' => 'name',
    ],

    /*
    |--------------------------------------------------------------------------
    | With Roles
    |--------------------------------------------------------------------------
    |
    | If with_roles is enabled, the displayed users will be grouped by role.
    |
    | You just need to set it to true, the group will show automatically
    | if your user_model has used trait from role authorization packages such as Laratrust,
    | otherwise groups will not be displayed even if with_roles is set to true.
    |
    | Currently supported:
    | - laratrust (https://github.com/santigarcor/laratrust)
    |
    | Next Plan:
    | - laravel bouncer (https://github.com/JosephSilber/bouncer)
    | - laravel permission (https://github.com/spatie/laravel-permission)
    |
    */
    'with_roles' => true,

    /*
    |--------------------------------------------------------------------------
    | Maximum User Shown
    |--------------------------------------------------------------------------
    |
    | The maximum number of users displayed
    | when with_roles is set to true, the number of displayed users will be multiplied
    | by the number of displayed roles.
    |
    | Be careful, this might make your application crash if there is a lot of user data.
    |
    */
    'limit'      => 3,
];
