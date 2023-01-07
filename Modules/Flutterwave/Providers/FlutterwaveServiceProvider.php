<?php

namespace Modules\Flutterwave\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class FlutterwaveServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        //$this->registerFactories();
        $this->loadMigrationsFrom(module_path('Flutterwave', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Flutterwave', 'Config/config.php') => config_path('flutterwave.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Flutterwave', 'Config/config.php'), 'flutterwave'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/flutterwave');

        $sourcePath = module_path('Flutterwave', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/flutterwave';
        }, \Config::get('view.paths')), [$sourcePath]), 'flutterwave');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/flutterwave');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'flutterwave');
        } else {
            $this->loadTranslationsFrom(module_path('Flutterwave', 'Resources/lang'), 'flutterwave');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('Flutterwave', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
