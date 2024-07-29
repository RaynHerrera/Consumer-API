<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTokenBlackListOutStandingTokensTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_black_list_out_standing_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->text('token');
            $table->timestamp('expires_at')->useCurrent();
            $table->integer('user_id');
            $table->string('jti');
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
        Schema::dropIfExists('token_black_list_out_standing_tokens');
    }
}
