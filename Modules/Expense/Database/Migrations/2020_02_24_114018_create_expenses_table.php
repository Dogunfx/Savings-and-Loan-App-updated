<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->unsignedBigInteger('expense_chart_of_account_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('asset_chart_of_account_id')->nullable();
            $table->decimal('amount',65,2)->default(0.00);
            $table->date('date')->nullable();
            $table->tinyInteger('recurring')->default(0);
            $table->string('recur_frequency')->default(31);
            $table->date('recur_start_date')->nullable();
            $table->date('recur_end_date')->nullable();
            $table->date('recur_next_date')->nullable();
            $table->enum('recur_type',
                array('day', 'week', 'month', 'year'))->default('month');
            $table->text('notes')->nullable();
            $table->text('description')->nullable();
            $table->text('files')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
