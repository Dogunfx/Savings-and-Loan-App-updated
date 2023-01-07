<?php

namespace Modules\Upgrade\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class UpgradeServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('Upgrade', 'Database/Migrations'));
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
            module_path('Upgrade', 'Config/config.php') => config_path('upgrade.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Upgrade', 'Config/config.php'), 'upgrade'
        );
        $this->mergeConfigFrom(
            module_path('Upgrade', 'Config/menus.php'), 'upgrade.menus'
        );
        $this->mergeConfigFrom(
            module_path('Upgrade', 'Config/permissions.php'), 'upgrade.permissions'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/upgrade');

        $sourcePath = module_path('Upgrade', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/upgrade';
        }, \Config::get('view.paths')), [$sourcePath]), 'upgrade');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/upgrade');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'upgrade');
        } else {
            $this->loadTranslationsFrom(module_path('Upgrade', 'Resources/lang'), 'upgrade');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(module_path('Upgrade', 'Database/factories'));
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
