<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts;

    use GuzzleHttp\Client;

    abstract class EmailValidationBase implements EmailValidationServiceInterface
    {
        protected string $baseUrl;
        protected string $apiKey;
        protected Client $client;

        public function __construct(string $baseUrl, string $apiKey)
        {
            $this->baseUrl = $baseUrl;
            $this->apiKey = $apiKey;
        }

        public function getServiceName(): string
        {
            return str(static::class)
                ->afterLast('\\')
                ->replace('Service', '')
                ->kebab()
                ->value();
        }

    }
