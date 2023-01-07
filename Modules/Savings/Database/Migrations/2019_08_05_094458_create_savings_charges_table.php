<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('savings_charge_type_id')->unsigned();
            $table->bigInteger('savings_charge_option_id')->unsigned();
            $table->text('name');
            $table->decimal('amount', 65, 6);
            $table->decimal('min_amount', 65, 6)->nullable();
            $table->decimal('max_amount', 65, 6)->nullable();
            $table->enum('payment_mode', ['regular', 'account_transfer'])->default('regular');
            $table->tinyInteger('schedule')->default(0);
            $table->integer('schedule_frequency')->nullable();
            $table->enum('schedule_frequency_type', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('allow_override')->default(0);
            $table->timestamps();
            $table->foreign('currency_id')->references('id')->on("currencies");
            $table->foreign('savings_charge_type_id')->references('id')->on("savings_charge_types");
            $table->foreign('savings_charge_option_id')->references('id')->on("savings_charge_options");
            $table->foreign('created_by_id')->references('id')->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savings_charges');
    }
}
