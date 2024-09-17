<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Concerns;

    use Illuminate\Support\Str;
    use InvalidArgumentException;

    trait Disposable
    {

        /**
         * @param string $email The email address to check whether it is a disposable or temporary email address
         *
         * @return bool Returns true when the provided email address is likely to be a disposable or temporary email address
         *
         * @throws InvalidArgumentException
         */
        public function inDisposableEmailList(string $email): bool
        {
            $emailDomain = Str::of($email)->stripTags()->squish()->trim()->after('@')->lower()->toString();

            // if cache is not enabled, check directly in domain list
            if ($this->cache === null) {
                return \in_array($emailDomain, $this->getDomainsFromFile(), true);
            }

            // then we check from cache
            return $this->cache->remember(
                $this->cacheKey($emailDomain.'_domain_list'),
                $this->ttl(),
                fn () => \in_array($emailDomain, $this->getDomainsFromFile(), true)
            );

        }

        /**
         * @return string[] Returns an array of disposable and temporary email address domains
         *
         * @throws InvalidArgumentException
         */
        private function getDomainsFromFile(): array
        {
            $emailListPath = config('laravel-email-verify.storage');

            if (!\file_exists($emailListPath) || !\is_readable($emailListPath)) {
                throw new InvalidArgumentException('The disposable email domains file is not readable or does not exist: ' . $emailListPath .'. Run `php artisan email-verify:update` to update the list.');
            }

            $fileContents = \file_get_contents($emailListPath);

            if ($fileContents === false) {
                throw new InvalidArgumentException('Invalid domain list file: ' . $emailListPath);
            }

            $jsonData = \json_decode($fileContents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new InvalidArgumentException('Error decoding JSON from the domain list file: ' . json_last_error_msg());
            }

            if (empty($jsonData)) {
                throw new InvalidArgumentException('Invalid or empty domain list in JSON file: ' . $emailListPath);
            }

            return \array_map('mb_strtolower', $jsonData);
        }

    }
