<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanRepaymentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayment_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('loan_id')->unsigned()->nullable();
            $table->date('paid_by_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('due_date');
            $table->integer('installment')->nullable();
            $table->decimal('principal', 65, 6)->default(0.00);
            $table->decimal('principal_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('principal_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('interest', 65, 6)->default(0.00);
            $table->decimal('interest_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('interest_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('interest_waived_derived', 65, 6)->default(0.00);
            $table->decimal('fees', 65, 6)->default(0.00);
            $table->decimal('fees_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('fees_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('fees_waived_derived', 65, 6)->default(0.00);
            $table->decimal('penalties', 65, 6)->default(0.00);
            $table->decimal('penalties_repaid_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_written_off_derived', 65, 6)->default(0.00);
            $table->decimal('penalties_waived_derived', 65, 6)->default(0.00);
            $table->decimal('total_due', 65, 6)->default(0.00);
            $table->string('month')->nullable();
            $table->string('year')->nullable();
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
        Schema::dropIfExists('loan_repayment_schedules');
    }
}
