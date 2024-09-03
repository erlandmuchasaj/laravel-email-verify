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

    'api_key' => env('INDISPOSABLE_KEY', 'live_106a7e8821e255c35b7969fc8ac557b455e6c88c896b468e2287f8f716b3472c'),


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
        'enabled' => true,
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
            'key' => 'live_106a7e8821e255c35b7969fc8ac557b455e6c88c896b468e2287f8f716b3472c',
        ],

        'usercheck' => [
            'domain' => 'https://api.usercheck.com/domain',
            'email' => 'https://api.usercheck.com/email',
            'key' => 'bEFiqX0MKQvORHX2jBwnCa1tNRhFlfvY',
        ],

        'mails' => [
            'domain' => 'https://api.mails.so/v1/validate',
            'email' => 'https://api.mails.so/v1/validate',
            'key' => '66815588-7953-4d58-9020-1dbbe545c008',
        ],

        'block-temporary-email' => [
            'domain' => 'https://block-temporary-email.com/check/domain',
            'email' => 'https://block-temporary-email.com/check/email',
            'key' => 'HFL4ZzPGlYaxJimbPlERn1MA067ENAvx8KhH50CQ',
        ],

        'zerobounce' => [
            'domain' => 'https://api.zerobounce.net/v2/validate',
            'email' => 'https://api.zerobounce.net/v2/validate',
            'key' => '7dc5df71a3784a7cb1954e775975b71e',
        ],

        'verifyright' => [
            'domain' => 'https://verifyright.co/verify/',
            'email' => 'https://verifyright.co/verify/',
            'key' => '92928b756e623357b3bd80e8dc90deaeadec5c3d500dd8999bd683405ffc562de1e0fb9f887c363007206ac32b640f24',
        ],

//            'mailboxvalidator' => [
//                'domain' => 'https://api.mailboxvalidator.com/v2/validation/single',
//                'email' => 'https://api.mailboxvalidator.com/v2/validation/single',
//                'key' => '7dc5df71a3784a7cb1954e775975b71e',
//            ],
//
//            'emaillistverify' => [
//                'domain' => 'https://apps.emaillistverify.com/api/verifyEmail',
//                'email' => 'https://apps.emaillistverify.com/api/verifyEmail',
//                'key' => '7dc5df71a3784a7cb1954e775975b71e',
//            ],
    ],

];
