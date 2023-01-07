<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetDepreciationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_depreciation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('asset_id')->unsigned()->nullable();
            $table->string('year')->nullable();
            $table->decimal('beginning_value', 65, 2)->nullable();
            $table->decimal('depreciation_value', 65, 2)->nullable();
            $table->decimal('rate', 65, 2)->nullable();
            $table->decimal('cost', 65, 2)->nullable();
            $table->decimal('accumulated', 65, 2)->nullable();
            $table->decimal('ending_value', 65, 2)->nullable();
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
        Schema::dropIfExists('asset_depreciation');
    }
}
