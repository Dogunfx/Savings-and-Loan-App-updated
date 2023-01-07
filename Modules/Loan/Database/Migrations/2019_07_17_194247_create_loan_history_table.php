<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('created_by_id')->unsigned();
            $table->text('action')->nullable();
            $table->text('user')->nullable();
            $table->timestamps();

            $table->index('loan_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_history');
    }
}
