<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanCreditChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_credit_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->text('name');
            $table->text('translated_name')->nullable();
            $table->enum('security_level', ['block', 'pass', 'warning'])->default('warning');
            $table->enum('rating_type', ['boolean', 'score'])->default('boolean');
            $table->decimal('pass_min_amount', 65, 6)->nullable();
            $table->decimal('pass_max_amount', 65, 6)->nullable();
            $table->decimal('warn_min_amount', 65, 6)->nullable();
            $table->decimal('warn_max_amount', 65, 6)->nullable();
            $table->decimal('fail_min_amount', 65, 6)->nullable();
            $table->decimal('fail_max_amount', 65, 6)->nullable();
            $table->text('general_error_msg')->nullable();
            $table->text('user_friendly_error_msg')->nullable();
            $table->text('general_warning_msg')->nullable();
            $table->text('user_friendly_warning_msg')->nullable();
            $table->text('general_success_msg')->nullable();
            $table->text('user_friendly_success_msg')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->timestamps();
            $table->foreign('created_by_id')->references('id')->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_credit_checks');
    }
}
