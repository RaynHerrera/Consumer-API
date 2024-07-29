<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;
   
    protected $table = "login_logs";
   
    protected $fillable = [
        'token_id', 'user_id', 'to', 'cc', 'bcc', 'subject', 'message_body', 'created_at' ,'updated_at'];

    protected $hidden = ['created_at' ,'updated_at'];
  
}
