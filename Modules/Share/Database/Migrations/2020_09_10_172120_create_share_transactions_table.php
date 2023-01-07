<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('share_id')->unsigned();
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('share_linked_charge_id')->unsigned()->nullable();
            $table->bigInteger('payment_detail_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('total_shares', 65, 6)->nullable();
            $table->decimal('charge_amount', 65, 6)->nullable();
            $table->decimal('credit', 65, 6)->nullable();
            $table->decimal('debit', 65, 6)->nullable();
            $table->decimal('balance', 65, 6)->nullable();
            $table->bigInteger('share_transaction_type_id')->unsigned();
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
            $table->index('share_id');
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
        Schema::dropIfExists('share_transactions');
    }
}
