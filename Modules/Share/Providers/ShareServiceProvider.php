<?php

namespace Modules\Share\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ShareServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('Share', 'Database/Migrations'));
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
            module_path('Share', 'Config/config.php') => config_path('share.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Share', 'Config/config.php'), 'share'
        );
        $this->mergeConfigFrom(
            module_path('Share', 'Config/permissions.php'), 'share.permissions'
        );
        $this->mergeConfigFrom(
            module_path('Share', 'Config/menus.php'), 'share.menus'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/share');

        $sourcePath = module_path('Share', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/share';
        }, \Config::get('view.paths')), [$sourcePath]), 'share');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/share');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'share');
        } else {
            $this->loadTranslationsFrom(module_path('Share', 'Resources/lang'), 'share');
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
            app(Factory::class)->load(module_path('Share', 'Database/factories'));
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
