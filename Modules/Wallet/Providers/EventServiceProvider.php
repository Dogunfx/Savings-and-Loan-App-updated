<?php


namespace Modules\Wallet\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Modules\Wallet\Events\TransactionUpdated' => [
            'Modules\Wallet\Listeners\UpdateTransactions',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }

    public function shouldDiscoverEvents()
    {
        return true;
    }
}