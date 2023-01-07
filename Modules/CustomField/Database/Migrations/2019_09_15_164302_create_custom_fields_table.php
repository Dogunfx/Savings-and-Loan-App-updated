<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->string('category')->nullable();
            $table->string('type');
            $table->text('name')->nullable();
            $table->text('label')->nullable();
            $table->text('options')->nullable();
            $table->text('class')->nullable();
            $table->text('db_columns')->nullable();
            $table->text('rules')->nullable();
            $table->text('default_values')->nullable();
            $table->tinyInteger('required')->default(0);
            $table->tinyInteger('active')->default(1);
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
        Schema::dropIfExists('custom_fields');
    }
}
