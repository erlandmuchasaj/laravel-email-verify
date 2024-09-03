# Laravel Email Verify

Add a simple email verification to your Laravel application.
It detects if the email is disposable  (temporary/throwaway/fake) email addresses.
This tool also helps to avoid communication errors and blocks spam addresses.

## Installation

You can install the package via composer:

```bash
composer require erlandmuchasaj/laravel-email-verify
```

## Config file
Publish the configuration file using artisan.

```bash
php artisan vendor:publish --provider="ErlandMuchasaj\LaravelEmailVerify\EmailVerifyServiceProvider"
```

Now you have access to the `laravel-email-verify.php` configuration file in the `config` directory. Here you can 
 configure which service to use for email verification. Defaults to 'kickbox'.

The only thing you need to pay attention to is the  `connections` key where you need to set the token for the service you are using.

```php
'connections' => [
        'kickbox' => [
            'domain' => 'https://open.kickbox.io/v1/disposable/',
            'email' => 'https://api.eu.kickbox.com/v2/verify',
            'key' => 'your-kickbox-api-key',
        ],
        
        //

    ],
```
You can also change the default service to use for email verification by changing the `default` key.

```php
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
```


## Usage
Use validation rule `email_verify` to check that specific field does not contain a disposable email address.

> [!NOTE]
> ❗ Place it after the `email` validator to ensure that only valid emails are processed.

Example:

```php
// Using validation rule name:
'email_field' => 'required|email|email_verify',
```

---

## Support me

I invest a lot of time and resources into creating [best in class open source packages](https://github.com/erlandmuchasaj?tab=repositories).

If you found this package helpful you can show support by clicking on the following button below and donating some amount to help me work on these projects frequently.

<a href="https://www.buymeacoffee.com/erland" target="_blank">
    <img src="https://www.buymeacoffee.com/assets/img/guidelines/download-assets-2.svg" style="height: 45px; border-radius: 12px" alt="buy me a coffee"/>
</a>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please see [SECURITY](SECURITY.md) for details.

## Credits

- [Erland Muchasaj](https://github.com/erlandmuchasaj)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
