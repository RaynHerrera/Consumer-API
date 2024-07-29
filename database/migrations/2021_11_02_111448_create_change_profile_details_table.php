<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeProfileDetailsTable extends Migration
{
    use ColumnMigrationFields; 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_profile_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->text('user_name')->nullable();
            $table->text('email_id')->nullable();
            $table->text('change_code');
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
        Schema::dropIfExists('change_profile_details');
    }
}
