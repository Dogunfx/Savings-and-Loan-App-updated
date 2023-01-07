<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->decimal('work_duration', 65, 2)->default(0.00);
            $table->string('duration_unit')->nullable();
            $table->decimal('amount_per_duration', 65, 2)->default(0.00);
            $table->decimal('total_duration_amount', 65, 2)->default(0.00);
            $table->text('picture')->nullable();
            $table->text('description')->nullable();
            $table->text('items')->nullable();
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
        Schema::dropIfExists('payroll_templates');
    }
}
