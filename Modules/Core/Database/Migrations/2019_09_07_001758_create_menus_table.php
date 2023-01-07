<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->string('parent_slug')->nullable();
            $table->tinyInteger('is_parent')->default(0);
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->integer('menu_order')->nullable();
            $table->text('url')->nullable();
            $table->text('permissions')->nullable();
            $table->string('icon')->nullable();
            $table->string('module')->nullable();
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
        Schema::dropIfExists('menus');
    }
}
