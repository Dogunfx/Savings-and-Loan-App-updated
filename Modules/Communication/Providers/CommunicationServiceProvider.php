<?php

namespace Modules\Communication\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class CommunicationServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('campaigns:process')->everyMinute();
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->commands([
            \Modules\Communication\Console\ProcessScheduledCampaigns::class,
        ]);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('communication.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'communication'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/permissions.php', 'communication.permissions'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/menus.php', 'communication.menus'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/communication');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/communication';
        }, \Config::get('view.paths')), [$sourcePath]), 'communication');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/communication');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'communication');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'communication');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
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
