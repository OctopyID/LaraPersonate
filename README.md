# Octopy Sudo
Allow a developer direct login as another user during development inspired by [sudo-su](https://github.com/viacreative/sudo-su).

## Installation
To install the package, simply follow the steps below.

Install the package using Composer:

```
$ composer require octopyid/sudo --dev

$ php artisan vendor:publish
```

## Configuration
After running `vendor:publish`, a config file called sudo.php should appear in your project.

## Disclaimer - DANGER!
This package can pose a serious security issue if used incorrectly, as anybody will be able to take control of any user's account. Please ensure that the service provider is only registered when the app is in a debug/local environment.

By default, the package will disable itself on any domains that don't have a TLD of .dev or .local. This is a security measure to reduce the risk of accidentally enabling the package in production.

By using this package, you agree that Octopy ID and the contributors of this package cannot be held responsible for any damages caused by using this package.

## To Do
- [ ] Unit Test
- [ ] UI Improvement
