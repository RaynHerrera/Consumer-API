<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDataTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_data_types', function (Blueprint $table) {
            $table->id();
            $table->text('general')->nullable();
            $table->text('record')->nullable();
            $table->text('commercial')->nullable();
            $table->text('inferences')->nullable();
            $table->text('user_id')->nullable();
            $table->text('company_types_1')->nullable();
            $table->text('company_types_2')->nullable();
            $table->text('company_types_3')->nullable();
            $table->text('company_types_4')->nullable();
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
        Schema::dropIfExists('user_data_types');
    }
}
