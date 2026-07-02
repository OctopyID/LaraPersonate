# Changelog

All notable changes to **LaraPersonate** will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [5.0.0] - 2026-07-03

### ⚠ Breaking Changes

- **Config key renamed:** `impersonate.enabled` → `impersonate.interface.enabled`
- **ENV variable renamed:** `IMPERSONATE_UI_ENABLED` (previously `IMPERSONATE_ENABLED`) — all interface-related env keys are now prefixed with `IMPERSONATE_UI_`
- **ENV variable renamed:** `IMPERSONATE_UI_WIDTH` (previously `IMPERSONATE_WIDTH`)
- **ENV variable renamed:** `IMPERSONATE_UI_DEBOUNCE` (previously `IMPERSONATE_DELAY`)
- Requires **PHP 8.2+** and **Laravel 12.x / 13.x**
- **`ImpersonateCollection`** no longer performs in-memory PHP filtering — database-level scoping via `scopeImpersonatable` is now the supported approach

### Added

- **`HasImpersonation::getImpersonateDisplayText()`** — default display text method returning `$this->name` so the trait is plug-and-play out of the box
- **`HasImpersonation::getImpersonateSearchField()`** — default search field returning `['name']` so basic search works without any configuration
- **`HasImpersonation::scopeImpersonatable()`** — placeholder scope that can be overridden to apply database-level filtering for both correctness and performance
- **`Impersonate::loginAs($user)`** — new fluent shortcut for manual backend impersonation without needing to pass the impersonator explicitly
- **`#[Scope]` attribute support** — `scopeImpersonatable` now works with both traditional `scopeMethod()` convention and the modern `#[Scope]` attribute available in Laravel 11+
- **Authorization guards on API endpoints** — `GET /_impersonate/users` and `POST /_impersonate/login` now return `403 Forbidden` when accessed by users without impersonation
  permission, preventing information disclosure
- **Config section `interface`** — all UI-related settings are now grouped under `impersonate.interface.*` for clarity
- **ENV reference table** in README covering all available `IMPERSONATE_UI_*` keys with their defaults and descriptions

### Changed

- **Zero Dependencies (Vanilla JS & CSS)** — completely dropped heavy frontend dependencies (`jQuery`, `Select2`) and the entire Node.js build ecosystem (Webpack, `package.json`, `.nvmrc`). The frontend is now 100% Vanilla JS and Vanilla CSS!

- **UI layout refactored** — impersonation widget `<table>` replaced with Flexbox layout for cleaner, more maintainable HTML
- **`Repository::find()`** now wraps the query in a `try/catch` block, returning an `ImpersonateException` instead of leaking internal system exceptions to API consumers
- **`Repository::get()`** applies `scopeImpersonatable` at the database query level (before pagination), fixing pagination count accuracy when filtering impersonatable users

### Fixed

- Pagination returning incorrect totals when `canBeImpersonated()` filter was applied in PHP memory after the query executed
- Information disclosure vulnerability: unauthenticated or unauthorized users could previously enumerate users via the `/_impersonate/users` endpoint
- `is_object()` and `method_exists()` calls that were always evaluating to `true` due to known model types

### Security

- **[HIGH]** Added `abort_unless($this->impersonate->authorized(), 403)` to both `ImpersonateController@index` and `ImpersonateController@login` — closes a user enumeration and
  unauthorized-access vulnerability on the impersonate API endpoints

---

> **Upgrade Guide**
>
> 1. Update `.env`: rename `IMPERSONATE_ENABLED` → `IMPERSONATE_UI_ENABLED`, `IMPERSONATE_WIDTH` → `IMPERSONATE_UI_WIDTH`, `IMPERSONATE_DELAY` → `IMPERSONATE_UI_DEBOUNCE`
> 2. Update config references: `impersonate.enabled` → `impersonate.interface.enabled`
> 3. Add `scopeImpersonatable()` to your User model (or the `#[Scope]` attribute equivalent) for correct pagination and faster search — see README for examples
