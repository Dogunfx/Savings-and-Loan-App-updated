<?php

namespace Modules\Share\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class ShareDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ShareChargeOptionsTableSeeder::class);
        $this->call(ShareChargeTypesTableSeeder::class);
    }
}
