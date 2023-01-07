<?php

namespace Modules\Expense\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ExpenseServiceProvider extends ServiceProvider
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
        $this->loadMigrationsFrom(module_path('Expense', 'Database/Migrations'));
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
            module_path('Expense', 'Config/config.php') => config_path('expense.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Expense', 'Config/config.php'), 'expense'
        );
        $this->mergeConfigFrom(
            module_path('Expense', 'Config/menus.php'), 'expense.menus'
        );
        $this->mergeConfigFrom(
            module_path('Expense', 'Config/permissions.php'), 'expense.permissions'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/expense');

        $sourcePath = module_path('Expense', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/expense';
        }, \Config::get('view.paths')), [$sourcePath]), 'expense');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/expense');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'expense');
        } else {
            $this->loadTranslationsFrom(module_path('Expense', 'Resources/lang'), 'expense');
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
            app(Factory::class)->load(module_path('Expense', 'Database/factories'));
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
