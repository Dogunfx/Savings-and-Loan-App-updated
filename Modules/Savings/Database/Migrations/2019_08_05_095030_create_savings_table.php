<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('savings_officer_id')->unsigned()->nullable();
            $table->bigInteger('savings_product_id')->unsigned()->nullable();
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
            $table->decimal('interest_rate', 65, 6);
            $table->enum('interest_rate_type', array(
                'day',
                'week',
                'month',
                'year',
            ))->default('year');
            $table->enum('compounding_period', array(
                'daily',
                'weekly',
                'monthly',
                'biannual',
                'annually',
            ));
            $table->enum('interest_posting_period_type', array(
                'daily',
                'weekly',
                'monthly',
                'biannual',
                'annually',
            ));
            $table->enum('interest_calculation_type', array(
                'daily_balance',
                'average_daily_balance',
            ))->default('daily_balance');
            $table->decimal('balance_derived', 65, 6)->default(0.00);
            $table->decimal('total_deposits_derived', 65, 6)->default(0.00);
            $table->decimal('total_withdrawals_derived', 65, 6)->default(0.00);
            $table->decimal('total_interest_posted_derived', 65, 6)->default(0.00);
            $table->decimal('minimum_balance_for_interest_calculation', 65, 6)->default(0.00);
            $table->integer('lockin_period')->default(0);
            $table->enum('lockin_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ))->default('days');
            $table->decimal('automatic_opening_balance', 65, 6)->default(0.00);
            $table->tinyInteger('allow_overdraft')->default(0);
            $table->decimal('overdraft_limit', 65, 6)->nullable();
            $table->decimal('overdraft_interest_rate', 65, 6)->nullable();
            $table->decimal('minimum_overdraft_for_interest', 65, 6)->nullable();
            $table->date('submitted_on_date')->nullable();
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
            $table->date('start_interest_posting_date')->nullable();
            $table->date('next_interest_posting_date')->nullable();
            $table->date('start_interest_calculation_date')->nullable();
            $table->date('next_interest_calculation_date')->nullable();
            $table->date('last_interest_calculation_date')->nullable();
            $table->date('last_interest_posting_date')->nullable();
            $table->decimal('calculated_interest', 65, 6)->nullable();
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
        Schema::dropIfExists('savings');
    }
}
