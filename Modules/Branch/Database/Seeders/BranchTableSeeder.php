<?php

namespace Modules\Branch\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Branch\Entities\Branch;

class BranchTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $branch = new Branch();
        $branch->create([
            'name' => 'Default',
            'is_system' => 1,
            'open_date' => date("Y-m-d")
        ]);

    }
}
