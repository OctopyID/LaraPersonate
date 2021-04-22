<p align="center">
    <img src="https://img.shields.io/packagist/l/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="License">
    <img src="https://img.shields.io/packagist/v/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="Version">
    <img src="https://img.shields.io/packagist/dt/octopyid/laravel-impersonate.svg?style=for-the-badge" alt="Downloads">
</p>

# Lara Personate

Is a user impersonate for the laravel framework. allow a developer direct login as another user during development inspired by [sudo-su](https://github.com/viacreative/sudo-su).

## Features

- Displays users based their role.
- Limit the number of users displayed.
- Search form using [tail.select](https://github.pytes.net/tail.select/).
- Auto discovery, no more setting up your ServiceProvider manually.
- Automatic injection via a middleware, no need to add some code to the blade.

## Installation

To install the package, simply follow the steps below.

Install the package using Composer:

```
$ composer require octopyid/laravel-impersonate:^1 --dev

$ artisan vendor:publish --provider="Octopy\LaraPersonate\LaraPersonateServiceProvider"
```

## Lara Personate 1.2.x Upgrade Guide

Since there are many changes in the config and assets files in version 1.2.0, make sure to republish the package.

```
$ artisan vendor:publish --provider="Octopy\LaraPersonate\LaraPersonateServiceProvider"
```

And that's it!

## Configuration

After running `vendor:publish`, a config file called `impersonate.php` should appear in your project.

## Demo

<p align="center">
    <img src="demo.gif" alt="Demo">
</p>

## Disclaimer - DANGER !

This package can pose a serious security issue if used incorrectly, as anybody will be able to take control of any user's account. Please ensure that the service provider is only
registered when the app is in a debug/local environment.

By default, the package will disable itself on any domains that don't have a TLD of .dev or .local. This is a security measure to reduce the risk of accidentally enabling the
package in production.

By using this package, you agree that Octopy ID and the contributors of this package cannot be held responsible for any damages caused by using this package.

## Security

If you discover any security related issues, please email [supianidz@gmail.com](mailto:supianidz@gmail.com) or [me@octopy.id](mailto:me@octopy.id) instead of using the issue
tracker.

## Credits

- [Supian M](https://github.com/SupianIDz)
- [Octopy ID](https://github.com/OctopyID)
- [sudo-su](https://github.com/viacreative/sudo-su)

## License

The MIT License (MIT). Please see [License File](https://github.com/SupianIDz/LaraPersonate/blob/master/LICENSE) for more information.

## To Do

- [ ] Unit tests
- [x] UI improvement
- [ ] Showing users with their role based on 3rd library like Laratrust, Bouncer and others.
    - [x] [Laratrust](https://github.com/santigarcor/laratrust)
    - [ ] [Bouncer](https://github.com/JosephSilber/bouncer)
    - [ ] [Permission](https://github.com/spatie/laravel-permission)

## Change Logs

### v1.2.4

- Update Requirement

### v1.2.3

- Add Badges.
- Allows Impersonate to be active in production.

### v1.2.2

- Exclude Lara Personate from json response.

### v1.2.1

- Exclude Lara Personate from ajax requests.

### v1.2.0

- Rewrite code.
- Added sign out button.
- Added a new feature to display users by the role according to third party packages.

### v1.1.0

- Add custom fields for user_model.
- Rename package, from laravel-sudo to laravel-impersonate.

### v1.0.0

- Initial Release.
