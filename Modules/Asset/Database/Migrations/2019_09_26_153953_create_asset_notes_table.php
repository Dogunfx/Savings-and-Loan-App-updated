<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_notes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('created_by_id')->unsigned()->nullable();
            $table->bigInteger('asset_id')->unsigned()->nullable();
            $table->text('attachment')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('asset_notes');
    }
}
