<?php

use Laravel\Fortify\Features;

return [

    /*
    |--------------------------------------------------------------------------
    | Fortify Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Fortify will use while
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Fortify Password Broker
    |--------------------------------------------------------------------------
    |
    | Here you may specify which password broker Fortify can use when a user
    | is resetting their password. This configured value should match one
    | of your password brokers setup in your "auth" configuration file.
    |
    */

    'passwords' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Username / Email
    |--------------------------------------------------------------------------
    |
    | This value defines which model attribute should be considered as your
    | application's "username" field. Typically, this might be the email
    | address of the users but you are free to change this value here.
    |
    | Out of the box, Fortify expects forgot password and reset password
    | requests to have a field named 'email'. If the application uses
    | another name for the field you may define it below as needed.
    |
    */

    'username' => 'email',

    'email' => 'email',

    /*
    |--------------------------------------------------------------------------
    | Lowercase Usernames
    |--------------------------------------------------------------------------
    |
    | This value defines whether usernames should be lowercased before saving
    | them in the database, as some database system string fields are case
    | sensitive. You may disable this for your application if necessary.
    |
    */

    'lowercase_usernames' => true,

    /*
    |--------------------------------------------------------------------------
    | Home Path
    |--------------------------------------------------------------------------
    |
    | Here you may configure the path where users will get redirected during
    | authentication or password reset when the operations are successful
    | and the user is authenticated. You are free to change this value.
    |
    */

    'home' => '/home',

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Prefix / Subdomain
    |--------------------------------------------------------------------------
    |
    | Here you may specify which prefix Fortify will assign to all the routes
    | that it registers with the application. If necessary, you may change
    | subdomain under which all of the Fortify routes will be available.
    |
    */

    'prefix' => '',

    'domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Fortify Routes Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middleware Fortify will assign to the routes
    | that it registers with the application. If necessary, you may change
    | these middleware but typically this provided default is preferred.
    |
    */

    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | By default, Fortify will throttle logins to five requests per minute for
    | every email and IP address combination. However, if you would like to
    | specify a custom rate limiter to call then you may specify it here.
    |
    */

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    /*
    |--------------------------------------------------------------------------
    | Register View Routes
    |--------------------------------------------------------------------------
    |
    | Here you may specify if the routes returning views should be disabled as
    | you may not need them when building your own application. This may be
    | especially true if you're writing a custom single-page application.
    |
    */

    'views' => true,

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | Some of the Fortify features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features or you can even remove all of these if you need to.
    |
    */

    'features' => [
        // Features::registration(),
        // Features::resetPasswords(),
        // Features::emailVerification(),
        // Features::updateProfileInformation(),
        // Features::updatePasswords(),

        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => false,
            // 'window' => 0,
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Views Paths
    |--------------------------------------------------------------------------
    |
    | Determines path of view that used in project
    | 
    | 
    |
    */

    'view-paths' => [
        'login' => 'fortune.auth.login',
        'two-factor' => [
            'register' => 'auth.two-factor.register',
            'challenge' => 'auth.two-factor.challenge',
            'recovery-code' => 'auth.two-factor.recovery-code',
        ],
    ],

     /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    |
    | Determines the message that used after an action is called (saved in session flash)
    | 
    | 
    |
    */

    'messages' => [
        'error' => [
            'two-factor' => [
                'register' => '2FA belum terdaftar, silahkan buka email dan ulangi langkah-langkah di atas',
                'challenge' => '2FA Code yang kamu masukkan salah, silahkan periksa kembali google authenticator kamu',
                'recovery-code' => 'Recovery code tidak sesuai, mohon diperiksa kembali',
                'recovery-code_used' => 'Recovery code sudah pernah digunakan, silahkan hubungi tim support untuk lebih lanjutnya',
                'resend-email' => 'Too many attempt, You may try again later',
            ],
        ],
        'success' => [
            'two-factor' => [
                'register' => 'Pendaftaran berhasil',
                'challenge' => null,
                'recovery-code' => null,
                'resend-email' => 'Email berhasil dikirim',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    |
    | Determines custom laravel validation on your own (form request)
    | 
    | 
    |
    */

    'validation' => [
        'login' => [
            'rules' => [
                'email' => 'required|string|email|exists:users,email',
                'password' => 'required|string|min:8',
                'g-recaptcha-response' => 'required|captcha',
            ], 
            'messages' => [
                'email.required' => 'Email Address is required',
                'email.email' => 'Not a valid email address',
                'email.exists' => 'Not a valid email address',
                'password.required' => 'Password is required',
                'password.min' => 'Please input minimum :min characters',
            ],
            'failed' => [
                'password' => "That password doesn't match. Try again?",
            ],
        ],
    ],

     /*
    |--------------------------------------------------------------------------
    | Mail
    |--------------------------------------------------------------------------
    |
    | Determines mail class that will be called
    | 
    | 
    |
    */

    'mail' => [
        'two-factor' => [
            'qr-code' => App\Mail\TwoFactorAuthenticationQRCode::class,
        ]
    ],

     /*
    |--------------------------------------------------------------------------
    | Two Factor Enabled
    |--------------------------------------------------------------------------
    |
    | Determines Two Factor is enabled / disabled
    | 
    | 
    |
    */
    
    'two_factor_enabled' => env('TWO_FACTOR_ENABLED', true),
];
