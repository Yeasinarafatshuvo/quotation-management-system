<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number');
            $table->integer('product_id')->length(10)->unsigned();
            $table->string('product_price');
            $table->integer('quotation_type')->length(1)->comment('0 for dealer and 1 for corporate price');
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('quotation_subject')->nullable();
            $table->integer('created_user')->unsigned()->nullable();
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
        Schema::dropIfExists('quotations');
    }
}
