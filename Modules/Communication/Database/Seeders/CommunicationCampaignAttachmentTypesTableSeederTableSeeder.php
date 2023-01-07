<?php

namespace Modules\Communication\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommunicationCampaignAttachmentTypesTableSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('communication_campaign_attachment_types')->insert([
            [
                'name' => 'Loan Schedule',
                'active' => 1,
            ],
            [
                'name' => 'Client Statement',
                'active' => 1,
            ],
            [
                'name' => 'Saving Mini Statement',
                'active' => 0,
            ],
        ]);
    }
}
