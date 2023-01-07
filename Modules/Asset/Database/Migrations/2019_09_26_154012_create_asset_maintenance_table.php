<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetMaintenanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_maintenance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('asset_maintenance_type_id')->unsigned()->nullable();
            $table->bigInteger('asset_id')->unsigned()->nullable();
            $table->text('performed_by')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 65, 2)->nullable();
            $table->decimal('mileage', 65, 2)->nullable();
            $table->tinyInteger('record_expense')->default(0);
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
        Schema::dropIfExists('asset_maintenance');
    }
}
