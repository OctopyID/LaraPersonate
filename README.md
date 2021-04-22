<p align="center">
    <img src="demo.gif" alt="Demo">
</p>

<p align="center">
    <img src="https://img.shields.io/packagist/l/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="License">
    <img src="https://img.shields.io/packagist/v/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="Version">
    <img src="https://img.shields.io/packagist/dt/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="Downloads">
</p>

# Lara Personate

Is a user impersonate for the Laravel framework. This package makes it easier for users who have access rights such as super admin to take over other user accounts.

## Installation

To install the package, simply follow the steps below.

Install the package using Composer:

```
$ composer require octopyid/laravel-impersonate

$ php artisan vendor:publish
```

Add the trait `Octopy\LaraPersonate\Models\Impersonate` to your **User** model.

```php
<?php

namespace App\Models;

use Octopy\LaraPersonate\Models\Impersonate;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Impersonate;
}
```

## Usage

### Defining Authorization

By default all users can **impersonate** an user. You need to add the method `canImpersonate()` to your user model:

```php
    /**
     * @return bool
     */
    public function canImpersonate() : bool
    {
        // example
        return $this->is_admin === 1;
    }
```

By default all users can **be impersonated**. You need to add the method `canBeImpersonated()` to your user model to extend this behavior:

```php
    /**
     * @return bool
     */
    public function canBeImpersonated() : bool
    {
        // example
        return $this->can_be_impersonated === 1;
    }
```

## Disclaimer - DANGER !

This package can pose a serious security issue if used incorrectly, as anybody will be able to take control of any user's account.

By using this package, you agree that Octopy ID and the contributors of this package cannot be held responsible for any damages caused by using this package.

## Security

If you discover any security related issues, please email [supianidz@gmail.com](mailto:supianidz@gmail.com) or [me@octopy.id](mailto:me@octopy.id) instead of using the issue
tracker.

## Credits

- [Supian M](https://github.com/SupianIDz)
- [Octopy ID](https://github.com/OctopyID)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
