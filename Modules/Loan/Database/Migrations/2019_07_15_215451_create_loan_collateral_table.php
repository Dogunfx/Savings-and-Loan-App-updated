<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanCollateralTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_collateral', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('loan_id')->unsigned();
            $table->bigInteger('loan_collateral_type_id')->unsigned();
            $table->text('description')->nullable();
            $table->decimal('value', 65, 6)->nullable();
            $table->text('link')->nullable();
            $table->enum('status', ['active', 'repossessed', 'sold', 'closed'])->default('active');
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
        Schema::dropIfExists('loan_collateral');
    }
}
