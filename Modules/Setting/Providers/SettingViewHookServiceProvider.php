<?php

namespace Modules\Setting\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Facades\Module;

class SettingViewHookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $links = [];
        $modules = Module::collections();
        foreach ($modules as $module) {
            if (file_exists(Module::getPath($module) . '/' . $module . '/Settings/SettingsLinks.php')) {
                $class = "\\Modules\\" . $module . "\\Settings\\SettingsLinks";
                $class = new $class($links);
                array_push($links, $class->get_links());

            }
        }
        View::composer('setting::setting.organisation', function ($view) use ($links) {
            $html = '';
            foreach ($links as $link) {
                foreach ($link as $key=>$value){
                    $html .= '<a href="' . url($key) . '" class="list-group-item">' . $value . '</a>';
                }
            }
            $view->with('links', $html);
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
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
