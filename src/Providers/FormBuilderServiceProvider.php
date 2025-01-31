<?php

declare(strict_types=1);

namespace Zk\FormBuilder\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class FormBuilderServiceProvider extends ServiceProvider
{
    private const BASE_PATH = __DIR__ . '/..';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        // Register package config.
        $this->registerConfig();

        // Load views.
        $this->loadViewsFrom(self::BASE_PATH . '/Resources/views', 'formbuilder');

        // Load Blade components.
        Blade::anonymousComponentPath(self::BASE_PATH . '/Resources/views/components', 'formbuilder');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Illuminate\Contracts\Validation\Validator', function ($app, $args) {
            return app()->makeWith('Illuminate\Validation\Validator', ['data' => [], 'rules' => []]);
        });
    }

    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        if ($this->app->runningInConsole()) {
            // Publish config file.
            $this->publishes([
                self::BASE_PATH . '/Config/formbuilder.php' => config_path('formbuilder.php')
            ], ['zk-formbuilder', 'zk-formbuilder-config']);

            // Publish JS and CSS files
            $this->publishes([
                self::BASE_PATH . '/Resources/assets/js/formbuilder.min.js' => public_path('js/formbuilder.min.js'),
                self::BASE_PATH . '/Resources/assets/js/formbuilder.js' => public_path('js/formbuilder.js'),
                self::BASE_PATH . '/Resources/assets/js/validator.min.js' => public_path('js/validator.min.js'),
                self::BASE_PATH . '/Resources/assets/js/validator.js' => public_path('js/validator.js'),
            ], ['zk-formbuilder', 'zk-formbuilder-assets']);
        }
    }
}
