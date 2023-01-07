<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned();
            $table->bigInteger('loan_id')->unsigned()->nullable();
            $table->bigInteger('loan_product_id')->unsigned();
            $table->decimal('amount', 65, 4)->default(0.00);
            $table->enum('status', array(
                'approved',
                'pending',
                'rejected'
            ))->default('pending');
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('loan_applications');
    }
}
