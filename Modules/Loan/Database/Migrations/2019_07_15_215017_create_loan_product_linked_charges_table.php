<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProductLinkedChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_product_linked_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_product_id')->unsigned();
            $table->bigInteger('loan_charge_id')->unsigned();
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
        Schema::dropIfExists('loan_product_linked_charges');
    }
}
