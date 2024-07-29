<?php

use App\Traits\ColumnMigrationFields;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthenticationUsersTable extends Migration
{
    use ColumnMigrationFields;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authentication_users', function (Blueprint $table) {
            $table->increments('id');
           $table->string('password',128);
           $table->timestamp('last_login')->nullable();
           $table->boolean('is_superuser');
           $table->string('username',255);
           $table->string('first_name',255);
           $table->string('last_name',255);
           $table->boolean('is_staff');
           $table->timestamp('date_joined')->useCurrent();
           $table->string('email',255);
           $table->string('country_id',255);
           $table->integer('email_code')->nullable();
           $table->integer('forgot_password_code')->nullable();
           $table->integer('role_id')->nullable();
           $table->string('elroi_id',100);
           $table->boolean('is_2fa_active')->default(false);
           $table->boolean('is_verified')->default(false);
           $table->string('logo',100)->nullable();
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
        Schema::dropIfExists('authentication_users');
    }
}
