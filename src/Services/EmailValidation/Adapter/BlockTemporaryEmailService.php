<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Adapter;

    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Concerns\Disposable;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\GuzzleException;
    use GuzzleHttp\Exception\RequestException;
    use GuzzleHttp\Utils;
    use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Contracts\EmailValidationServiceInterface;
    use stdClass;

    class BlockTemporaryEmailService implements EmailValidationServiceInterface
    {
        use Disposable;

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
                'base_uri' => rtrim((string) $this->baseUrl, '/\\') . '/',
                // 'verify' => false,
                'headers' => [
                    'X-Api-Key' => $this->apiKey,
                    'Content-Type' => 'application/json; charset=utf-8',
                    'Accept' => 'application/json'
                ],
            ];

            $this->client = new Client($options);

            return $this->client;
        }

        public function isRealEmail(string $email): bool
        {
            // first we check if email is in disposable list
            if ($this->inDisposableEmailList($email)) {
                return false;
            }

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
            $res = $this->formatResponse($response);

            // Return true if the email address' domain has a valid MX entry in DNS
            return match (true) {
                $res->status == 400, $res->disposable == true, $res->mx == false => true,
                default => false,
            };
        }

        public function getServiceName(): string
        {
            return 'block-temporary-email';
        }

        private function formatResponse(mixed $data): stdClass
        {
            $object = new stdClass();
            $object->status = optional($data)->status ?? 400;
            $object->mx = optional($data)->dns ?? false;
            $object->disposable = optional($data)->temporary ?? false;

            return $object;
        }
    }
