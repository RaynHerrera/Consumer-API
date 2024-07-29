<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokenBlackListBlackListedTokensTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_blacklist_blacklistedtoken', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('blacklisted_at')->useCurrent();
            $table->integer('token_id');
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
        Schema::dropIfExists('token_blacklist_blacklistedtoken');
    }
}
