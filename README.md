<p align="center">
    <img src="demo.gif" alt="Demo">
</p>

<p align="center">
    <img src="https://img.shields.io/github/actions/workflow/status/OctopyID/LaraPersonate/tests.yml?branch=main&style=for-the-badge" alt="Tests">
    <img src="https://img.shields.io/packagist/v/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="Version">
    <img src="https://img.shields.io/packagist/dt/octopyid/laravel-impersonate.svg?style=for-the-badge&color=F28D1A" alt="Downloads">
    <img src="https://img.shields.io/packagist/l/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="License">
</p>

<p align="center">
    <a href="https://github.com/Safouene1/support-palestine-banner/Markdown-pages/Support.md"> 
        <img src="https://raw.githubusercontent.com/Safouene1/support-palestine-banner/master/banner-support.svg">
    </a>
</p>

# Laravel Impersonate

Is an Impersonation package for the Laravel Framework. With this package you can easily impersonate other users either manually or using the interface we provide.

You don't have to worry about authorizing who can impersonate or who can be impersonated, coz we provided it by default, just need to adjust it a little according to your
rules.

| Impersonate                                                 | Laravel     | Impersonate                                                 | Laravel   |
|-------------------------------------------------------------|-------------|-------------------------------------------------------------|-----------|
| [v4.x](https://github.com/OctopyID/LaraPersonate/tree/main) | 10.x - 12.x | [v2.x](https://github.com/OctopyID/LaraPersonate/tree/v2.x) | 7.x - 8.x |
| [v3.x](https://github.com/OctopyID/LaraPersonate/tree/v3.x) | 9.x - 10.x  | [v1.x](https://github.com/OctopyID/LaraPersonate/tree/v1.x) | 7.x - 8.x |

## Installation

> **Warning**
>
> This version is a breaking change, many changes were made to the addition of new features, new UI design, and code structure.
>
> If you are upgrade from an old version, please delete the old assets and republish the assets, configure and reset the [limitations](#311-defining-limitation) on the User Model
> according to this version.

To install the package, simply follow the steps below.

### Install The Package

```bash
composer require octopyid/laravel-impersonate:^4
```

### Publish The Package

```bash
php artisan vendor:publish --tag="impersonate"
```

> **Note**
>
> Sometimes some users experience the problem of layout after upgrading the package, this can be solved by deleting the `public/vendor/octopyid/impersonate` folder then republish
> the assets.

### Add `HasImpersonation` Trait to  User Model

Add the trait `Octopy\Impersonate\Concerns\HasImpersonation` to your **User** model.

```php
namespace App\Models;

use Octopy\Impersonate\Concerns\HasImpersonation;
use Illuminate\Foundation\Auth\User as Authenticatable;     

class User extends Authenticatable
{
    use HasImpersonation;
}

```

If you plan to use the provided UI, add `Octopy\Impersonate\Contracts\HasImpersonationUI` interface to add mandatory configuration for the UI.

```php
namespace App\Models;

use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Contracts\HasImpersonationUI;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasImpersonationUI
{
    use HasImpersonation;
    
    /**
     * @return string
     */
    public function getImpersonateDisplayText() : string
    {
        return $this->name;
    }
    
    /**
     * This following is useful for performing user searches through the interface,
     * You can use fields in relations freely using dot notation,
     * 
     * example: posts.title, department.name.   
     */
    public function getImpersonateSearchField() : array
    {
        return [
            'name', 'posts.title',
        ];
    }
}

```

## Events

There are two events available that can be used to improve your workflow:

- `Octopy\Impersonate\Events\BeginImpersonation` is fired when an impersonation is begin.
- `Octopy\Impersonate\Events\LeaveImpersonation` is fired when an impersonation is leave.

## Configuration

This configuration is intended to customize the appearance of Laravel Impersonate, if you don't need a UI, don't forget to set `IMPERSONATE_ENABLED` to `false` in your environment
file because it is enabled by default.

Please refer to the [impersonate.php](config/impersonate.php) file to see the available configurations.

## Usage

### Basic Usage

By default, you don't need to do anything, but keep in mind, Impersonation can be done by anyone if you don't define the rules of who can do impersonation or who can be
impersonated.

#### Defining Limitation

To limit who can do **impersonation** or who is can be **impersonated**, add
`setImpersonateAuthorization(Authorization $authorization)` on the Model to enforce the limitation.

The **impersonator** method is intended for who can perform the impersonation and the **impersonated** method is intended for anyone who is allowed to be imitated.

> **Warning**
>
> Not defining the Authorization rules in the Model or misdefining them can lead to serious security issues.

The example below uses [Laratrust](https://github.com/santigarcor/laratrust/) for role management where **SUPER_ADMIN** can perform impersonation against **CUSTOMER**. Feel
free to use any other Role Management you like.

```php
use Octopy\Impersonate\Concerns\HasImpersonation;
use Octopy\Impersonate\Authorization;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasImpersonation;
    
    /**
     * @param  Authorization $authorization
     * @return void
     */
    public function setImpersonateAuthorization(Authorization $authorization) : void
    {
        $authorization->impersonator(function (User $user) {
            return $user->hasRole('SUPER_ADMIN');
        });

        $authorization->impersonated(function (User $user) {
            return $user->hasRole('CUSTOMER');
        });
    }
}
```

### Advanced Usage

#### Impersonating User Manually

Sometimes you need Impersonating manually, to perform it, you can use the impersonate singleton.

```php
impersonate()->begin($admin, $customer);
```

Or just simply call the impersonation method directly through the User Model.

```php
$admin->impersonate($customer);
```

#### Defining Guard

Sometimes, you want to use custom guards for authentication, instead of the built-in guards.

```php
impersonate()->guard('foo')->begin($admin, $customer);
```

#### Leaving Impersonation Mode

To leave Impersonation mode, you just need to call the `leave` method on impersonate singleton. This will return you to the original user.

```php
impersonate()->leave();
```

Or via Model directly

```php
$admin->impersonate()->leave();
```

Don't hesitate to use a guard if you need it.

## Disclaimer

This package can pose a serious security issue if used incorrectly, as anybody will be able to take control of any user's account.

By using this package, you agree that Octopy ID and the contributors of this package cannot be held responsible for any damages caused by using this package.

## Security

If you discover any security related issues, please email [bug@octopy.dev](mailto:bug@octopy.dev) instead of using the issue
tracker.

## Credits

- [Supian M](https://github.com/SupianIDz)
- [Octopy ID](https://github.com/OctopyID)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
