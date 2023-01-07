<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationCampaignLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_campaign_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('sms_gateway_id')->nullable();
            $table->bigInteger('communication_campaign_id')->nullable();
            $table->enum('campaign_type', ['sms', 'email']);
            $table->text('description')->nullable();
            $table->text('send_to')->nullable();
            $table->text('campaign_name')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed'])->default('pending');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communication_campaign_logs');
    }
}
