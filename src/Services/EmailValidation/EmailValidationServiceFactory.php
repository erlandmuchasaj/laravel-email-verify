<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation;

    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\BlockTemporaryEmailService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\KickboxService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\MailsService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\UsercheckService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\VerifyRightService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter\ZeroBounceService;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;
    use InvalidArgumentException;

    class EmailValidationServiceFactory
    {
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

            return match ($service) {
                'mails' => new MailsService($baseUrl, $apiKey),
                'kickbox' => new KickboxService($baseUrl, $apiKey),
                'usercheck' => new UsercheckService($baseUrl, $apiKey),
                'zerobounce' => new ZeroBounceService($baseUrl, $apiKey),
                'verifyright' => new VerifyRightService($baseUrl, $apiKey),
                'block-temporary-email' => new BlockTemporaryEmailService($baseUrl, $apiKey),
                // Add other services
                default => throw new InvalidArgumentException("Invalid email validation service: $service"),
            };
        }
    }
