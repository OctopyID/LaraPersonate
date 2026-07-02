# LaraPersonate v5.0 - Implementation Plan

This document outlines the complete plan for refactoring `octopyid/laravel-impersonate` for the `v5.0` major release.

### 1. Environment & Dependencies Update
- [ ] **Target Branch**: `main`
- [ ] **Drop Laravel 10 Support**: Update `composer.json` to require `illuminate/contracts: ^11.0|^12.0|^13.0`.
- [ ] **PHP Version**: Bump minimum requirement to `php: ^8.2` (required for Pest v3).
- [ ] **Test Dependencies**: Update `orchestra/testbench` to `*`, `nunomaduro/collision` to `*`, and add `pestphp/pest: *` and `pestphp/pest-plugin-laravel: *`.

### 2. API Refactor (Authorization Logic)
Replace the legacy `Authorization` closure-based approach with idiomatic Laravel model methods.
- [ ] Delete `src/Authorization.php`.
- [ ] Modify `src/Concerns/HasImpersonation.php`:
  - [ ] Remove `setImpersonateAuthorization()`.
  - [ ] Add `public function canImpersonate(): bool { return false; }`.
  - [ ] Add `public function canBeImpersonated(): bool { return false; }`.
- [ ] Update `src/Impersonate.php` validation logic to use these new methods.

### 3. Testing Migration (PHPUnit -> Pest PHP)
- [ ] Delete `phpunit.xml` and replace with a standard Pest-compatible `phpunit.xml`.
- [ ] Initialize Pest (`./vendor/bin/pest --init`).
- [ ] Rewrite all tests in `tests/Unit/` and `tests/Feature/` into concise Pest format:
  - [ ] `ImpersonateRepositoryTest.php`
  - [ ] `ImpersonateTest.php`
  - [ ] `ImpersonateAuthorizationTest.php`
  - [ ] `ImpersonateInterfaceTest.php`
  - [ ] `ImpersonateControllerTest.php`
- [ ] Clean up `tests/TestCase.php`.

### 4. Zero Dependency Frontend (No jQuery, No Webpack)
- [ ] Delete `package.json`, `yarn.lock`, `webpack.mix.js`, and `node_modules/`.
- [ ] Delete `resources/asset/octopy.scss` and `octopy.js`.
- [ ] Create `public/octopy.css` using raw, modern CSS variables and flexbox/grid.
- [ ] Create `public/octopy.js` using vanilla ES6 JavaScript.
- [ ] **Custom Dropdown**: Build a lightweight, custom autocomplete dropdown in Vanilla JS to replace Select2. This handles AJAX fetching of users (`/_impersonate/users`) without any external dependencies.
- [ ] Update `resources/views/impersonate.blade.php` to accommodate the new DOM structure for the custom dropdown.

### 5. Strict Static Analysis & Formatting (Larastan Level 9 + Pint)
- [ ] Add `"larastan/larastan": "*"` to `composer.json` `require-dev` to always use the latest version.
- [ ] Create `phpstan.neon` with `level: 9` and include Larastan extension.
- [ ] Refactor source code (`src/`) to eliminate all `mixed` types, ensure generic arrays (`array<int, string>`), and enforce strict type hints and DocBlocks.
- [ ] Add `"laravel/pint": "*"` to `composer.json` `require-dev` to always use the latest version.
- [ ] Apply code formatting across the entire codebase using the newly created `pint.json` configuration file (`./vendor/bin/pint`).

### 6. Verification
- [ ] Run `composer update` to apply dependency changes.
- [ ] Run `./vendor/bin/phpstan analyse` and ensure 0 errors.
- [ ] Run `./vendor/bin/pint --test` to ensure formatting passes.
- [ ] Run `./vendor/bin/pest` and ensure 100% passing tests.
- [ ] Test manually in the `demo` project to ensure the UI behaves beautifully and smoothly.
