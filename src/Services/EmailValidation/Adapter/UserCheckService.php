<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use stdClass;
    use GuzzleHttp\Utils;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationBase;

    class UserCheckService extends EmailValidationBase
    {
        public function initializeClient(): Client
        {
            if (isset($this->client)) {
                return $this->client;
            }

            $options = [
                'base_uri' => rtrim((string) $this->baseUrl, '/\\') . '/',
                // 'verify' => false,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
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
                $response = $this->initializeClient()->get($email);

                $responseBody = Utils::jsonDecode($response->getBody()->getContents());

                return ! $this->isDisposable($responseBody);
            } catch (RequestException | GuzzleException $e) {
                report($e);
                return true; // Assume true if there's an error
            }
        }

        public function isDisposable(mixed $response): bool
        {
            $res = $this->formatResponseVP($response);

            // Return true if the email address' domain has a valid MX entry in DNS
            return match (true) {
                $res->status == 400, $res->disposable == true, $res->mx == false => true,
                default => false,
            };
        }

        private function formatResponseVP(mixed $data): stdClass
        {
            $object = new stdClass();
            $object->status = optional($data)->status ?? 400;
            $object->mx = optional($data)->mx ?? false;
            $object->disposable = optional($data)->disposable ?? false;

            return $object;
        }
    }
