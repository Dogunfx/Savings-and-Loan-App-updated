<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanLinkedCreditChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_linked_credit_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('loan_credit_check_id')->unsigned();
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
        Schema::dropIfExists('loan_linked_credit_checks');
    }
}
