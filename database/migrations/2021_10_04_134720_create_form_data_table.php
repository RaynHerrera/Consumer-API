<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormDataTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_formdata', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url',1000);
            $table->integer('company');
            $table->integer('data_element');
            $table->integer('user');
            $table->string('value',1000);
            $table->integer('occurrences');
            $table->string('hash_value',1000);
            $this->AddCommonFields($table);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extension_formdata');
    }
}
