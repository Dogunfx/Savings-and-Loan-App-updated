<?php


namespace Modules\Savings\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Modules\Savings\Events\SavingsStatusChanged' => [
            'Modules\Savings\Listeners\SavingsStatusChangedCampaigns',
        ],
        'Modules\Savings\Events\TransactionUpdated' => [
            'Modules\Savings\Listeners\UpdateTransactions',
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