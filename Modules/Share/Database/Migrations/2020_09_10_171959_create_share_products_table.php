<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('share_reference_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('share_suspense_control_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('equity_chart_of_account_id')->unsigned()->nullable();
            $table->bigInteger('income_from_fees_chart_of_account_id')->unsigned()->nullable();
            $table->text('name');
            $table->text('short_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('decimals')->nullable();
            $table->integer('total_shares')->nullable();
            $table->integer('shares_to_be_issued')->nullable();
            $table->decimal('nominal_price', 65, 6)->nullable();
            $table->decimal('capital_value', 65, 6)->nullable();
            $table->decimal('minimum_shares_per_client', 65, 6)->nullable();
            $table->decimal('default_shares_per_client', 65, 6)->nullable();
            $table->decimal('maximum_shares_per_client', 65, 6)->nullable();
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
        Schema::dropIfExists('share_products');
    }
}
