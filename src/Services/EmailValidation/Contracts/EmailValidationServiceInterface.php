<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts;

    use GuzzleHttp\Client;

    interface EmailValidationServiceInterface
    {
        public function initializeClient(): Client;

        public function isRealEmail(string $email): bool;

        public function isDisposable(mixed $response): bool;

        public function getServiceName(): string;
    }
