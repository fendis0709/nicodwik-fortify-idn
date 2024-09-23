<p align="center"><img src="/art/logo.svg" alt="Logo Laravel Fortify"></p>

<p align="center">
    <a href="https://github.com/laravel/fortify/actions">
        <img src="https://github.com/laravel/fortify/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravel/fortify">
        <img src="https://img.shields.io/packagist/dt/laravel/fortify" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/fortify">
        <img src="https://img.shields.io/packagist/v/laravel/fortify" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/fortify">
        <img src="https://img.shields.io/packagist/l/laravel/fortify" alt="License">
    </a>
</p>

## Introduction

Laravel Fortify is a frontend agnostic authentication backend for Laravel. Fortify powers the registration, authentication, and two-factor authentication features of [Laravel Jetstream](https://github.com/laravel/jetstream).

## Official Documentation

Documentation for Fortify can be found on the [Fortify Laravel website](https://laravel.com/docs/fortify).

## Installation

1. Install using command `composer require nicodwik/fortify`
2. After successfull installation, run `php artisan fortify:install`
3. Some files will be generated automatically
4. Add this code in `config/app.php`, inside `providers` array
```php
'providers' => ServiceProvider::defaultProviders()->merge([
  // Any other providers
  App\Providers\FortifyServiceProvider::class, // Add this provider
])->toArray();
```
5. Add this code below in `app/Http/Kernel.php`, inside `protected $middlewareAliases`
```php
protected $middlewareAliases = [
  // Any other middleware aliases
  'last_login' => \App\Http\Middleware\CheckLastLoginMiddleware::class,  // Add this middleware alias
];
```

## Things To Do

Check config `fortify.php`
-  `view-paths`
  <br> Determines path of view that used in project
  
- `messages`
  <br> Determines the message that used after an action is called (saved in session flash)

- `validation`
  <br> Determines custom laravel validation on your own (form request)
  
- `mail`
  <br> Determines mail class that will be called

- `two_factor_enabled`
  <br> Determines two factor is enabled / disabled

## Note

#### Route

List route you can use
```bash
## 2FA register page
two-factor.register (GET)
two-factor.verify (GET)
two-factor.resend-email (POST)
two-factor.proceed (POST)

## 2FA input page
two-factor.login (GET)
two-factor.challenge (POST)
```

#### Published file

If you run `php artisan fortify:install`, these files will be generated :

```bash
## Config
config/fortify.php

## Mail
app/Mail/TwoFactorAuthenticationQRCode.php
resources/views/email/twofactor-qrcode.blade.php

## 2FA page
resources/views/auth/two-factor/register.blade.php
resources/views/auth/two-factor/challenge.blade.php
resources/views/auth/two-factor/recovery-code.blade.php

## Migrations
database/migrations/2014_10_12_200000_add_two_factor_columns_to_users_table.php

## Listener (for last login feature)
app/Listeners/SetLastLoginSession.php

## Middleware (for last login feature)
app/Http/Middleware/CheckLastLoginMiddleware.php
```
