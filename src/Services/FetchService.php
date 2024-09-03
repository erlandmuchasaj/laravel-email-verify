<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Services;

    use InvalidArgumentException;
    use UnexpectedValueException;

    class FetchService
    {
        /**
         * Handle fetching and decoding JSON data from a URL.
         *
         * @param string $url The source URL.
         * @return array The decoded JSON data.
         * @throws InvalidArgumentException|UnexpectedValueException
         */
        public function handle(string $url): array
        {
            if (! $url) {
                throw new InvalidArgumentException('Source URL is null');
            }

            $content = @file_get_contents($url);

            if ($content === false) {
                throw new UnexpectedValueException('Failed to interpret the source URL ('.$url.')');
            }

            return $this->decodeJson($content);
        }

        /**
         * Save the array of domains to a storage file as JSON.
         *
         * @param array $domains The domains to save.
         * @param string $path The storage path.
         *
         * @return bool|int The result of file_put_contents.
         * @throws UnexpectedValueException
         */
        public function saveToStorage(array $domains, string $path): bool|int
        {
            $encodedData = json_encode($domains, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($encodedData === false) {
                throw new UnexpectedValueException('Failed to encode domains as JSON.');
            }

            return file_put_contents($path, $encodedData);
        }

        /**
         * Validate and decode a JSON string.
         *
         * @param string $data The JSON string to validate and decode.
         * @return array The decoded JSON data.
         * @throws UnexpectedValueException
         */
        protected function decodeJson(string $data): array
        {
            $jsonData = json_decode($data, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new UnexpectedValueException('JSON decoding error: ' . json_last_error_msg());
            }

            if (empty($jsonData)) {
                throw new UnexpectedValueException('Decoded JSON data is empty or invalid.');
            }

            return $jsonData;
        }
    }
