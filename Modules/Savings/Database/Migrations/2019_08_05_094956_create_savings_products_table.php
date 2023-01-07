<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('savings_reference_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('overdraft_portfolio_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('savings_control_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('interest_on_savings_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('write_off_savings_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_fees_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_penalties_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_interest_overdraft_chart_of_account_id')->unsigned()->nullable();
            $table->text('name');
            $table->text('short_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('decimals')->nullable();
            $table->enum('savings_category', array(
                'voluntary',
                'compulsory',
            ));
            $table->tinyInteger('auto_create')->default(0);
            $table->decimal('minimum_interest_rate', 65, 6);
            $table->decimal('default_interest_rate', 65, 6);
            $table->decimal('maximum_interest_rate', 65, 6);
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
            $table->decimal('automatic_opening_balance', 65, 6)->default(0.00);
            $table->decimal('minimum_balance_for_interest_calculation', 65, 6)->default(0.00);
            $table->integer('lockin_period')->default(0);
            $table->enum('lockin_type', array(
                'days',
                'weeks',
                'months',
                'years',
            ))->default('days');
            $table->decimal('minimum_balance', 65, 6)->default(0.00);
            $table->tinyInteger('allow_overdraft')->default(0);
            $table->decimal('overdraft_limit', 65, 6)->nullable();
            $table->decimal('overdraft_interest_rate', 65, 6)->nullable();
            $table->decimal('minimum_overdraft_for_interest', 65, 6)->nullable();
            $table->enum('days_in_year', ['actual', '360', '365', '364'])->default('365')->nullable();
            $table->enum('days_in_month', ['actual', '30', '31'])->default('30')->nullable();
            $table->enum('accounting_rule', ['none', 'cash'])->default('none')->nullable();
            $table->tinyInteger('active')->default(0);
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
        Schema::dropIfExists('savings_products');
    }
}
