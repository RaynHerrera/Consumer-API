<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePiDataElementsTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extension_pidataelement', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pi_date_element',255);
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
        Schema::dropIfExists('extension_pidataelement');
    }
}
