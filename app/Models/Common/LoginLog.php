<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $table = "login_logs";

    protected $fillable = [
       'access_tokens_id','user_id','login_time','logout_time','created_by','updated_by'
    ];
}
