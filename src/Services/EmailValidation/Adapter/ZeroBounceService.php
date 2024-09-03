<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Utils;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;

    class ZeroBounceService implements EmailValidationServiceInterface
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
                'base_uri' => rtrim($this->baseUrl, '/\\'),
                // 'verify' => false,
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ],
            ];
            
            $this->client = new Client($options);

            return $this->client;
        }

        public function isRealEmail(string $email): bool
        {
            try {
                $response = $this->initializeClient()->get('', ['query' => [
                    'email' => $email,
                    'api_key' => $this->apiKey,
                    'ip_address' =>  request()->ip(),
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
            if (
                ! empty($response->status) &&
                ($response->status === 'do_not_mail' || $response->status === 'invalid')
            ) {
                return true;
            }

            // if ($response->status === 'valid' && $response->smtp_valid === true) {
            //     // Proceed with user registration
            // } else {
            //     // Do not proceed with user registration
            // }

            return false;
        }

        public function getServiceName(): string
        {
            return 'zerobounce';
        }
    }
