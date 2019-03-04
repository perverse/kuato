<?php

namespace Kuato\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class KuatoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // @todo not sure how necessary this is... and there may be edge cases where it's actively not wanted?
        if (config('app.env') == 'local'):
            $this->registerGenerators();
        endif;

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Kuato\Exceptions\Handler::class
        );

        $this->registerServices();
        $this->registerMiddleware();
    }

    protected function registerServices()
    {
        $this->app->singleton('Kuato\Contracts\ModuleServiceInterface', function($app) {
            return new \Kuato\Services\ModuleService($app, config('kuato.paths.modules'));
        });

        $this->app->register(\Kuato\Providers\ModuleServiceProvider::class);
        $this->app->singleton('Kuato\Contracts\RelationshipLoaderInterface', 'Kuato\Services\RelationshipLoader');
    }

    protected function registerGenerators()
    {
        $this->app->singleton('Kuato\Contracts\Generators\StubProcessorServiceInterface', 'Kuato\Generators\StubProcessorService');
        $this->app->singleton('Kuato\Contracts\Generators\GeneratorServiceInterface', 'Kuato\Generators\GeneratorService');

        $this->app->singleton('command.kuato.module', 'Kuato\Commands\ModuleMakeCommand');
        $this->app->singleton('command.kuato.dropdb', 'Kuato\Commands\DropDatabaseTables');
    }

    protected function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('kuato.middleware.jsonresponse', 'Kuato\Middleware\ApiResponseJson');
        $this->app['router']->aliasMiddleware('kuato.middleware.downloadresponse', 'Kuato\Middleware\ApiResponseDownload');
        $this->app['router']->aliasMiddleware('kuato.middleware.cors', 'Barryvdh\Cors\HandleCors');
    }

    public function boot()
    {
        if (config('app.env') == 'local'):
            $this->bootGenerators();
        endif;

        $this->bootstrapConfig();
        $this->bootstrapCommands();
        $this->bootstrapViews();
        $this->bootstrapValidationRules();
    }

    protected function bootGenerators()
    {
        $this->commands('command.kuato.module');
        $this->commands('command.kuato.dropdb');
    }

    protected function bootstrapConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/kuato.php' => config_path('kuato.php')
        ]);
    }

    protected function bootstrapCommands()
    {
    }

    protected function bootstrapViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../Views', 'kuato');

        $this->publishes([
            __DIR__ . '/../Views' => base_path('resources/views/vendor/kuato')
        ], 'views');
    }

    protected function bootstrapValidationRules()
    {
        Validator::extend('integerInArray', function($attribute, $value, $parameters) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    if (intval($v) != $v) return false;
                } 
                return true;
            } 

            return is_int($value);
        });

        Validator::extend('alpha_spaces', function($attribute, $value)
        {
            return preg_match('/^[\pL\s]+$/u', $value);
        });

        Validator::extend('yes_or_no', function($attribute, $value)
        {
            return $value === 'yes' || $value === 'no';
        });
    }
}