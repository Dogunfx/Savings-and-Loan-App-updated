<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollItemsMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_items_meta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('payroll_id')->unsigned()->nullable();
            $table->bigInteger('payroll_item_id')->unsigned()->nullable();
            $table->decimal('percentage', 65, 2)->nullable();
            $table->string('name')->nullable();
            $table->enum('type', array('allowance', 'deduction'));
            $table->enum('amount_type', array('fixed', 'percentage'));
            $table->decimal('amount', 65, 2);
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
        Schema::dropIfExists('payroll_items_meta');
    }
}
