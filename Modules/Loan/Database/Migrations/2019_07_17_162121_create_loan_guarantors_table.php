<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_guarantors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->bigInteger('loan_id')->unsigned()->nullable();
            $table->tinyInteger('is_client')->default(0);
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'unspecified'])->default('unspecified')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'deceased', 'unspecified', 'closed'])->default('pending');
            $table->enum('marital_status', ['married', 'single', 'divorced', 'widowed', 'unspecified', 'other'])->default('unspecified')->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->bigInteger('title_id')->unsigned()->nullable();
            $table->bigInteger('profession_id')->unsigned()->nullable();
            $table->bigInteger('client_relationship_id')->unsigned()->nullable();
            $table->string('mobile')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('employer')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->date('created_date')->nullable();
            $table->date('joined_date')->nullable();
            $table->decimal('guaranteed_amount', 65, 6)->nullable();
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
        Schema::dropIfExists('loan_guarantors');
    }
}
