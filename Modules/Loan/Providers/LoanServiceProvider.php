<?php

namespace Modules\Loan\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class LoanServiceProvider extends ServiceProvider
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
            $schedule->command('loan_penalties:process')->daily();
        });
        app('arrilot.widget-namespaces')->registerNamespace('Loan', '\Modules\Loan\Widgets');

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
            \Modules\Loan\Console\ProcessPenalties::class,
            \Modules\Loan\Console\FixSchedules::class,
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
            __DIR__.'/../Config/config.php' => config_path('loan.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'loan'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/widgets.php', 'loan.widgets'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/permissions.php', 'loan.permissions'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../Config/menus.php', 'loan.menus'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/loan');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/loan';
        }, \Config::get('view.paths')), [$sourcePath]), 'loan');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/loan');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'loan');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'loan');
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
