<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('loan_charge_type_id')->unsigned();
            $table->bigInteger('loan_charge_option_id')->unsigned();
            $table->text('name');
            $table->decimal('amount', 65, 6);
            $table->decimal('min_amount', 65, 6)->nullable();
            $table->decimal('max_amount', 65, 6)->nullable();
            $table->enum('payment_mode', ['regular', 'account_transfer'])->default('regular');
            $table->tinyInteger('schedule')->default(0)->nullable();
            $table->integer('schedule_frequency')->nullable();
            $table->enum('schedule_frequency_type', ['days', 'weeks', 'months', 'years'])->nullable();
            $table->tinyInteger('is_penalty')->default(0)->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('allow_override')->default(0);
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
        Schema::dropIfExists('loan_charges');
    }
}
