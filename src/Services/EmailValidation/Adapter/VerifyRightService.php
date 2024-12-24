<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use GuzzleHttp\Utils;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationBase;

    class VerifyRightService extends EmailValidationBase
    {
        public function initializeClient(): Client
        {
            if (isset($this->client)) {
                return $this->client;
            }

            $options = [
                'base_uri' => rtrim($this->baseUrl, '/\\') . '/',
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
                $response = $this->initializeClient()->get($email, ['query' => [
                    'token' => $this->apiKey,
                ]]);

                $responseBody = Utils::jsonDecode($response->getBody()->getContents());

                return ! $this->isDisposable($responseBody);
            } catch (RequestException | GuzzleException $e) {
                if  ($e->hasResponse()) {
                    $resultError = Utils::jsonDecode($e->getResponse()->getBody()->getContents());

                    report("VerifyRightService: {$resultError->error->message}");

                    return $resultError->status;
                }

                report($e);
                return true; // Assume true if there's an error
            }
        }

        public function isDisposable(mixed $response): bool
        {
            // status `true` mains email is not disposable.
            return ! $response->status;
        }

    }
