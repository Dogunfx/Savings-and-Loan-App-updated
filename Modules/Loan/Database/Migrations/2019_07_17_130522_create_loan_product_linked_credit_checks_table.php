<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProductLinkedCreditChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_product_linked_credit_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_product_id')->unsigned();
            $table->bigInteger('loan_credit_check_id')->unsigned();
            $table->integer('check_order')->nullable();
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
        Schema::dropIfExists('loan_product_linked_credit_checks');
    }
}
