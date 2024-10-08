<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Email verifier
    |--------------------------------------------------------------------------
    |
    | This option controls the default verifier that is used to verify any email
    |
    | Supported: "kickbox", "usercheck", "mails", "block-temporary-email", "zerobounce"
    |            "verifyright", "mailboxvalidator", "emaillistverify"
    */

    'default' => env('INDISPOSABLE_SERVICE', 'kickbox'),


    /*
    |--------------------------------------------------------------------------
    | Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable email verifier
    |
    */

    'enabled' => env('INDISPOSABLE_ENABLED', true),


    /*
    |--------------------------------------------------------------------------
    | Validation type
    |--------------------------------------------------------------------------
    |
    | This option may be used switch validation from email to domain.
    | Supported: "domain", "email"
    | Defaults: email
    */

    'type' => env('INDISPOSABLE_TYPE', 'email'),


    /*
    |--------------------------------------------------------------------------
    | API key
    |--------------------------------------------------------------------------
    |
    | It is better to set it on `connections.provider.key` key of the configuration file
    |
    */

    'api_key' => env('INDISPOSABLE_KEY', ''),


    /*
    |--------------------------------------------------------------------------
    | JSON Source URLs
    |--------------------------------------------------------------------------
    |
    | The source URLs yielding a list of disposable email domains. Change these
    | to whatever source you like. Just make sure they all return a JSON array.
    |
    */

    'source' => 'https://cdn.jsdelivr.net/gh/disposable/disposable-email-domains@master/domains.json',

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The location where the retrieved domains list should be stored locally.
    | The path should be accessible and writable by the web server. A good
    | place for storing the list is in the framework's own storage path.
    |
    */

    'storage' => storage_path('framework/disposable_domains.json'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define whether the disposable domains list should be cached.
    | If you disable caching or when the cache is empty, the list will be
    | fetched from local storage instead.
    |
    | You can optionally specify an alternate cache connection or modify the
    | cache key as desired.
    |
    */

    'cache' => [
        'enabled' => env('INDISPOSABLE_CACHE', true),
        'store' => 'default',
        'key' => '_disposable_email_domains_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all the email verifiers used by your application plus
    | their respective settings. Several examples have been configured for
    | you, and you are free to add your own as your application requires.
    |
    | You will specify which one you are using for your
    | email verifier below. You are free to add additional mailers as required.
    |
    | Supported: "kickbox", "usercheck", "mails", "block-temporary-email", "zerobounce"
    |            "verifyright", "mailboxvalidator", "emaillistverify"
    |
    */

    'connections' => [
        'kickbox' => [
            'domain' => 'https://open.kickbox.io/v1/disposable/',
            'email' => 'https://api.eu.kickbox.com/v2/verify',
            'key' => env('INDISPOSABLE_KEY'),
        ],

        'usercheck' => [
            'domain' => 'https://api.usercheck.com/domain',
            'email' => 'https://api.usercheck.com/email',
            'key' => env('INDISPOSABLE_KEY'),
        ],

        'mails' => [
            'domain' => 'https://api.mails.so/v1/validate',
            'email' => 'https://api.mails.so/v1/validate',
            'key' => env('INDISPOSABLE_KEY'),
        ],

        'block-temporary-email' => [
            'domain' => 'https://block-temporary-email.com/check/domain',
            'email' => 'https://block-temporary-email.com/check/email',
            'key' => env('INDISPOSABLE_KEY'),
        ],

        'zerobounce' => [
            'domain' => 'https://api.zerobounce.net/v2/validate',
            'email' => 'https://api.zerobounce.net/v2/validate',
            'key' => env('INDISPOSABLE_KEY'),
        ],

        'verifyright' => [
            'domain' => 'https://verifyright.co/verify/',
            'email' => 'https://verifyright.co/verify/',
            'key' => env('INDISPOSABLE_KEY'),
        ],

    ],

];
