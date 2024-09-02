<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Concerns;

    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Str;
    use InvalidArgumentException;

    trait Disposable
    {
        /**
         * It is located in root folder of the package
         * @var string - Path to the file containing disposable and temporary email address domains
         */
        private string $emailListPath = __DIR__ . '/../../../../disposable_email_domains.txt';

        /**
         * @var int The duration in seconds to cache the disposable email domains list - default 30 days
         */
        private int $cacheDuration = 60 * 60 * 24 * 30; // Cache for 30 days

        /**
         * @param string $email The email address to check whether it is a disposable or temporary email address
         *
         * @return bool Returns true when the provided email address is likely to be a disposable or temporary email address
         *
         * @throws InvalidArgumentException
         */
        public function inDisposableEmailList(string $email): bool
        {
            if (! \filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid email provided: $email");
            }

            $emailDomain = Str::of($email)->stripTags()->squish()->trim()->after('@')->lower()->toString();

            return \in_array($emailDomain, $this->getDomainsFromFile(), true);
        }

        /**
         * @return string[] Returns an array of disposable and temporary email address domains
         *
         * @throws InvalidArgumentException
         */
        private function getDomainsFromFile(): array
        {
            return Cache::remember('disposable_email_domains', $this->cacheDuration, function () {
                if (!\file_exists($this->emailListPath) || !\is_readable($this->emailListPath)) {
                    throw new InvalidArgumentException('Invalid domain list file: ' . $this->emailListPath);
                }

                $fileContents = \file($this->emailListPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                if (empty($fileContents)) {
                    throw new InvalidArgumentException('Invalid domain list file: ' . $this->emailListPath);
                }

                return \array_map('mb_strtolower', $fileContents);
            });
        }

    }
