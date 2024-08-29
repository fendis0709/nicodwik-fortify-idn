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

Documentation for Fortify can be found on the [Laravel website](https://laravel.com/docs/fortify).

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
list route you can use
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
if you run `php artisan fortify:install`, these files will be generated :

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
```
