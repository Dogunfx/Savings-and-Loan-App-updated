<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('asset_type_id')->unsigned()->nullable();
            $table->bigInteger('branch_id')->unsigned()->nullable();
            $table->string('name')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 65, 2)->nullable();
            $table->decimal('replacement_value', 65, 2)->nullable();
            $table->decimal('value', 65, 2)->nullable();
            $table->integer('life_span')->nullable();
            $table->decimal('salvage_value', 65, 2)->nullable();
            $table->text('serial_number')->nullable();
            $table->string('bought_from')->nullable();
            $table->string('purchase_year')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ["active", "inactive", "sold", "damaged", "written_off"])->nullable();
            $table->tinyInteger('active')->default(0)->nullable();
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
        Schema::dropIfExists('assets');
    }
}
