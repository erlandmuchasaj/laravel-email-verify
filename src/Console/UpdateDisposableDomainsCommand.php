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
            if (empty($source)) {
                $this->error('Source URLs should be defined and not empty in the configuration file.');
                return Command::FAILURE;
            }

            $this->line('Fetching from source...');

            $fetchService = new FetchService();

            $data = $this->laravel->call([$fetchService, 'handle'], ['url' => $source]);

            $this->info('Saving list to storage!');

            $fetchService->saveToStorage($data, config('laravel-email-verify.storage'));

            $this->info('Disposable domains list updated successfully!');

            Cache::forget(config('laravel-email-verify.cache.key'));

            return Command::SUCCESS;
        }

    }
