<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use GuzzleHttp\Client;
    use GuzzleHttp\Utils;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationBase;

    class IsTempMailService extends EmailValidationBase
    {
        public function initializeClient(): Client
        {
            if (isset($this->client)) {
                return $this->client;
            }

            $this->client = new Client([
              'base_uri' => rtrim($this->baseUrl, '/\\') . '/',
            ]);

            return $this->client;
        }

        public function isRealEmail(string $email): bool
        {
            try {
                $response = $this->initializeClient()->get($this->apiKey . '/' .$email);

                $responseBody = Utils::jsonDecode($response->getBody()->getContents());

                dump($responseBody);

                return ! $this->isDisposable($responseBody);
            } catch (RequestException | GuzzleException $e) {
                dump($e);
                report($e);
                return true; // Assume true if there's an error
            }
        }

        public function isDisposable(mixed $response): bool
        {
            return $response->blocked ?? false;
        }

    }
