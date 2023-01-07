<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('setting_key')->unique();
            $table->string('module')->nullable();
            $table->text('setting_value')->nullable();
            $table->integer('order')->nullable();
            $table->enum('category', ['email', 'sms', 'general', 'system', 'update', 'other']);
            $table->enum('type', ['text', 'textarea', 'number', 'select', 'radio', 'date', 'select_db', 'radio_db', 'select_multiple', 'select_multiple_db','checkbox','checkbox_db','file','info'])->default('text');
            $table->text('options')->nullable();
            $table->text('rules')->nullable();
            $table->text('class')->nullable();
            $table->tinyInteger('required')->default(0);
            $table->string('db_columns')->nullable();
            $table->string('info')->nullable();
            $table->tinyInteger('displayed')->default(1);
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
        Schema::dropIfExists('settings');
    }
}
