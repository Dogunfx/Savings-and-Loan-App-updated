<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientIdentificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_identification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->bigInteger('client_identification_type_id')->unsigned()->nullable();
            $table->string('identification_value')->nullable();
            $table->text('description')->nullable();
            $table->integer('size')->nullable();
            $table->text('link')->nullable();
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
        Schema::dropIfExists('client_identification');
    }
}
