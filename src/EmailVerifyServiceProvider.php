<?php

namespace ErlandMuchasaj\LaravelEmailVerify;

use ErlandMuchasaj\LaravelEmailVerify\Console\UpdateDisposableDomainsCommand;
use ErlandMuchasaj\LaravelEmailVerify\Services\EmailValidation\Indisposable;
use Illuminate\Support\ServiceProvider;

class EmailVerifyServiceProvider extends ServiceProvider
{

    /**
     * $abstract Package name
     * @var string
     */
    public static string $abstract = 'laravel-email-verify';

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/' . static::$abstract . '.php',
            static::$abstract
        );
    }

    /**
     * Bootstrap the application services.
     * In general is a good practice that in providers not to use helpers
     * but to use Laravel app container to access the data such as config, cache, etc.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/' . static::$abstract . '.php' => config_path(static::$abstract . '.php'),
            ], static::$abstract);

            $this->commands(UpdateDisposableDomainsCommand::class);
        }


        // load translations
        $path = __DIR__.'/../lang';
     
        // ex: __('package::file.key');
        $this->loadTranslationsFrom($path, static::$abstract);

        // ex: __('Normal Text');
        $this->loadJsonTranslationsFrom($path);

        // register the custom validation rule after app is booted.
        $this->app->booted(function($app) {

            // get validator and translator
            $validator = $app['validator'];
            $translator = $app['translator'];

            // extend the validator with the custom email_verify rule
            $validator->extend('email_verify', function ($attribute, $value, $parameters, $validator) use ($app) {

                $isEnabled = $app['config'][static::$abstract . '.enabled'];

                if (! $isEnabled) {
                    return true;
                }

                // Only build and pass the requested cache store if caching is enabled.
                if ($app['config'][static::$abstract . '.cache.enabled']) {
                    $store = $app['config'][static::$abstract . '.cache.store'];
                    $cache = $app['cache']->store($store == 'default' ? $app['config']['cache.default'] : $store);
                }

                $indisposable = new Indisposable($cache ?? null);

                return $indisposable->validate($attribute, $value, $parameters, $validator);
            });

            // Add a replacer that gets the translation message at runtime
            $validator->replacer('email_verify', function ($message, $attribute, $rule, $parameters) use ($app) {
                $translatedMessage = $app['translator']->get(static::$abstract.'::validation.email_verify');

                // Get the custom attribute name if defined in validation.php attributes array
                $customAttributes = $app['translator']->get('validation.attributes');
                $attributeName = $customAttributes[$attribute] ?? $attribute;

                // Replace the attribute placeholder with the translated attribute name
                return str_replace(':attribute', $attributeName, $translatedMessage);
            });
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [static::$abstract];
    }
}
