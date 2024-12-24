<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use GuzzleHttp\Utils;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationBase;

    class MailsService extends EmailValidationBase
    {
        public function initializeClient(): Client
        {
            if (isset($this->client)) {
                return $this->client;
            }

            $options = [
                'base_uri' => rtrim($this->baseUrl, '/\\'),
                // 'verify' => false,
                'headers' => [
                    'X-Mails-Api-Key' => $this->apiKey,
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
                    'email' => $email
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
            if ($response->data?->result === 'undeliverable') {
                return true;
            }

            if ($response->data?->result === 'risky' && $response->data?->is_disposable) {
                return true;
            }

            return (bool) $response->data?->is_disposable;
        }

    }
