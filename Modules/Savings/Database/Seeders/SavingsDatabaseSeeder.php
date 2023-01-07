<?php

namespace Modules\Savings\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SavingsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(SavingsChargeTypesTableSeederTableSeeder::class);
        $this->call(SavingsChargeOptionsTableSeederTableSeeder::class);
        $this->call(SavingsTransactionTypesTableSeederTableSeeder::class);
        $this->call(SettingTableSeeder::class);
    }
}
