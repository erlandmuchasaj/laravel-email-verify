<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use GuzzleHttp\Client;
    use GuzzleHttp\Utils;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;

    class KickboxService implements EmailValidationServiceInterface
    {
        protected string $baseUrl;
        protected string $apiKey;
        protected Client $client;

        public function __construct(string $baseUrl, string $apiKey)
        {
            $this->baseUrl = $baseUrl;
            $this->apiKey = $apiKey;
        }

        public function initializeClient(): Client
        {
            if (isset($this->client)) {
                return $this->client;
            }

            $options = [
                'base_uri' => rtrim($this->baseUrl, '/\\') . '/',
                'api_version' => 'v2',
                // 'verify' => false,
                'headers' => [
                    'Authorization' => 'token ' . $this->apiKey,
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json',
                    'User-Agent' => 'laravel-email-verify/1.0.0 (https://github.com/erlandmuchasaj/laravel-email-verify)',
                ],
            ];

            $this->client = new Client($options);

            return $this->client;
        }

        public function isRealEmail(string $email): bool
        {
            // then we validate against service to see if they are valid and deliverable
            try {
                $response = $this->initializeClient()->get('', ['query' => [
                    'email' => $email,
                    'apikey' => $this->apiKey,
                ]]);

                $responseBody = Utils::jsonDecode($response->getBody()->getContents());

                return ! $this->isDisposable($responseBody);
            } catch (RequestException | GuzzleException $e) {
                report($e);
                return true; // Assume true if there's an error
            }
        }

        public function isDisposable(mixed $response): bool
        {
            return $response->result === 'undeliverable' ||
                ($response->result === 'risky' && $response->disposable);
        }

        public function getServiceName(): string
        {
            return 'kickbox';
        }

    }
