<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('currency_id');

            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->enum('client_type', array(
                'client',
                'group',
            ))->default('client')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'closed', 'suspended', 'rejected', 'approved'])->default('pending')->nullable();
            $table->decimal('balance', 65, 6)->nullable();
            $table->integer('decimals')->default(2);
            $table->text('description')->nullable();
            $table->date('submitted_on_date')->nullable();
            $table->bigInteger('submitted_by_user_id')->unsigned()->nullable();
            $table->date('approved_on_date')->nullable();
            $table->bigInteger('approved_by_user_id')->unsigned()->nullable();
            $table->text('approved_notes')->nullable();
            $table->date('rejected_on_date')->nullable();
            $table->bigInteger('rejected_by_user_id')->unsigned()->nullable();
            $table->text('rejected_notes')->nullable();
            $table->date('closed_on_date')->nullable();
            $table->bigInteger('closed_by_user_id')->unsigned()->nullable();
            $table->text('closed_notes')->nullable();
            $table->date('activated_on_date')->nullable();
            $table->bigInteger('activated_by_user_id')->unsigned()->nullable();
            $table->text('activated_notes')->nullable();
            $table->date('suspended_on_date')->nullable();
            $table->bigInteger('suspended_by_user_id')->unsigned()->nullable();
            $table->text('suspended_notes')->nullable();
            $table->date('inactive_on_date')->nullable();
            $table->bigInteger('inactive_by_user_id')->unsigned()->nullable();
            $table->text('inactive_notes')->nullable();
            $table->timestamps();
            $table->index('client_id');
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
        Schema::dropIfExists('wallets');
    }
}
