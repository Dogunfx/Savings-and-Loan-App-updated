<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('share_officer_id')->unsigned()->nullable();
            $table->bigInteger('share_product_id')->unsigned()->nullable();
            $table->bigInteger('savings_id')->unsigned()->nullable();
            $table->enum('client_type', array(
                'client',
                'group',
            ))->default('client')->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->string('account_number')->nullable();
            $table->string('external_id')->nullable();
            $table->integer('decimals')->nullable();
            $table->integer('total_shares')->nullable();
            $table->decimal('nominal_price', 65, 6)->nullable();
            $table->integer('minimum_active_period')->nullable();
            $table->enum('minimum_active_period_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ))->default('days')->nullable();
            $table->integer('lockin_period')->default(0);
            $table->enum('lockin_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ))->default('days');
            $table->tinyInteger('allow_dividends_for_inactive_clients')->default(0);
            $table->date('submitted_on_date')->nullable();
            $table->date('application_date')->nullable();
            $table->bigInteger('submitted_by_user_id')->unsigned()->nullable();
            $table->date('approved_on_date')->nullable();
            $table->bigInteger('approved_by_user_id')->unsigned()->nullable();
            $table->text('approved_notes')->nullable();
            $table->date('activated_on_date')->nullable();
            $table->bigInteger('activated_by_user_id')->unsigned()->nullable();
            $table->text('activated_notes')->nullable();
            $table->date('rejected_on_date')->nullable();
            $table->bigInteger('rejected_by_user_id')->unsigned()->nullable();
            $table->text('rejected_notes')->nullable();
            $table->date('dormant_on_date')->nullable();
            $table->bigInteger('dormant_by_user_id')->unsigned()->nullable();
            $table->text('dormant_notes')->nullable();
            $table->date('closed_on_date')->nullable();
            $table->bigInteger('closed_by_user_id')->unsigned()->nullable();
            $table->text('closed_notes')->nullable();
            $table->date('inactive_on_date')->nullable();
            $table->bigInteger('inactive_by_user_id')->unsigned()->nullable();
            $table->text('inactive_notes')->nullable();
            $table->date('withdrawn_on_date')->nullable();
            $table->bigInteger('withdrawn_by_user_id')->unsigned()->nullable();
            $table->text('withdrawn_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'active', 'withdrawn', 'rejected', 'closed', 'inactive', 'dormant', 'submitted'])->default('submitted');
            $table->timestamps();
            $table->index('client_id');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shares');
    }
}
