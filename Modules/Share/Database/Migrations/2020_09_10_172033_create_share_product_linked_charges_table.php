<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShareProductLinkedChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('share_product_linked_charges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('share_product_id')->unsigned();
            $table->bigInteger('share_charge_id')->unsigned();
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
        Schema::dropIfExists('share_product_linked_charges');
    }
}
