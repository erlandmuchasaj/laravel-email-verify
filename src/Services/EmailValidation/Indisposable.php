<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation;

    use ErlandMuchasaj\LaravelEmailVerify\Exceptions\CredentialsNotFoundException;
    use Illuminate\Support\Carbon;
    use Illuminate\Validation\Validator;
    use Illuminate\Contracts\Cache\Repository as Cache;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;

    class Indisposable
    {
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

            if ($this->cache === null) {
                return $this->isRealEmail($value);
            }

            return $this->cache->remember($this->cacheKey($value), $this->ttl(), fn () => $this->isRealEmail($value));
        }

        public function isRealEmail(string $email): bool
        {
            return $this->service->isRealEmail($email);
        }

        public function cacheKey(string $value): string
        {
            return $this->service->getServiceName().'_disposable_email_'.$value.'|email';
        }

        public function ttl(): Carbon
        {
            return now()->addMinutes(60);
        }
    }
