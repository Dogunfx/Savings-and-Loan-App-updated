<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned()->nullable();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('payroll_template_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('employee_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('description')->nullable();
            $table->text('comments')->nullable();
            $table->decimal('work_duration', 65, 2)->default(0.00);
            $table->string('duration_unit')->nullable();
            $table->decimal('amount_per_duration', 65, 2)->default(0.00);
            $table->decimal('total_duration_amount', 65, 2)->default(0.00);
            $table->decimal('gross_amount', 65, 2)->default(0.00);
            $table->decimal('total_allowances', 65, 2)->default(0.00);
            $table->decimal('total_deductions', 65, 2)->default(0.00);
            $table->date('date')->nullable();
            $table->string('year')->nullable();
            $table->string('month')->nullable();
            $table->tinyInteger('recurring')->default(0)->nullable();
            $table->string('recur_frequency')->default(31)->nullable();
            $table->date('recur_start_date')->nullable();
            $table->date('recur_end_date')->nullable();
            $table->date('recur_next_date')->nullable();
            $table->enum('recur_type', ['day', 'week', 'month', 'year'])->default('month')->nullable();
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
        Schema::dropIfExists('payroll');
    }
}
