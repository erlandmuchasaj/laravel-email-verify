<?php

    namespace ErlandMuchasaj\LaravelEmailVerify\Console;

    use ErlandMuchasaj\LaravelEmailVerify\Services\FetchService;
    use Illuminate\Console\Command;
    use Illuminate\Support\Facades\Cache;

    class UpdateDisposableDomainsCommand extends Command
    {
        protected $signature = 'email-verify:update-disposable-domains';
        protected $description = 'Update the disposable domains list';

        public function handle(): int
        {
            $source = config('laravel-email-verify.source');
            $storage = config('laravel-email-verify.storage');

            if (blank($source)) {
                $this->error(__('Source URLs should be defined and not empty in the configuration file.'));
                return Command::FAILURE;
            }

            $this->line(__('Fetching from source: :url.', ['url' => $source]));

            $fetchService = new FetchService();

            $data = $this->laravel->call([$fetchService, 'handle'], ['url' => $source]);

            $this->info(__('Saving list to storage: :storage.', ['storage' => $storage]));

            $fetchService->saveToStorage($data, $storage);

            $this->info(__('Disposable domains list updated successfully!'));

            Cache::forget(config('laravel-email-verify.cache.key'));

            return Command::SUCCESS;
        }

    }
