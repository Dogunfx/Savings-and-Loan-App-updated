<?php

namespace Modules\Savings\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class SavingsServiceProvider extends ServiceProvider
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
            $schedule->command('savings:calculate_interest')->daily();
            $schedule->command('savings:process_interest')->daily();
        });
        app('arrilot.widget-namespaces')->registerNamespace('Savings', '\Modules\Savings\Widgets');
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
            \Modules\Savings\Console\CalculateInterest::class,
            \Modules\Savings\Console\ProcessInterest::class,
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
            __DIR__.'/../Config/config.php' => config_path('savings.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'savings'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/widgets.php', 'savings.widgets'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/permissions.php', 'savings.permissions'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/menus.php', 'savings.menus'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/savings');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/savings';
        }, \Config::get('view.paths')), [$sourcePath]), 'savings');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/savings');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'savings');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'savings');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
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
