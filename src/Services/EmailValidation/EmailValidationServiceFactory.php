<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation;

    use ErlandMuchasaj\LaravelEmailVerify\Exceptions\CredentialsNotFoundException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\BlockTemporaryEmailService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\IsTempMailService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\KickboxService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\MailsService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\UserCheckService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\VerifyRightService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\ZeroBounceService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;
    use InvalidArgumentException;

    class EmailValidationServiceFactory
    {
        /**
         * @throws CredentialsNotFoundException
         */
        public static function create(string $service): EmailValidationServiceInterface
        {
            $connections = config('laravel-email-verify.connections');

            // check that service exists in connection
            // Fallback to kickbox if the service is not found
            if (! \array_key_exists($service, $connections)) {
                throw new InvalidArgumentException("Invalid email validation service: $service");
            }

            $baseUrl = $connections[$service]['email'];
            $apiKey = $connections[$service]['key'];

            // fetch the api key from the config - which allows the config to be cached
            // throw exception if the email verify credentials are missing from the env
            if (blank($apiKey)) {
                // throw the custom exception defined below
                throw new CredentialsNotFoundException('Please provide a INDISPOSABLE_KEY in your .env file.');
            }

            return match ($service) {
                'mails' => new MailsService($baseUrl, $apiKey),
                'kickbox' => new KickboxService($baseUrl, $apiKey),
                'user-check' => new UserCheckService($baseUrl, $apiKey),
                'zero-bounce' => new ZeroBounceService($baseUrl, $apiKey),
                'verify-right' => new VerifyRightService($baseUrl, $apiKey),
                'block-temporary-email' => new BlockTemporaryEmailService($baseUrl, $apiKey),
                'is-temp-mail' => new IsTempMailService($baseUrl, $apiKey),
                // Add other services
                default => throw new InvalidArgumentException("Invalid email validation service: $service"),
            };
        }
    }
