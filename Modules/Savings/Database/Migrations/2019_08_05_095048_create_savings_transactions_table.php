<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSavingsTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('savings_id')->unsigned();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('savings_linked_charge_id')->unsigned()->nullable();
            $table->bigInteger('payment_detail_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('credit', 65, 6)->nullable();
            $table->decimal('debit', 65, 6)->nullable();
            $table->decimal('balance', 65, 6)->nullable();
            $table->bigInteger('savings_transaction_type_id')->unsigned();
            $table->tinyInteger('reversed')->default(0);
            $table->tinyInteger('reversible')->default(0);
            $table->date('submitted_on')->nullable();
            $table->date('created_on')->nullable();
            $table->text('description')->nullable();
            $table->string('reference')->nullable();
            $table->string('gateway_id')->nullable();
            $table->text('payment_gateway_data')->nullable();
            $table->tinyInteger('online_transaction')->default(0);
            $table->enum('status',['pending','approved','declined'])->default('pending');
            $table->timestamps();
            $table->index('savings_id');
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('savings_transactions');
    }
}
