<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsLinkedChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_linked_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->bigInteger('savings_id')->unsigned()->nullable();
            $table->bigInteger('savings_charge_id')->unsigned()->nullable();
            $table->bigInteger('savings_charge_type_id')->unsigned()->nullable();
            $table->bigInteger('savings_charge_option_id')->unsigned()->nullable();
            $table->bigInteger('savings_transaction_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('calculated_amount', 65, 6)->nullable();
            $table->decimal('paid_amount', 65, 6)->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('waived')->default(0);
            $table->tinyInteger('is_paid')->default(0);
            $table->date('submitted_on')->nullable();
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
        Schema::dropIfExists('savings_linked_charges');
    }
}
