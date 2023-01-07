<?php

namespace Modules\Loan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LoanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(LoanChargeOptionsTableSeeder::class);
        $this->call(LoanChargeTypesTableSeeder::class);
        $this->call(LoanCreditChecksTableSeeder::class);
        $this->call(LoanTransactionProcessingStrategiesTableSeeder::class);
        $this->call(LoanTransactionTypesTableSeeder::class);
        $this->call(SettingTableSeeder::class);
    }
}
