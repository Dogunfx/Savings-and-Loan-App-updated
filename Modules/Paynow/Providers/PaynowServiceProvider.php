<?php

namespace Modules\Paynow\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class PaynowServiceProvider extends ServiceProvider
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
       // $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Paynow', 'Database/Migrations'));
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
            module_path('Paynow', 'Config/config.php') => config_path('paynow.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Paynow', 'Config/config.php'), 'paynow'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/paynow');

        $sourcePath = module_path('Paynow', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/paynow';
        }, \Config::get('view.paths')), [$sourcePath]), 'paynow');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/paynow');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'paynow');
        } else {
            $this->loadTranslationsFrom(module_path('Paynow', 'Resources/lang'), 'paynow');
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
            app(Factory::class)->load(module_path('Paynow', 'Database/factories'));
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
