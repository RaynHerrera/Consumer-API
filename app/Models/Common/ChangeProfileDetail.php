<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeProfileDetail extends Model
{
    use HasFactory;

    protected $table = "change_profile_details";

    protected $fillable = [
       'user_id','email_id','user_name','change_code'
    ];

    
}
