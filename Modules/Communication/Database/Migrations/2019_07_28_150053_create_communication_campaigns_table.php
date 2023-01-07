<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunicationCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communication_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('sms_gateway_id')->unsigned()->nullable();
            $table->bigInteger('communication_campaign_business_rule_id')->unsigned()->nullable();
            $table->bigInteger('communication_campaign_attachment_type_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('loan_officer_id')->unsigned()->nullable();
            $table->bigInteger('loan_product_id')->unsigned()->nullable();
            $table->text('subject')->nullable();
            $table->text('name')->nullable();
            $table->enum('campaign_type', ['sms', 'email'])->default('email');
            $table->enum('trigger_type', ['direct', 'schedule', 'triggered'])->default('direct');
            $table->date('scheduled_date')->nullable();
            $table->string('scheduled_time')->nullable();
            $table->integer('schedule_frequency')->nullable();
            $table->enum('schedule_frequency_type', ['days', 'weeks', 'months', 'years'])->default('days');
            $table->date('scheduled_next_run_date')->nullable();
            $table->date('scheduled_last_run_date')->nullable();
            $table->integer('from_x')->nullable();
            $table->integer('to_y')->nullable();
            $table->integer('cycle_x')->nullable();
            $table->integer('cycle_y')->nullable();
            $table->integer('overdue_x')->nullable();
            $table->integer('overdue_y')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->enum('status', ['pending', 'active', 'inactive', 'closed','done'])->default('pending');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('communication_campaigns');
    }
}
