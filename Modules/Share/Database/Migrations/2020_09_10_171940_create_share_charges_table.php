<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('created_by_id')->unsigned()->nullable();
            $table->bigInteger('currency_id')->unsigned();
            $table->bigInteger('share_charge_type_id')->unsigned();
            $table->bigInteger('share_charge_option_id')->unsigned();
            $table->text('name');
            $table->decimal('amount', 65, 6);
            $table->decimal('min_amount', 65, 6)->nullable();
            $table->decimal('max_amount', 65, 6)->nullable();
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('allow_override')->default(0);
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
        Schema::dropIfExists('share_charges');
    }
}
