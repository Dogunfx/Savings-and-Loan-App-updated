<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_gateways', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->text('name')->nullable();
            $table->text('to_name')->nullable();
            $table->text('url')->nullable();
            $table->text('msg_name')->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('is_current')->default(0);
            $table->tinyInteger('http_api')->default(1);
            $table->text('class_name')->nullable();
            $table->text('key_one')->nullable();
            $table->text('key_one_description')->nullable();
            $table->text('key_two')->nullable();
            $table->text('key_two_description')->nullable();
            $table->text('key_three')->nullable();
            $table->text('key_three_description')->nullable();
            $table->text('key_four')->nullable();
            $table->text('key_four_description')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('sms_gateways');
    }
}
