<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation;

    use Illuminate\Support\Carbon;
    use Illuminate\Validation\Validator;
    use Illuminate\Contracts\Cache\Repository as Cache;
    use ErlandMuchasaj\LaravelEmailVerify\Support\Validator as EmailValidator;
    use ErlandMuchasaj\LaravelEmailVerify\Exceptions\CredentialsNotFoundException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Concerns\Disposable;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;
    use InvalidArgumentException;

    class Indisposable
    {
        use Disposable;

        protected bool $enabled;
        protected EmailValidationServiceInterface $service;

        /**
         * The cache repository.
         *
         * @var Cache|null
         */
        protected ?Cache $cache;

        /**
         * @throws CredentialsNotFoundException
         */
        public function __construct(?Cache $cache = null)
        {
            $this->cache = $cache;
            $this->enabled = config('laravel-email-verify.enabled');
            $this->service = EmailValidationServiceFactory::create(config('laravel-email-verify.default'));
        }

        public function validate(string $attribute, mixed $value, array $parameters, Validator $validator): bool
        {
            if (! $this->enabled) {
                return true;
            }

            return $this->isRealEmail($value);
        }

        public function isRealEmail(string $email): bool
        {
            if ($this->isEmailAddressValid($email) === false) {
                throw new InvalidArgumentException("Invalid email address: `{$email}`");
            }

            // first we check if email is in disposable list
            if ($this->inDisposableEmailList($email)) {
                return false;
            }

            // if cache is not enabled, we validate against service
            if ($this->cache === null) {
                return $this->service->isRealEmail($email);
            }

            // then we validate against service to see if they are valid and deliverable
            return $this->cache->remember($this->cacheKey($email), $this->ttl(), fn () => $this->service->isRealEmail($email));
        }

        public function cacheKey(string $value): string
        {
            return $this->service->getServiceName().'_disposable_email_'.$value.'|email';
        }

        public function ttl(): Carbon
        {
            return now()->addMinutes(60);
        }

        /**
         * Validates the email address.
         *
         * @return bool Returns true if the email address is valid
         */
        final public function isEmailAddressValid(string $emailAddress): bool
        {
            return EmailValidator::isEmailAddressValid($emailAddress);
        }
    }
