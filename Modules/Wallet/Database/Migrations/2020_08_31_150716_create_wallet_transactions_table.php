<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('wallet_id');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('payment_detail_id')->nullable();
            $table->enum('transaction_type',['deposit','withdrawal','savings_transfer','loan_transfer'])->default('deposit');
            $table->string('name')->nullable();
            $table->decimal('amount', 65, 6);
            $table->decimal('credit', 65, 6)->nullable();
            $table->decimal('debit', 65, 6)->nullable();
            $table->decimal('balance', 65, 6)->nullable();
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
            $table->index('wallet_id');
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
        Schema::dropIfExists('wallet_transactions');
    }
}
