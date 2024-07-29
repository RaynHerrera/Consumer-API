<?php

namespace App\Models\Import;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Excelupload extends Model
{
    use HasFactory;

    protected $table = "exceluploads";

    protected $fillable = [
      'file_name','original_name','status','uploaded_record','notuploaded_record','created_at','created_by','emailsent_at','updated_at'
    ];

    public function user(){
        return $this->hasOne(User::class,'id','created_by');
    }
}
